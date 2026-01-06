<?php

namespace App\Http\Controllers;

use App\Services\BiometricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            
            // Debug logging
            \Log::info('WebAuthn Registration Options Generated', [
                'user_id' => $user->id,
                'options_type' => gettype($options),
                'options_keys' => is_array($options) ? array_keys($options) : 'not_array',
                'has_pubKeyCredParams' => is_array($options) && isset($options['pubKeyCredParams']),
                'pubKeyCredParams_type' => is_array($options) && isset($options['pubKeyCredParams']) ? gettype($options['pubKeyCredParams']) : 'missing',
            ]);
            
            if (is_array($options) && isset($options['pubKeyCredParams'])) {
                \Log::info('pubKeyCredParams content', [
                    'pubKeyCredParams' => $options['pubKeyCredParams']
                ]);
            }

            return response()->json([
                'options' => $options,
            ]);
        } catch (\Exception $e) {
            \Log::error('WebAuthn Registration Options Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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

            \Log::info('Received attestation response', [
                'user_id' => $user->id,
                'response_keys' => array_keys($attestationResponse),
                'response_type' => gettype($attestationResponse)
            ]);
            
            // Pass the raw attestation response to service
            $this->biometricService->verifyRegistrationResponse($user, json_encode($attestationResponse));

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

            \Log::info('Received assertion response', [
                'user_id' => $user->id,
                'response_keys' => array_keys($assertionResponse),
                'response_type' => gettype($assertionResponse)
            ]);
            
            // Pass the raw assertion response to service
            $this->biometricService->verifyUnlockResponse($user, json_encode($assertionResponse));

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
