#!/usr/bin/env bash
set -euo pipefail

scripts/with_xdebug_disabled.sh vendor/bin/php-cs-fixer fix . --dry-run
