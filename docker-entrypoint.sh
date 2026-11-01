#!/bin/sh
set -e
echo "Listen ${PORT:-10000}" > /etc/apache2/ports.conf
exec "$@"
