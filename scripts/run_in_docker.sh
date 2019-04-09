#!/usr/bin/env bash
set -euo pipefail

if [[ -z ${CI:-} ]]; then
    docker_args="-tv $(pwd):/app -u $(id -u):$(id -g)"
else
    docker_args=""
fi

docker run ${docker_args} strict-json "$@"
