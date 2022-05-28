<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemPermissionsTest extends TestCase
{
    /** @test */
    public function changePermissions(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');

        $instance->changePermissions('file-zero.txt', 0444);
        $this->assertEquals('0444', $instance->getFilePermissions('file-zero.txt'));

        $instance->changePermissions('file-zero.txt', 0644);
        $this->assertEquals('0644', $instance->getFilePermissions('file-zero.txt'));
    }

    // /** @test */
    // public function changePermissionsException(): void
    // {
    //     $instance = new Filesystem(__DIR__ . '/structure');
    //     $instance->changePermissions('file-zero.txt', 999999999);
    // }

    /** @test */
    public function isReadableObject(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertTrue($instance->isReadable('file-zero.txt'));
    }

    /** @test */
    public function isReadableObjectException(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertFalse($instance->isReadable('../../../file-zero.txt'));
    }

    /** @test */
    public function isWritableObject(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->changePermissions('file-zero.txt', 0777);
        $this->assertTrue($instance->isWritable('file-zero.txt'));
    }

    /** @test */
    public function isWritableObjectException(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertFalse($instance->isWritable('../../../file-zero.txt'));
    }
}
