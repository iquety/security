<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PathGetAbsolutePathTest extends TestCase
{
    /** @return array<mixed> */
    public function pathProvider(): array
    {
        $list = [];

        // caminhos com inicio relativo
        $list[] = [ 'tests/structure',   '', __DIR__ . '/structure' ];
        $list[] = [ './tests/structure', '', __DIR__ . '/structure' ];

        // caminhos com inicio relativo + nó
        $list[] = [ 'tests',   'structure', __DIR__ . '/structure' ];
        $list[] = [ './tests', 'structure', __DIR__ . '/structure' ];

        // caminhos com nucleo relativo
        $list[] = [ __DIR__ . '/./structure' ,        '',  __DIR__ . '/structure' ];
        $list[] = [ __DIR__ . '/../tests/structure' , '',  __DIR__ . '/structure' ];

        // caminhos com nó relativo
        $list[] = [ __DIR__ , '/./structure',        __DIR__ . '/structure' ];
        $list[] = [ __DIR__ , '/../tests/structure', __DIR__ . '/structure' ];

        // caminho absolutos
        $list[] = [ __DIR__ . '/structure', '', __DIR__ . '/structure' ];

        // caminho absolutos + nó
        $list[] = [ __DIR__, 'structure', __DIR__ . '/structure' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider pathProvider
     */
    public function getAbsolutePath(string $path, string $node, string $result): void
    {
        $instance = new Path($path);
        $instance->addNodePath($node);

        $this->assertEquals($result, $instance->getAbsolutePath());
    }

    /** @return array<mixed> */
    public function invalidPathProvider(): array
    {
        $list = [];

        // caminhos com inicio relativo invalido
        $list[] = [ 'structure',   '', __DIR__ . '/structure' ];
        $list[] = [ '../structure', '', __DIR__ . '/structure' ];

        // caminhos com nó relativo invalido
        $list[] = [ __DIR__ , '/./struct',        __DIR__ . '/structure' ];
        $list[] = [ __DIR__ , '/tests/../struct', __DIR__ . '/structure' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidPathProvider
     */
    public function getAbsolutePathException(string $path, string $node): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches("/The '.*' path cannot be resolved/");

        $instance = new Path($path);
        $instance->addNodePath($node);

        $instance->getAbsolutePath();
    }

    /** @return array<mixed> */
    public function invalidContextPathProvider(): array
    {
        $list = [];

        // caminhos que se resolvem acima do contexto
        // devem ser bloqueados com uma exceção
        $list[] = [ __DIR__ ,  '../' ];
        $list[] = [ __DIR__ . '/structure',  '../' ];
        $list[] = [ __DIR__ . '/structure',  'level-one/../../' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidContextPathProvider
     */
    public function getAbsolutePathContextException(string $path, string $node): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches(
            "/Cannot get absolute path outside the scope of the '.*' context/"
        );

        $instance = new Path($path);
        $instance->addNodePath($node);

        $instance->getAbsolutePath();
    }
}
