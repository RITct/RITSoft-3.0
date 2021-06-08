#!/usr/bin/env bash
docker-compose up -d
chmod +x ./src/wait_for_it.sh
./src/wait_for_it.sh localhost:3000
# Waiting for hot reload
sleep 5
curl localhost:3000