#!/usr/bin/env bash
set -euo pipefail

docker run -tv $(pwd):/app -u $(id -u):$(id -g) strict-json "$@"
