<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use PHPUnit\Framework\TestCase;

class PathParseTest extends TestCase
{
    /** @return array<mixed> */
    public function pathProvider(): array
    {
        $list = [];

        $list[] = [
            '../level-two/level-one/filename.txt', // context
            '../level-two/level-one/filename.txt', // path
            '../level-two/level-one', // dir
            'filename.txt', // file
            'filename', // name
            'txt', // ext
        ];

        $list[] = [
            '../level-two/level-one/filename', // context
            '../level-two/level-one/filename', // path
            '../level-two/level-one', // dir
            'filename', // file
            'filename', // name
            '', // ext
        ];

        $list[] = [
            '../level-two/level-one/dirname/', // context
            '../level-two/level-one/dirname', // path
            '../level-two/level-one', // dir
            'dirname', // file
            'dirname', // name
            '', // ext
        ];

        $list[] = [
            '../level-two', // context
            '../level-two', // path
            '..', // dir
            'level-two', // file
            'level-two', // name
            '', // ext
        ];

        $list[] = [
            '../', // context
            '..', // path
            '', // dir
            '', // file
            '..', // name
            '', // ext
        ];

        $list[] = [
            '.', // context
            '.', // path
            '', // dir
            '', // file
            '.', // name
            '', // ext
        ];

        $list[] = [
            '', // context
            '', // path
            '', // dir
            '', // file
            '', // name
            '', // ext
        ];

        return $list;
    }

    /**
     * @test
     * @dataProvider pathProvider
    */
    public function pathParse(
        string $originPath,
        string $path,
        string $directory,
        string $file,
        string $name,
        string $extension
    ): void {
        $instance = new Path($originPath);

        $this->assertEquals($path, $instance->getPath());
        $this->assertEquals($directory, $instance->getDirectory());
        $this->assertEquals($file, $instance->getFile());
        $this->assertEquals($name, $instance->getName());
        $this->assertEquals($extension, $instance->getExtension());
    }

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
        $this->assertEquals(ltrim($subpath, DIRECTORY_SEPARATOR), $instance->getNodePath());
    }
}
