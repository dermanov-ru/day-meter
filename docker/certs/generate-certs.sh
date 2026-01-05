#!/bin/bash

# Generate self-signed certificate for localhost
CERTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Check if certificates already exist
if [ -f "$CERTS_DIR/localhost.crt" ] && [ -f "$CERTS_DIR/localhost.key" ]; then
    echo "Certificates already exist, skipping generation"
    exit 0
fi

echo "Generating self-signed certificate for localhost..."

# Generate private key and certificate
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "$CERTS_DIR/localhost.key" \
    -out "$CERTS_DIR/localhost.crt" \
    -subj "/C=RU/ST=Moscow/L=Moscow/O=DayMeter/OU=Dev/CN=localhost" \
    -addext "subjectAltName=DNS:localhost,DNS:*.localhost,IP:127.0.0.1"

if [ $? -eq 0 ]; then
    echo "✓ Certificates generated successfully"
    echo "  - Certificate: $CERTS_DIR/localhost.crt"
    echo "  - Key: $CERTS_DIR/localhost.key"
    echo ""
    echo "On macOS, to trust the certificate:"
    echo "  sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain $CERTS_DIR/localhost.crt"
    echo ""
    echo "On Linux (Ubuntu/Debian), to trust the certificate:"
    echo "  sudo cp $CERTS_DIR/localhost.crt /usr/local/share/ca-certificates/"
    echo "  sudo update-ca-certificates"
else
    echo "✗ Failed to generate certificates"
    exit 1
fi
