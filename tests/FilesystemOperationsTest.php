<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemOperationsTest extends TestCase
{
    /** @test */
    public function changePermissions(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function changePermissionsException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function makeDirectory(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');

        $instance->makeDirectory('level-one/level-two/level-three');

        $this->assertDirectoryExists(__DIR__ . '/structure/level-one/level-two/level-three');
    }

    /** @test */
    public function makeDirectoryLocalPathException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function makeDirectoryRelativePathException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function makeDirectoryOperationException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function setFileContents(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function setFileContentsException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function appendFileContents(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }

    /** @test */
    public function appendFileContentsException(): void
    {
        $this->markTestIncomplete();
        $this->assertTrue(true);
    }
}
