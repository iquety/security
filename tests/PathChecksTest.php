<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use PHPUnit\Framework\TestCase;

class PathChecksTest extends TestCase
{
    /** @return array<mixed> */
    public function relativePathList(): array
    {
        return [
            [ '../' ],
            [ '..' ],
            [ '..' . __DIR__ . '/structure' ],
            [ __DIR__ . '/./structure' ],
            [ __DIR__ . '/../structure' ],
            [ __DIR__ . '/structure/./' ],
            [ __DIR__ . '/structure/../' ],
            [ __DIR__ . '/structure/..' ],
            [ 'level-two/../file-one.txt' ],
        ];
    }

    /**
     * @test
     * @dataProvider relativePathList
    */
    public function isRelativePath(string $path): void
    {
        $instance = new Path($path);
        $this->assertTrue($instance->isRelativePath());
    }

    /** @return array<mixed> */
    public function absolutePathList(): array
    {
        return [
            [ '/' ],
            [ __DIR__ ],
            [ __DIR__ . '/structure' ],
            [ __DIR__ . '/structure/level-one' ],
            [ __DIR__ . '/structure/level-one/level-two' ],
        ];
    }

    /**
     * @test
     * @dataProvider absolutePathList
    */
    public function isNotRelativePath(string $path): void
    {
        $instance = new Path($path);
        $this->assertFalse($instance->isRelativePath());
    }

    /**
     * @test
     * @dataProvider relativePathList
     * @dataProvider absolutePathList
    */
    public function isLocalPath(string $path): void
    {
        $instance = new Path($path);
        $this->assertTrue($instance->isLocalPath());
    }
}
