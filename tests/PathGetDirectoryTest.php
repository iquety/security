<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Iquety\Security\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PathGetDirectoryTest extends TestCase
{
    /** @return array<mixed> */
    public function levelsProvider(): array
    {
        $list = [];

        $list[] = [ 0 ];
        $list[] = [ -1 ];
        $list[] = [ -2 ];

        return $list;
    }

    /**
     * @test
     * @dataProvider levelsProvider
     */
    public function getDirectoryInvalidLevel(int $levels): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Directory levels must be greater than zero');

        $instance = new Path('../dir');
        $instance->addNodePath('subdir/file');

        $instance->getDirectory($levels);
    }

    /** @return array<mixed> */
    public function pathProvider(): array
    {
        $list = [];

        $list[] = [ '../dir/subdir/file', '', 1,  '../dir/subdir' ];
        $list[] = [ '../dir/subdir/file', '', 2,  '../dir' ];
        $list[] = [ '../dir/subdir/file', '', 3,  '..' ];
        $list[] = [ './dir/subdir/file', '', 3,  '' ];

        $list[] = [ '../dir/subdir/file', '', 4,  '' ];
        $list[] = [ '../dir/subdir/file', '', 5,  '' ];
        $list[] = [ '../dir/subdir/file', '', 6,  '' ];

        $list[] = [ '../dir', 'subdir/file', 1,  '../dir/subdir' ];
        $list[] = [ '../dir', 'subdir/file', 2,  '../dir' ];
        $list[] = [ './dir', 'subdir/file', 2,  './dir' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider pathProvider
     */
    public function getDirectory(string $path, string $node, int $levels, string $result): void
    {
        $instance = new Path($path);
        $instance->addNodePath($node);

        $this->assertEquals($result, $instance->getDirectory($levels));
    }

    /** @return array<mixed> */
    public function pathContextualizedProvider(): array
    {
        $list = [];

        $list[] = [ '../dir', 'subdir/file', 3 ];
        $list[] = [ './dir', 'subdir/file', 3 ];
        $list[] = [ '../', 'subdir/file', 3 ];

        $list[] = [ '../dir', 'subdir/file', 4 ];
        $list[] = [ '../dir', 'subdir/file', 5 ];

        return $list;
    }

    /**
     * @test
     * @dataProvider pathContextualizedProvider
     */
    public function getDirectoryException(string $path, string $node, int $levels): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Cannot get information from a directory outside the scope of the '$path' context"
        );

        $instance = new Path($path);
        $instance->addNodePath($node);

        $instance->getDirectory($levels);
    }
}
