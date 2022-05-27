<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemGettersTest extends TestCase
{
    /** @test */
    public function getDirectoryContents(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([
            __DIR__ . '/structure/level-one/file-one.txt',
            __DIR__ . '/structure/level-one/level-two'
        ], $instance->getDirectoryContents('level-one'));
    }

    /** @test */
    public function getDirectoryContentsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given path is out of context');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getDirectoryContents('not-exists');
    }
    
    /** @test */
    public function getFileRows(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([
            '000', '111', '222', '333', '444', '555', '666', '777', '888', '999'
        ], $instance->getFileRows('file-zero.txt'));
    }

    /** @test */
    public function getFileRowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given path is out of context');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getFileRows('not-exists.txt');
    }

    /** @test */
    public function getFileContents(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');

        $this->assertEquals(
            "000\n111\n222\n333\n444\n555\n666\n777\n888\n999",
            $instance->getFileContents('file-zero.txt')
        );
    }

    /** @test */
    public function getFileContentsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given path is out of context');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getFileContents('not-exists.txt');
    }
}
