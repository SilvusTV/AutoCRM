#!/bin/bash

# Initialize Let's Encrypt for the Laravel application
# This script helps set up Let's Encrypt certificates for the first time

# Exit on error
set -e

# Default values
email=""
domain=""
staging=0

# Parse command line arguments
while [[ $# -gt 0 ]]; do
  case $1 in
    --email)
      email="$2"
      shift 2
      ;;
    --domain)
      domain="$2"
      shift 2
      ;;
    --staging)
      staging=1
      shift
      ;;
    *)
      echo "Unknown option: $1"
      exit 1
      ;;
  esac
done

# Check if required parameters are provided
if [ -z "$email" ] || [ -z "$domain" ]; then
  echo "Usage: $0 --email your-email@example.com --domain yourdomain.com [--staging]"
  echo ""
  echo "Options:"
  echo "  --email EMAIL     Email address for Let's Encrypt notifications"
  echo "  --domain DOMAIN   Domain name for the certificate"
  echo "  --staging         Use Let's Encrypt staging server (for testing)"
  exit 1
fi

# Create required directories
mkdir -p ./docker/nginx/certbot/conf
mkdir -p ./docker/nginx/certbot/www

# Update .env file with required variables
if grep -q "CERTBOT_EMAIL=" .env; then
  # Update existing variables
  sed -i "s/CERTBOT_EMAIL=.*/CERTBOT_EMAIL=$email/" .env
  sed -i "s/DOMAIN_NAME=.*/DOMAIN_NAME=$domain/" .env
else
  # Add new variables
  echo "" >> .env
  echo "# Let's Encrypt configuration" >> .env
  echo "CERTBOT_EMAIL=$email" >> .env
  echo "DOMAIN_NAME=$domain" >> .env
fi

echo "Environment variables updated in .env file."

# Create a temporary nginx config for certificate initialization
cat > ./docker/nginx/init-ssl.conf << EOF
server {
    listen 80;
    server_name $domain;
    
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    location / {
        return 200 "Let's Encrypt initialization server";
    }
}
EOF

echo "Created temporary Nginx configuration for certificate initialization."

# Create a temporary docker-compose file for initialization
cat > ./docker-compose-init-ssl.yml << EOF
version: '3.8'

services:
  nginx:
    image: nginx:stable-alpine
    container_name: init-nginx
    ports:
      - "80:80"
    volumes:
      - ./docker/nginx/init-ssl.conf:/etc/nginx/conf.d/default.conf
      - ./docker/nginx/certbot/www:/var/www/certbot
    restart: unless-stopped

  certbot:
    image: certbot/certbot
    container_name: init-certbot
    volumes:
      - ./docker/nginx/certbot/conf:/etc/letsencrypt
      - ./docker/nginx/certbot/www:/var/www/certbot
    depends_on:
      - nginx
EOF

# Add the appropriate certbot command based on staging flag
if [ $staging -eq 1 ]; then
  echo "    command: certonly --webroot --webroot-path=/var/www/certbot --email $email --agree-tos --no-eff-email --staging -d $domain --force-renewal" >> ./docker-compose-init-ssl.yml
else
  echo "    command: certonly --webroot --webroot-path=/var/www/certbot --email $email --agree-tos --no-eff-email -d $domain --force-renewal" >> ./docker-compose-init-ssl.yml
fi

echo "Created temporary docker-compose file for certificate initialization."

echo "Starting certificate initialization process..."
docker-compose -f docker-compose-init-ssl.yml up -d nginx
echo "Waiting for Nginx to start..."
sleep 5
docker-compose -f docker-compose-init-ssl.yml up certbot
echo "Certificate initialization completed."

# Copy certificates to the correct location
mkdir -p ./docker/nginx/ssl/letsencrypt
cp -r ./docker/nginx/certbot/conf/live/$domain/* ./docker/nginx/ssl/letsencrypt/
echo "Certificates copied to ./docker/nginx/ssl/letsencrypt/"

# Clean up
docker-compose -f docker-compose-init-ssl.yml down
rm ./docker-compose-init-ssl.yml
rm ./docker/nginx/init-ssl.conf

echo "Initialization complete. You can now deploy your application with Let's Encrypt SSL."
echo "Run: docker-compose -f docker-compose-production.yml up -d"