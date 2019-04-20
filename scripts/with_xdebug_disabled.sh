#!/usr/bin/env bash

set -euo pipefail

xdebug_ini=$(php -i | grep "xdebug.*\.ini" -m 1)
if [[ -z "${xdebug_ini}" ]]; then
    eval "$@"
    exit $?
fi

mv "${xdebug_ini}" "${xdebug_ini}.disabled"
set +e
eval "$@"
exit_code=$?
set -e
mv "${xdebug_ini}.disabled" "${xdebug_ini}"
exit ${exit_code}
