#!/usr/bin/env bash
set -euo pipefail

vendor/bin/infection \
    --threads=$(nproc) \
    --coverage=test-results/phpunit/ \
    --min-msi=89 \
    --min-covered-msi=92
