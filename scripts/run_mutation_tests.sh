#!/usr/bin/env bash
set -euo pipefail

vendor/bin/infection \
    --threads=$(nproc) \
    --coverage=test-results/phpunit/ \
    --min-msi=93 \
    --min-covered-msi=96
