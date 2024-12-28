<?php

namespace Pathogen\Parsing;

interface ParserInterface
{
    function parse(string $path, ParseOptions $options): ParsingResult;
}