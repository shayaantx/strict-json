#!/usr/bin/env bash
set -euo pipefail

$(dirname $0)/run_in_docker.sh vendor/bin/infection \
    --initial-tests-php-options="-d zend_extension=xdebug.so" --threads=$(nproc) \
    --min-msi=89 \
    --min-covered-msi=92
