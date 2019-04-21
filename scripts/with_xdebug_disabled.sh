#!/usr/bin/env bash

set -eu

xdebug_ini=$(php -i | grep -m 1 xdebug | sed 's/,//')
if [[ -z "${xdebug_ini}" ]]; then
    eval "$@"
    exit $?
fi

if [[ $(id -u) = 0 ]]; then
    mv_cmd="mv"
else
    mv_cmd="sudo mv"
fi

${mv_cmd} "${xdebug_ini}" "${xdebug_ini}.disabled"
set +e
eval "$@"
exit_code=$?
set -e
${mv_cmd} "${xdebug_ini}.disabled" "${xdebug_ini}"
exit ${exit_code}
