<?php

namespace App\Services;

use App\Models\User;
use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use Cose\Algorithms;
use Cose\Key\EC2Key;
use Cose\Key\OKPKey;
use Cose\Key\RSAKey;
use Exception;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\Denormalizer;
use Webauthn\Normalizer;

class BiometricService
{
    protected string $rpId;
    protected string $rpName;
    protected string $rpOrigin;

    public function __construct()
    {
        $this->rpId = request()->getHost();
        $this->rpName = config('app.name', 'DayMeter');
        $this->rpOrigin = config('app.url');
    }

    /**
     * Generate options for registration (setup)
     */
    public function getRegistrationOptions(User $user)
    {
        $challenge = random_bytes(32);
        session(['webauthn_challenge' => base64_encode($challenge)]);

        // Manually build WebAuthn options in correct format
        $options = [
            'rp' => [
                'name' => $this->rpName,
                'id' => $this->rpId,
            ],
            'user' => [
                'id' => base64_encode((string) $user->id),
                'name' => $user->email,
                'displayName' => $user->name,
            ],
            'challenge' => base64_encode($challenge),
            'pubKeyCredParams' => [
                [
                    'type' => 'public-key',
                    'alg' => -7, // ES256
                ],
                [
                    'type' => 'public-key', 
                    'alg' => -257, // RS256
                ],
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'userVerification' => 'required',
                'residentKey' => 'preferred',
            ],
            'attestation' => 'direct',
            'timeout' => 60000,
        ];

        \Log::info('Manual WebAuthn options created', [
            'user_id' => $user->id,
            'pubKeyCredParams' => $options['pubKeyCredParams'],
            'challenge_length' => strlen($challenge),
        ]);

        return $options;
    }

    /**
     * Verify registration response and store credential
     */
    public function verifyRegistrationResponse(User $user, string $attestationResponseJson): void
    {
        $attestationResponse = json_decode($attestationResponseJson, true);
        if (!$attestationResponse) {
            throw new Exception('Invalid attestation response');
        }

        $challenge = base64_decode(session('webauthn_challenge') ?? '');
        if (!$challenge) {
            throw new Exception('Challenge not found in session');
        }

        $validator = new AuthenticatorAttestationResponseValidator(
            [],
            new Denormalizer()
        );

        try {
            $publicKeyCredentialSource = $validator->check(
                $attestationResponse['response'],
                new PublicKeyCredentialCreationOptions(
                    new PublicKeyCredentialRpEntity($this->rpName, $this->rpId, $this->rpOrigin),
                    new PublicKeyCredentialUserEntity($user->email, (string) $user->id, $user->name),
                    $challenge,
                    []
                ),
                $this->rpOrigin
            );

            // Store credential
            $user->webauthn_credential_id = base64_encode($publicKeyCredentialSource->publicKeyCredentialId);
            $user->webauthn_public_key = base64_encode($publicKeyCredentialSource->credentialPublicKey);
            $user->webauthn_counter = $publicKeyCredentialSource->signCount;
            $user->biometric_enabled = true;
            $user->save();

            session()->forget('webauthn_challenge');
        } catch (Exception $e) {
            throw new Exception('Registration verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate options for unlock (assertion)
     */
    public function getUnlockOptions(User $user)
    {
        if (!$user->webauthn_credential_id) {
            throw new Exception('User has no registered biometric credential');
        }

        $challenge = random_bytes(32);
        session(['webauthn_challenge' => base64_encode($challenge)]);
        session(['webauthn_user_id' => $user->id]);

        // Manually build WebAuthn request options
        $options = [
            'challenge' => base64_encode($challenge),
            'allowCredentials' => [
                [
                    'type' => 'public-key',
                    'id' => $user->webauthn_credential_id, // Already base64 encoded
                ]
            ],
            'userVerification' => 'required',
            'timeout' => 60000,
        ];

        \Log::info('Manual WebAuthn unlock options created', [
            'user_id' => $user->id,
            'credential_id_length' => strlen($user->webauthn_credential_id),
            'challenge_length' => strlen($challenge),
        ]);
        
        return $options;
    }

    /**
     * Verify unlock response
     */
    public function verifyUnlockResponse(User $user, string $assertionResponseJson): bool
    {
        $assertionResponse = json_decode($assertionResponseJson, true);
        if (!$assertionResponse) {
            throw new Exception('Invalid assertion response');
        }

        $challenge = base64_decode(session('webauthn_challenge') ?? '');
        if (!$challenge) {
            throw new Exception('Challenge not found in session');
        }

        if (!$user->webauthn_credential_id || !$user->webauthn_public_key) {
            throw new Exception('User has no registered biometric credential');
        }

        $credentialId = base64_decode($user->webauthn_credential_id);
        $publicKey = base64_decode($user->webauthn_public_key);

        $publicKeyCredentialSource = new PublicKeyCredentialSource(
            $credentialId,
            \Webauthn\PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
            [],
            'none',
            null,
            $publicKey,
            $user->id,
            $user->webauthn_counter
        );

        $validator = new AuthenticatorAssertionResponseValidator(
            new Denormalizer()
        );

        try {
            $publicKeyCredentialSource = $validator->check(
                $publicKeyCredentialSource,
                $assertionResponse['response'],
                new PublicKeyCredentialRequestOptions(
                    $challenge,
                    PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED,
                    []
                ),
                $this->rpOrigin,
                null
            );

            // Update counter for replay attack prevention
            $user->webauthn_counter = $publicKeyCredentialSource->signCount;
            $user->save();

            session()->forget('webauthn_challenge');
            session()->forget('webauthn_user_id');

            return true;
        } catch (Exception $e) {
            throw new Exception('Unlock verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Disable biometric authentication
     */
    public function disable(User $user): void
    {
        $user->biometric_enabled = false;
        $user->webauthn_credential_id = null;
        $user->webauthn_public_key = null;
        $user->webauthn_counter = 0;
        $user->save();
    }
}
