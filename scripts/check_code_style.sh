#!/usr/bin/env bash
set -euo pipefail

$(dirname $0)/run_in_docker.sh vendor/bin/php-cs-fixer fix . --dry-run
