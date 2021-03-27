#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker run --rm -it -v "$DIR:/code" -w "/code" "pathogen-php-8.0" "$@"