<?php

namespace App\Http\Controllers;

use App\Services\BiometricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webauthn\Denormalizer;
use Webauthn\PublicKeyCredentialCreationOptions;

class BiometricController extends Controller
{
    protected BiometricService $biometricService;

    public function __construct(BiometricService $biometricService)
    {
        $this->biometricService = $biometricService;
    }

    /**
     * Generate WebAuthn options for biometric setup
     */
    public function registerOptions(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if ($user->biometric_enabled) {
                return response()->json([
                    'error' => 'Biometric is already enabled for this user',
                ], 400);
            }

            $options = $this->biometricService->getRegistrationOptions($user);

            return response()->json([
                'options' => $options,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify WebAuthn registration response and store credential
     */
    public function registerVerify(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if ($user->biometric_enabled) {
                return response()->json([
                    'error' => 'Biometric is already enabled for this user',
                ], 400);
            }

            $attestationResponse = $request->input('attestationResponse');
            if (!$attestationResponse) {
                return response()->json([
                    'error' => 'Attestation response is required',
                ], 400);
            }

            // Denormalize the response to expected format
            $denormalizer = new Denormalizer();
            $attestationResponseData = [
                'response' => $attestationResponse,
            ];
            
            $this->biometricService->verifyRegistrationResponse($user, json_encode($attestationResponseData));

            return response()->json([
                'success' => true,
                'message' => 'Biometric authentication enabled successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate WebAuthn options for unlock
     */
    public function unlockOptions(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user->biometric_enabled) {
                return response()->json([
                    'error' => 'Biometric authentication is not enabled',
                ], 400);
            }

            $options = $this->biometricService->getUnlockOptions($user);

            return response()->json([
                'options' => $options,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify WebAuthn unlock response
     */
    public function unlockVerify(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user->biometric_enabled) {
                return response()->json([
                    'error' => 'Biometric authentication is not enabled',
                ], 400);
            }

            $assertionResponse = $request->input('assertionResponse');
            if (!$assertionResponse) {
                return response()->json([
                    'error' => 'Assertion response is required',
                ], 400);
            }

            // Wrap response in expected format
            $assertionResponseData = [
                'response' => $assertionResponse,
            ];
            
            $this->biometricService->verifyUnlockResponse($user, json_encode($assertionResponseData));

            return response()->json([
                'success' => true,
                'message' => 'Biometric verification successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Disable biometric authentication
     */
    public function disable(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user->biometric_enabled) {
                return response()->json([
                    'error' => 'Biometric authentication is not enabled',
                ], 400);
            }

            $this->biometricService->disable($user);

            return response()->json([
                'success' => true,
                'message' => 'Biometric authentication disabled',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get biometric status for current user
     */
    public function status(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'biometric_enabled' => (bool) $user->biometric_enabled,
        ]);
    }
}
