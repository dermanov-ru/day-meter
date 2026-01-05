/**
 * BiometricService - Handles WebAuthn registration and unlocking
 */
export class BiometricService {
  constructor() {
    this.baseUrl = '/api/biometric';
  }

  /**
   * Check if WebAuthn is supported
   */
  isSupported() {
    return !!(
      navigator.credentials &&
      navigator.credentials.create &&
      navigator.credentials.get
    );
  }

  /**
   * Check if platform authenticator is available (biometric)
   */
  async isPlatformAuthenticatorAvailable() {
    if (!this.isSupported()) {
      return false;
    }

    try {
      return await window.PublicKeyCredential?.isUserVerifyingPlatformAuthenticatorAvailable();
    } catch (error) {
      console.error('Error checking platform authenticator:', error);
      return false;
    }
  }

  /**
   * Get registration options from server
   */
  async getRegistrationOptions() {
    try {
      const response = await axios.post(`${this.baseUrl}/register/options`);
      return response.data.options;
    } catch (error) {
      throw new Error(
        error.response?.data?.error || 'Failed to get registration options'
      );
    }
  }

  /**
   * Convert ArrayBuffer to base64 string
   */
  arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    bytes.forEach(byte => {
      binary += String.fromCharCode(byte);
    });
    return window.btoa(binary);
  }

  /**
   * Convert base64 string to ArrayBuffer
   */
  base64ToArrayBuffer(base64) {
    const binary = window.atob(base64);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }
    return bytes.buffer;
  }

  /**
   * Register biometric credential
   */
  async register() {
    if (!this.isSupported()) {
      throw new Error('WebAuthn not supported on this device');
    }

    try {
      // Get options from server
      const options = await this.getRegistrationOptions();

      // Convert base64 to ArrayBuffer for WebAuthn
      options.challenge = this.base64ToArrayBuffer(options.challenge);
      options.user.id = this.base64ToArrayBuffer(options.user.id);

      // Create credential
      const credential = await navigator.credentials.create({
        publicKey: options,
      });

      if (!credential) {
        throw new Error('Biometric registration cancelled or failed');
      }

      // Send response to server
      const attestationResponse = {
        response: {
          clientDataJSON: this.arrayBufferToBase64(
            credential.response.clientDataJSON
          ),
          attestationObject: this.arrayBufferToBase64(
            credential.response.attestationObject
          ),
        },
        id: credential.id,
        rawId: this.arrayBufferToBase64(credential.rawId),
        type: credential.type,
      };

      const response = await axios.post(
        `${this.baseUrl}/register/verify`,
        { attestationResponse }
      );

      return response.data;
    } catch (error) {
      throw new Error(
        error.response?.data?.error ||
          error.message ||
          'Biometric registration failed'
      );
    }
  }

  /**
   * Get unlock options from server
   */
  async getUnlockOptions() {
    try {
      const response = await axios.post(`${this.baseUrl}/unlock/options`);
      return response.data.options;
    } catch (error) {
      throw new Error(
        error.response?.data?.error || 'Failed to get unlock options'
      );
    }
  }

  /**
   * Unlock with biometric
   */
  async unlock() {
    if (!this.isSupported()) {
      throw new Error('WebAuthn not supported on this device');
    }

    try {
      // Get options from server
      const options = await this.getUnlockOptions();

      // Convert base64 to ArrayBuffer for WebAuthn
      options.challenge = this.base64ToArrayBuffer(options.challenge);

      if (options.allowCredentials && options.allowCredentials.length > 0) {
        options.allowCredentials.forEach(cred => {
          cred.id = this.base64ToArrayBuffer(cred.id);
        });
      }

      // Get credential assertion
      const assertion = await navigator.credentials.get({
        publicKey: options,
      });

      if (!assertion) {
        throw new Error('Biometric verification cancelled or failed');
      }

      // Send response to server
      const assertionResponse = {
        response: {
          clientDataJSON: this.arrayBufferToBase64(assertion.response.clientDataJSON),
          authenticatorData: this.arrayBufferToBase64(
            assertion.response.authenticatorData
          ),
          signature: this.arrayBufferToBase64(assertion.response.signature),
          userHandle: assertion.response.userHandle
            ? this.arrayBufferToBase64(assertion.response.userHandle)
            : null,
        },
        id: assertion.id,
        rawId: this.arrayBufferToBase64(assertion.rawId),
        type: assertion.type,
      };

      const response = await axios.post(
        `${this.baseUrl}/unlock/verify`,
        { assertionResponse }
      );

      return response.data;
    } catch (error) {
      throw new Error(
        error.response?.data?.error ||
          error.message ||
          'Biometric verification failed'
      );
    }
  }

  /**
   * Check biometric status
   */
  async getStatus() {
    try {
      const response = await axios.get(`${this.baseUrl}/status`);
      return response.data;
    } catch (error) {
      console.error('Failed to get biometric status:', error);
      return { biometric_enabled: false };
    }
  }

  /**
   * Disable biometric
   */
  async disable() {
    try {
      const response = await axios.post(`${this.baseUrl}/disable`);
      return response.data;
    } catch (error) {
      throw new Error(
        error.response?.data?.error || 'Failed to disable biometric'
      );
    }
  }
}

export default new BiometricService();
