#!/bin/bash
# Ejecuta npm run build dentro del contenedor node-builder
set -e

CONTAINER="catenab_node"

if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    echo "El contenedor '${CONTAINER}' no está corriendo. Iniciándolo..."
    docker start "$CONTAINER"
    sleep 2
fi

docker exec "$CONTAINER" npm run "$@"
