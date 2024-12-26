<?php

namespace Mschop\Pathogen\Parsing;

interface ParserInterface
{
    function parse(string $path, ParseOptions $options): ParsingResult;
}