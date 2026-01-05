#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  DayMeter - HTTPS Local Development Server${NC}"
echo -e "${BLUE}================================================${NC}\n"

# Check if Docker is running
if ! docker ps > /dev/null 2>&1; then
    echo -e "${RED}✗ Docker is not running${NC}"
    exit 1
fi

# Check if certificates exist
if [ ! -f "docker/certs/localhost.crt" ] || [ ! -f "docker/certs/localhost.key" ]; then
    echo -e "${YELLOW}Certificates not found, generating...${NC}"
    ./docker/certs/generate-certs.sh
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Failed to generate certificates${NC}"
        exit 1
    fi
fi

# Stop existing containers
echo -e "${YELLOW}Stopping existing containers...${NC}"
docker compose down > /dev/null 2>&1

# Start Docker Compose services
echo -e "${YELLOW}Starting services...${NC}"
docker compose up -d

if [ $? -ne 0 ]; then
    echo -e "${RED}✗ Failed to start Docker Compose${NC}"
    exit 1
fi

# Wait for services to be ready
echo -e "${YELLOW}Waiting for services to be ready...${NC}"
sleep 5

# Check if services are healthy
echo -e "${YELLOW}Checking service health...${NC}"
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if curl -s https://localhost/health > /dev/null 2>&1 || curl -s -k https://localhost > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Services are ready${NC}\n"
        break
    fi
    attempt=$((attempt + 1))
    echo -n "."
    sleep 1
done

if [ $attempt -eq $max_attempts ]; then
    echo -e "${YELLOW}Services are starting up (this can take a moment)${NC}"
fi

echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  ✓ DayMeter HTTPS Server Started${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${BLUE}Access the application:${NC}"
echo -e "  ${BLUE}https://localhost${NC}"
echo ""
echo -e "${BLUE}Services:${NC}"
echo -e "  App:      ${BLUE}https://localhost${NC}"
echo -e "  Mail:     ${BLUE}http://localhost:8025${NC}"
echo -e "  Database: localhost:3306 (mysql)"
echo -e "  Redis:    localhost:6379"
echo ""
echo -e "${YELLOW}Notes:${NC}"
echo -e "  • HTTPS is enabled with self-signed certificate"
echo -e "  • Certificate trust depends on your browser/system"
echo -e "  • Run '${BLUE}docker compose logs -f${NC}' to see logs"
echo -e "  • Run '${BLUE}./stop-https.sh${NC}' to stop the server"
echo ""
