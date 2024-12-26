#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker run -it -w "/code" -v "$DIR:/code" pathogen-php-8.3 bash