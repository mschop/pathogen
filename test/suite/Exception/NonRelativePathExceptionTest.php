<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pathogen\Exception;

use Eloquent\Pathogen\RelativePath;
use Exception;


/**
 * @covers Eloquent\Pathogen\Exception\NonRelativePathException
 * @covers Eloquent\Pathogen\Exception\AbstractInvalidPathException
 */
class NonRelativePathExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $path = new RelativePath(array('foo', 'bar'));
        $previous = new Exception;
        $exception = new NonRelativePathException($path, $previous);

        $this->assertSame("Invalid path 'foo/bar'. The supplied path is not relative.", $exception->getMessage());
        $this->assertSame('The supplied path is not relative.', $exception->reason());
        $this->assertSame($path, $exception->path());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
