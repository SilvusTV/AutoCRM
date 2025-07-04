#!/bin/bash

# Bash script to generate self-signed SSL certificates for development/testing

# Set the output directory
outputDir="$(dirname "$0")"

# Set certificate details
domain="localhost"
password="password"
daysValid=365

# Generate private key and certificate
echo "Generating self-signed SSL certificate for $domain..."
openssl req -x509 -nodes -days $daysValid -newkey rsa:2048 -keyout "$outputDir/server.key" -out "$outputDir/server.crt" -subj "/CN=$domain" -addext "subjectAltName=DNS:$domain,DNS:www.$domain,IP:127.0.0.1"

echo "SSL certificate generated successfully!"
echo "Private key: $outputDir/server.key"
echo "Certificate: $outputDir/server.crt"

# Make the script executable
chmod +x "$0"