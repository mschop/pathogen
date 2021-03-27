#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DOCKER_DIR=$DIR

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

versions=( "8.0" )

for version in "${versions[@]}"
do
    docker build "$DOCKER_DIR" -t "pathogen-php-$version" --build-arg "PHP_VERSION=$version"
    docker run --rm -v "$DIR:/code" -w "/code" "pathogen-php-$version" composer update --no-interaction --no-progress

    if [[ $mutations == "YES" ]]; then
        docker run --rm -v "$DIR:/code" -w "/code" "pathogen-php-$version" vendor/bin/infection
    else
        docker run --rm -v "$DIR:/code" -w "/code" "pathogen-php-$version" vendor/bin/phpunit
    fi
done
