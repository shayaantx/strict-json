#!/usr/bin/env bash
set -euo pipefail

vendor/bin/infection \
    --initial-tests-php-options="-d zend_extension=xdebug.so" --threads=$(nproc) \
    --min-msi=89 \
    --min-covered-msi=92
