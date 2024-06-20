<?php

declare(strict_types=1);
namespace Swissmakers\OpenSSH\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Get the path to a stub file.
     *
     * @param string $nameOfStub
     * @return string
     */
    public function getStub(string $nameOfStub): string
    {
        return __DIR__."/stubs/{$nameOfStub}";
    }

    /**
     * Get the path to a temporary file.
     *
     * @param string $fileName
     * @return string
     */
    public function getTempPath(string $fileName): string
    {
        return __DIR__."/temp/{$fileName}";
    }
}
