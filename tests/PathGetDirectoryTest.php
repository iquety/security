<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Security\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PathGetDirectoryTest extends TestCase
{
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

    /** @test */
    public function getDirectoryInvalidLevel(string $path, string $node, int $levels): void
    {
        $instance = new Path($path);
        $instance->addNodePath($node);

        $instance->getDirectory($levels);
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
