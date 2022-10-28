<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Security\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PathAddNodePathTest extends TestCase
{
    /** @return array<mixed> */
    public function nodeProvider(): array
    {
        $list = [];

        // barra no contexto
        $list[] = [ 'dir/',   'file.txt', 'dir/file.txt'   ];
        $list[] = [ './dir/', 'file.txt', './dir/file.txt' ];
        $list[] = ['../dir/', 'file.txt', '../dir/file.txt' ];
        $list[] = ['../', 'file.txt', '../file.txt' ];
        $list[] = ['./', 'file.txt', './file.txt' ];
        $list[] = ['/', 'file.txt', '/file.txt' ];

        // barra no nó
        $list[] = [ 'dir',   '/file.txt', 'dir/file.txt'   ];
        $list[] = [ './dir', '/file.txt', './dir/file.txt' ];
        $list[] = ['../dir', '/file.txt', '../dir/file.txt' ];
        $list[] = ['..', '/file.txt', '../file.txt' ];
        $list[] = ['.', '/file.txt', './file.txt' ];
        $list[] = ['', '/file.txt', '/file.txt' ];

        // barras em ambos
        $list[] = [ 'dir/',   '/file.txt', 'dir/file.txt'   ];
        $list[] = [ './dir/', '/file.txt', './dir/file.txt' ];
        $list[] = ['../dir/', '/file.txt', '../dir/file.txt' ];
        $list[] = ['../', '/file.txt', '../file.txt' ];
        $list[] = ['./', '/file.txt', './file.txt' ];
        $list[] = ['/', '/file.txt', '/file.txt' ];

        // sem barras
        $list[] = [ 'dir',   'file.txt', 'dir/file.txt'   ];
        $list[] = [ './dir', 'file.txt', './dir/file.txt' ];
        $list[] = ['../dir', 'file.txt', '../dir/file.txt' ];
        $list[] = ['..', 'file.txt', '../file.txt' ];
        $list[] = ['.', 'file.txt', './file.txt' ];
        $list[] = ['', 'file.txt', 'file.txt' ];

        $list[] = ['', '', '' ];

        $list[] = ['dir', '', 'dir' ];
        $list[] = ['./dir', '', './dir' ];
        $list[] = ['../dir', '', '../dir' ];
        $list[] = ['.', '', '.' ];
        $list[] = ['..', '', '..' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider nodeProvider
    */
    public function addNodePath(
        string $context,
        string $subpath,
        string $fullpath,
    ): void {
        $instance = new Path($context);
        $instance->addNodePath($subpath);

        $this->assertEquals($fullpath, $instance->getPath());
    }

    /** @test */
    public function addNodePathFlow(): void
    {
        $instance = new Path(__DIR__);
        $instance->addNodePath('dir/subdir');

        $this->assertEquals(__DIR__ . '/dir/subdir', $instance->getPath());

        $instance->addNodePath('deep/file.txt');

        $this->assertEquals(__DIR__ . '/dir/subdir/deep/file.txt', $instance->getPath());
    }

    /** @test */
    public function addNodePathException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot add a new node to a file path');

        $instance = new Path('dir/subdir/file.txt');
        $instance->addNodePath('new/nodes');
    }
}
