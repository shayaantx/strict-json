# StrictJson

StrictJson converts JSON into your plain old PHP classes

**View the user documentation at https://sburba.github.io/strict-json/**

# Development

Working locally requires Docker or PHP 7.2 to be installed. The rest of these steps will assume you're using docker. If
you're not using docker, just run the commands without the `scripts/run_in_docker.sh` prefix (And skip building the
image, obviously).

## Build

Run `scripts/build_docker.sh` to build the docker image

## Install dependencies

Run `scripts/run_in_docker.sh composer install` to install all the required dependencies

## Validate Code

Run `scripts/run_in_docker.sh scripts/check_all.sh` to check code style and run unit and mutation tests

## Automatically fix code style

Run `scripts/run_in_docker.sh vendor/bin/php-cs-fixer fix .`

## Preview docs

Run `scripts/run_in_docker.sh vendor/bin/couscous preview 0.0.0.0:8000`
Go to localhost:8000 to preview your documentation changes
