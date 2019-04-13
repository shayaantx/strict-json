#!/usr/bin/env bash
set -euo pipefail

branch=${CIRCLE_BRANCH:-$(git symbolic-ref --short -q HEAD)}
repository=https://${GITHUB_TOKEN}@github.com/sburba/strict-json

if [[ ! -z ${CIRCLE_PULL_REQUEST:-} ]]; then
    echo "Skipping docs deploy, PR branch"
    exit 0
fi

if [[ ! ${branch} == 'master' ]]; then
    echo "Skipping docs deploy, non master branch"
    exit 0
fi

if [[ ! -z ${CI:-} ]]; then
    git config --global user.name "CI AutoDeploy"
    git config --global user.email "autodeploy@samburba.com"
fi

mkdir -p ~/.ssh/
touch ~/.ssh/known_hosts
ssh-keyscan -H github.com 2>/dev/null >> ~/.ssh/known_hosts

# Couscous really likes to log the access token, so redirect all output to /dev/null
vendor/bin/couscous deploy --repository "${repository}" &> /dev/null
echo "Deployed Docs"
