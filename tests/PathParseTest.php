<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use PHPUnit\Framework\TestCase;

class PathParseTest extends TestCase
{
    /**
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function pathProvider(): array
    {
        $list = [];

        $list['file relative with two levels and extension'] = [
            '../level-two/level-one/filename.txt', // context
            '../level-two/level-one/filename.txt', // path
            '../level-two/level-one', // dir
            'filename.txt', // file
            'filename', // name
            'txt', // ext
        ];

        $list['file absolute with two levels and extension'] = [
            'level-two/level-one/filename.txt', // context
            'level-two/level-one/filename.txt', // path
            'level-two/level-one', // dir
            'filename.txt', // file
            'filename', // name
            'txt', // ext
        ];

        $list['file relative with two levels without extension'] = [
            '../level-two/level-one/filename', // context
            '../level-two/level-one/filename', // path
            '../level-two/level-one', // dir
            'filename', // file
            'filename', // name
            '', // ext
        ];

        $list['file absolute with two levels without extension'] = [
            'level-two/level-one/filename', // context
            'level-two/level-one/filename', // path
            'level-two/level-one', // dir
            'filename', // file
            'filename', // name
            '', // ext
        ];

        $list['directory relative with two levels'] = [
            '../level-two/level-one/dirname/', // context
            '../level-two/level-one/dirname', // path
            '../level-two/level-one', // dir
            'dirname', // file
            'dirname', // name
            '', // ext
        ];

        $list['directory absolute with two levels'] = [
            'level-two/level-one/dirname/', // context
            'level-two/level-one/dirname', // path
            'level-two/level-one', // dir
            'dirname', // file
            'dirname', // name
            '', // ext
        ];

        $list['directory relative with single level'] = [
            '../level-two', // context
            '../level-two', // path
            '..', // dir
            'level-two', // file
            'level-two', // name
            '', // ext
        ];

        $list['directory absolute with single level'] = [
            'level-two', // context
            'level-two', // path
            '.', // dir
            'level-two', // file
            'level-two', // name
            '', // ext
        ];

        $list['empty directory relative'] = [
            '../', // context
            '..', // path
            '.', // dir
            '..', // file
            '.', // name
            '', // ext
        ];

        $list['empty file relative'] = [
            '.', // context
            '.', // path
            '.', // dir
            '.', // file
            '', // name
            '', // ext
        ];

        $list['empty'] = [
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
