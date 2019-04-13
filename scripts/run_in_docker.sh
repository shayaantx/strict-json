#!/usr/bin/env bash
set -euo pipefail

docker run --rm -itv $(pwd):/app -u $(id -u):$(id -g) -p 8000:8000 strict-json "$@"
