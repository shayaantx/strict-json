#!/usr/bin/env bash
set -euo pipefail

scripts/check_code_style.sh
scripts/run_tests.sh
scripts/run_infection.sh
