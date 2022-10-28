<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FilesystemFilesTest extends TestCase
{
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
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File ' . __DIR__ . '/structure/not-exists.txt does not exist');

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
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File ' . __DIR__ . '/structure/not-exists.txt does not exist');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getFileContents('not-exists.txt');
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
    public function setFileContents(): void
    {
        $contextPath = __DIR__ . '/structure';

        $instance = new Filesystem($contextPath);

        $instance->setFileContents('runtime/set-file-contents.txt', 'contents');
        $this->assertEquals('contents', $instance->getFileContents('runtime/set-file-contents.txt'));

        $instance->setFileContents('runtime/set-file-contents.txt', 'naitis');
        $this->assertEquals('naitis', $instance->getFileContents('runtime/set-file-contents.txt'));

        unlink("$contextPath/runtime/set-file-contents.txt");
        $this->assertTrue(rmdir("$contextPath/runtime"));
    }

    /** @test */
    public function setFileContentsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file path path must be absolute');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->setFileContents('runtime/../../set-file-contents.txt', 'naitis');
    }

    /** @test */
    public function appendFileContents(): void
    {
        $contextPath = __DIR__ . '/structure';
        $filePath = 'runtime/append-file-contents.txt';

        $instance = new Filesystem($contextPath);

        $instance->appendFileContents($filePath, 'contents');
        $this->assertEquals('contents', $instance->getFileContents($filePath));

        $instance->appendFileContents($filePath, 'naitis');
        $this->assertEquals('contentsnaitis', $instance->getFileContents($filePath));

        unlink("$contextPath/$filePath");
        $this->assertTrue(rmdir("$contextPath/runtime"));
    }

    // /** @test */
    // public function appendFileContentsException(): void
    // {
    //     $this->markTestIncomplete();
    // }
}
