<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FilesystemFilesTest extends TestCase
{
    /** @return array<int,array<int,string>> */
    public function fileContentsProvider(): array
    {
        $list = [];

        $list[] = [__DIR__, __DIR__ . '/structure/file-zero.txt'];
        $list[] = [__DIR__ . '/structure', 'file-zero.txt'];
        $list[] = [__DIR__ , '/structure/file-zero.txt'];
        $list[] = [__DIR__ , 'structure/file-zero.txt'];

        return $list;
    }

    /**
     * @test
     * @dataProvider fileContentsProvider
     */
    public function getFileRows(string $contextPath, string $filePath): void
    {
        $instance = new Filesystem($contextPath);
        $this->assertEquals([
            '000   ', '   111', '222', '333', '444', '555', '666', '777', '888', '999'
        ], $instance->getFileRows($filePath));
    }

    /** @test */
    public function getFileRowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File ' . __DIR__ . '/structure/not-exists.txt does not exist');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->getFileRows('not-exists.txt');
    }

    /**
     * @test
     * @dataProvider fileContentsProvider
     */
    public function getFileContents(string $contextPath, string $filePath): void
    {
        $instance = new Filesystem($contextPath);

        $this->assertEquals(
            "000   \n   111\n222\n333\n444\n555\n666\n777\n888\n999",
            $instance->getFileContents($filePath)
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

    /** @return array<int,array<int,string>> */
    public function isFileProvider(): array
    {
        $list = [];

        $list[] = [__DIR__, __DIR__ . '/structure/level-one/file-one.txt'];
        $list[] = [__DIR__ . '/', __DIR__ . '/structure/level-one/file-one.txt'];

        $list[] = [__DIR__, 'structure/level-one/file-one.txt'];
        $list[] = [__DIR__, '/structure/level-one/file-one.txt'];
        $list[] = [__DIR__ . '/', 'structure/level-one/file-one.txt'];
        $list[] = [__DIR__ . '/', '/structure/level-one/file-one.txt'];

        $list[] = [__DIR__ . '/structure', 'level-one/file-one.txt'];
        $list[] = [__DIR__ . '/structure', '/level-one/file-one.txt'];
        $list[] = [__DIR__ . '/structure/', 'level-one/file-one.txt'];
        $list[] = [__DIR__ . '/structure/', '/level-one/file-one.txt'];

        $list[] = [__DIR__ . '/structure/level-one', 'file-one.txt'];
        $list[] = [__DIR__ . '/structure/level-one', '/file-one.txt'];
        $list[] = [__DIR__ . '/structure/level-one/', 'file-one.txt'];
        $list[] = [__DIR__ . '/structure/level-one/', '/file-one.txt'];

        $list[] = [__DIR__ . '/structure/level-one/level-two', 'file-two.txt'];
        $list[] = [__DIR__ . '/structure/level-one/level-two', '/file-two.txt'];
        $list[] = [__DIR__ . '/structure/level-one/level-two/', 'file-two.txt'];
        $list[] = [__DIR__ . '/structure/level-one/level-two/', '/file-two.txt'];

        return $list;
    }

    /**
     * @test
     * @dataProvider isFileProvider
     */
    public function isFile(string $contextPath, string $filePath): void
    {
        $instance = new Filesystem($contextPath);
        $this->assertTrue($instance->isFile($filePath));
    }

    /** @test */
    public function isNotFile(): void
    {
        $instance = new Filesystem(__DIR__ . '/structure/level-one');
        $this->assertFalse($instance->isFile('file-nop.txt'));
    }

    /** @return array<int,array<int,string>> */
    public function setFileContentsProvider(): array
    {
        $list = [];

        $list[] = [__DIR__, __DIR__ . '/structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/', __DIR__ . '/structure/runtime/set-file-contents.txt'];

        $list[] = [__DIR__, '/structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__, 'structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/', '/structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/', 'structure/runtime/set-file-contents.txt'];

        $list[] = [__DIR__ . '/structure', 'runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure', '/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/', 'runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/', '/runtime/set-file-contents.txt'];

        $list[] = [__DIR__ . '/structure/runtime', 'set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/runtime', '/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/runtime/', 'set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/runtime/', '/set-file-contents.txt'];

        $list[] = [__DIR__ . '/structure', __DIR__ . '/structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/', __DIR__ . '/structure/runtime/set-file-contents.txt'];

        $list[] = [__DIR__ . '/structure/runtime', __DIR__ . '/structure/runtime/set-file-contents.txt'];
        $list[] = [__DIR__ . '/structure/runtime/', __DIR__ . '/structure/runtime/set-file-contents.txt'];

        return $list;
    }

    /**
     * @test
     * @dataProvider setFileContentsProvider
     */
    public function setFileContents(string $contextPath, string $filePath): void
    {
        $instance = new Filesystem($contextPath);

        $instance->setFileContents($filePath, 'contents');
        $this->assertEquals('contents', $instance->getFileContents($filePath));

        $instance->setFileContents($filePath, 'naitis');
        $this->assertEquals('naitis', $instance->getFileContents($filePath));

        unlink(__DIR__ . "/structure/runtime/set-file-contents.txt");
    }

    /** @test */
    public function setFileContentsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file path path must be absolute');

        $instance = new Filesystem(__DIR__ . '/structure');
        $instance->setFileContents('runtime/../../set-file-contents.txt', 'naitis');
    }

    /**
     * @test
     * @dataProvider setFileContentsProvider
     */
    public function appendFileContents(string $contextPath, string $filePath): void
    {
        $instance = new Filesystem($contextPath);

        $instance->appendFileContents($filePath, 'contents');
        $this->assertEquals('contents', $instance->getFileContents($filePath));

        $instance->appendFileContents($filePath, 'naitis');
        $this->assertEquals('contentsnaitis', $instance->getFileContents($filePath));

        unlink(__DIR__ . "/structure/runtime/set-file-contents.txt");
    }

    // /** @test */
    // public function appendFileContentsException(): void
    // {
    //     $this->markTestIncomplete();
    // }
}
