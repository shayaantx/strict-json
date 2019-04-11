#!/usr/bin/env bash
set -euo pipefail

vendor/bin/php-cs-fixer fix . --dry-run
