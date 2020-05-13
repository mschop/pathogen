#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DOCKER_DIR=$DIR/docker

docker=`command -v docker`

if [ -z "$docker" ]; then
    printf "\nDocker is missing from your installation.\n"
    exit 1
fi

mutations=NO

for option in "$@"; do
  case $option in
    --mutations)
      mutations=YES
      ;;
  esac
done


for version in "7.4" # Later add further versions here
do
    docker build "$DOCKER_DIR" -t "pathogen-php-$version" --build-arg "PHP_VERSION=$version"
    docker run -v "$DIR:/code" -w "/code" "pathogen-php-$version" composer install --no-interaction --no-progress

    if [[ $mutations == "YES" ]]; then
        docker run -v "$DIR:/code" -w "/code" "pathogen-php-$version" vendor/bin/infection
    else
        docker run -v "$DIR:/code" -w "/code" "pathogen-php-$version" vendor/bin/phpunit
    fi
done
