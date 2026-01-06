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

        \Log::info('Processing registration response', [
            'user_id' => $user->id,
            'has_id' => isset($attestationResponse['id']),
            'has_rawId' => isset($attestationResponse['rawId']),
            'has_response' => isset($attestationResponse['response']),
            'response_keys' => isset($attestationResponse['response']) ? array_keys($attestationResponse['response']) : 'missing'
        ]);

        try {
            // For now, just store the basic credential info without full validation
            // This is simplified for testing - in production you'd want full WebAuthn validation
            if (!isset($attestationResponse['id']) || !isset($attestationResponse['rawId'])) {
                throw new Exception('Missing credential ID in response');
            }

            // Store credential
            $user->webauthn_credential_id = $attestationResponse['rawId']; // Already base64 encoded from frontend
            $user->webauthn_public_key = 'placeholder_public_key'; // Simplified for now
            $user->webauthn_counter = 0;
            $user->biometric_enabled = true;
            $user->save();

            \Log::info('Biometric registration completed', [
                'user_id' => $user->id,
                'credential_id_length' => strlen($user->webauthn_credential_id)
            ]);

            session()->forget('webauthn_challenge');
        } catch (Exception $e) {
            \Log::error('Registration verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
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

        if (!$user->webauthn_credential_id) {
            throw new Exception('User has no registered biometric credential');
        }

        \Log::info('Processing unlock response', [
            'user_id' => $user->id,
            'has_id' => isset($assertionResponse['id']),
            'has_response' => isset($assertionResponse['response']),
            'credential_id_matches' => isset($assertionResponse['rawId']) && $assertionResponse['rawId'] === $user->webauthn_credential_id
        ]);

        try {
            // Simplified validation - just check that the credential ID matches
            // In production, you'd want full WebAuthn signature verification
            if (!isset($assertionResponse['id']) || !isset($assertionResponse['rawId'])) {
                throw new Exception('Missing credential ID in assertion response');
            }

            if ($assertionResponse['rawId'] !== $user->webauthn_credential_id) {
                throw new Exception('Credential ID mismatch');
            }

            // Update counter (simplified)
            $user->webauthn_counter = $user->webauthn_counter + 1;
            $user->save();

            \Log::info('Biometric unlock completed', [
                'user_id' => $user->id,
                'counter' => $user->webauthn_counter
            ]);

            session()->forget('webauthn_challenge');
            session()->forget('webauthn_user_id');

            return true;
        } catch (Exception $e) {
            \Log::error('Unlock verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
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
