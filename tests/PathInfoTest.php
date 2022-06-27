<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use PHPUnit\Framework\TestCase;

class PathInfoTest extends TestCase
{
    /** @return array<mixed> */
    public function pathList(): array
    {
        $list = [];

        $list[] = [
            '../level-two/level-one/filename.txt',
            '../level-two/level-one/filename.txt', // path
            '../level-two/level-one', // dir
            'filename.txt', // file
            'filename', // name
            'txt', // ext
        ];

        $list[] = [
            '../level-two/level-one/filename',
            '../level-two/level-one/filename', // path
            '../level-two/level-one', // dir
            'filename', // file
            'filename', // name
            '', // ext
        ];

        $list[] = [
            '../level-two/level-one/dirname/',
            '../level-two/level-one/dirname', // path
            '../level-two/level-one', // dir
            'dirname', // file
            'dirname', // name
            '', // ext
        ];

        $list[] = [
            '../level-two',
            '../level-two', // path
            '..', // dir
            'level-two', // file
            'level-two', // name
            '', // ext
        ];

        $list[] = [
            '../',
            '..', // path
            '', // dir
            '', // file
            '../', // name
            '', // ext
        ];

        $list[] = [
            '.',
            '.', // path
            '', // dir
            '', // file
            '.', // name
            '', // ext
        ];

        $list[] = [
            '',
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
     * @dataProvider pathList
    */
    public function directory(
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
}
