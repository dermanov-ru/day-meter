#!/bin/bash

# Add certificate to Chrome/Chromium trusted certificates

CERT_PATH="/home/mark/projects/DayMeter/docker/certs/localhost.crt"
CHROME_CONFIG_PATHS=(
    ~/.config/chromium
    ~/.config/google-chrome
    ~/.config/google-chrome-unstable
)

echo "Adding certificate to Chrome/Chromium..."

# Convert PEM to DER format (required by Chrome)
openssl x509 -in "$CERT_PATH" -outform DER -out /tmp/localhost.der 2>/dev/null

if [ ! -f /tmp/localhost.der ]; then
    echo "✗ Failed to convert certificate"
    exit 1
fi

# Try to add to Chrome default profile
for chrome_path in "${CHROME_CONFIG_PATHS[@]}"; do
    if [ -d "$chrome_path" ]; then
        # Create certificates directory if it doesn't exist
        mkdir -p "$chrome_path/Default/Certificates"
        
        # Copy certificate
        cp /tmp/localhost.der "$chrome_path/Default/Certificates/localhost.der" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo "✓ Certificate added to $chrome_path"
        fi
    fi
done

# Clean up
rm -f /tmp/localhost.der

echo ""
echo "To complete the process:"
echo "1. Close Chrome/Chromium completely"
echo "2. Open Chrome: chrome://settings/certificates"
echo "3. Go to 'Authorities' tab"
echo "4. Search for 'localhost' and import the certificate"
echo "5. Check 'Trust this certificate for identifying websites'"
echo ""
echo "Or simply run this command to auto-trust on Linux:"
echo "  sudo cp $CERT_PATH /usr/local/share/ca-certificates/"
echo "  sudo update-ca-certificates"
