<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use Freep\Security\Path;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FilesystemDirectoriesTest extends TestCase
{
    private function makeDir(string $path): void
    {
        if (is_dir($path) === false) {
            mkdir($path, 0777, true);
        }
    }

    /** @test */
    public function getDirectoryContents(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([
            new Path(__DIR__ . '/structure/level-one/file-one.txt'),
            new Path(__DIR__ . '/structure/level-one/level-two')
        ], $instance->getDirectoryContents('level-one'));
    }

    /** @test */
    public function getDirectoryEmptyContents(): void
    {
        $this->makeDir(__DIR__ . '/structure/empty');

        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([], $instance->getDirectoryContents('empty'));
    }

    /** @test */
    public function getDirectoryFiles(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');

        $this->assertEquals([
            new Path(__DIR__ . '/structure/level-one/file-one.txt'),
        ], $instance->getDirectoryFiles('level-one'));
    }

    /** @test */
    public function getDirectoryEmptyFiles(): void
    {
        $this->makeDir(__DIR__ . '/structure/empty');

        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([], $instance->getDirectoryFiles('empty'));
    }

    /** @test */
    public function getDirectorySubdirs(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure');

        $this->assertEquals([
            new Path(__DIR__ . '/structure/level-one/level-two')
        ], $instance->getDirectorySubdirs('level-one'));
    }

    /** @test */
    public function getDirectoryEmptySubdirs(): void
    {
        $this->makeDir(__DIR__ . '/structure/empty');

        $instance = new Filesystem(__DIR__ . '/structure');
        $this->assertEquals([], $instance->getDirectorySubdirs('empty'));
    }

    /** @test */
    public function getDirectoryContentsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Directory ' . __DIR__ . '/structure/not-exists does not exist');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getDirectoryContents('not-exists');
    }

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
    public function makeDirectory(): void
    {
        $contextPath = __DIR__ . '/structure';

        $instance = new Filesystem($contextPath);
        $instance->makeDirectory('level-one/level-two/level-three');

        $this->assertDirectoryExists("$contextPath/level-one/level-two/level-three");

        // isso não causará efeito. diretório não precisa ser criado
        $instance->makeDirectory('level-one/level-two/level-three');

        rmdir("$contextPath/level-one/level-two/level-three");
    }

    /** @test */
    public function makeDirectoryLocalPathException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The path specified for the directory to be created is invalid');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->makeDirectory('http://level-one');
    }

    /** @test */
    public function makeDirectoryRelativePathException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The directory path to be created cannot be relative');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->makeDirectory('/../../');
    }

    // /** @test */
    // public function makeDirectoryOperationException(): void
    // {
    //     $this->markTestIncomplete();
    // }
}
