#!/bin/bash

COMPOSER_PATH="vendor"
NODE_PATH="node_modules"

# Проверяем, существует ли sail
if [ ! -d "$COMPOSER_PATH" ]; then
    echo "vendor not found. Installing..."
    docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/app" \
      -w /app \
      composer install --ignore-platform-reqs
fi

if [ ! -d "$NODE_PATH" ]; then
    echo "node_modules not found. Installing..."
    docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/app" \
      -w /app \
      node npm ci
fi

