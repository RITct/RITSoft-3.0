#!/usr/bin/env bash
chmod +x ./docker-start.sh && sh ./docker-start.sh
chmod +x ./src/wait_for_it.sh && ./src/wait_for_it.sh localhost:3000
# Waiting for hot reload
sleep 5
curl localhost:3000