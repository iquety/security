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
            '../level-two/level-one',
            'filename.txt',
            'filename',
            'txt',
        ];

        $list[] = [
            '../level-two/level-one/filename',
            '../level-two/level-one',
            'filename',
            'filename',
            '',
        ];

        $list[] = [
            '../level-two',
            '..',
            'level-two',
            'level-two',
            '',
        ];

        $list[] = [
            '../',
            '', // dir
            '', // file
            '../', // name
            '', // ext
        ];

        $list[] = [
            '.',
            '', // dir
            '', // file
            '.', // name
            '', // ext
        ];

        $list[] = [
            '',
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
        string $path,
        string $directory,
        string $file,
        string $name,
        string $extension
    ): void {
        $instance = new Path($path);

        $this->assertEquals($directory, $instance->getDirectory());
        $this->assertEquals($file, $instance->getFile());
        $this->assertEquals($name, $instance->getName());
        $this->assertEquals($extension, $instance->getExtension());
    }
}
