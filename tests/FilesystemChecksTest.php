<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemChecksTest extends TestCase
{
    /** @test */
    public function isDirectory(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertTrue($instance->isDirectory('level-one'));

        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertTrue($instance->isDirectory('level-two'));

        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertFalse($instance->isDirectory('level-deep'));
    }

    /** @test */
    public function isFile(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertTrue($instance->isFile('level-one/file-one.txt'));

        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertTrue($instance->isFile('file-one.txt'));

        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertTrue($instance->isFile('level-two/file-two.txt'));

        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertFalse($instance->isFile('file-nop.txt'));
    }

    /** @test */
    public function isReadableObject(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function isWritableObject(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }
}
