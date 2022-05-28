<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Path;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /** @test */
    public function getDirectory(): void
    {
        $instance = new Path('../level-two/level-one/filename.txt');
        $this->assertEquals('../level-two/level-one', $instance->getDirectory());
        $this->assertEquals('../level-two', $instance->getDirectory(2));
        $this->assertEquals('..', $instance->getDirectory(3));
        $this->assertEquals('', $instance->getDirectory(4));
    }

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

    /**
     * @test
     * @dataProvider absolutePathList
    */
    public function isNotRelativePath(string $path): void
    {
        $instance = new Path($path);
        $this->assertFalse($instance->isRelativePath());
    }

    /** @return array<mixed> */
    public function externalPathList(): array
    {
        return [
            'admin' => [ 'admin://etc/default/grub' ],
            'app' => [ 'app://com.foo.bar/index.html' ],
            'file' => [ 'file://host.com/file.txt' ],
            'ftp' => [ 'ftp://host.com' ],
            'http' => [ 'http://host.com' ],
            'imap' => [ 'imap://ricardo@host.com/command' ],
            'jar' => [ 'jar:user-naitis' ],
            'javascript' => [ 'javascript:alert(1)' ],
            'jdbc' => [ 'jdbc:mysql://host:port/database?params' ],
            'ldap' => [ 'ldap://ldap1.example.net:6666/o=University%20of%20Michigan' ],
            'mailto' => [ 'mailto:jsmith@example.com?subject=A%20Test&body=My%20idea%20is%3A%20%0A' ],
            'nntp' => [ 'nntp://host.com:55' ],
            'pop' => [ 'pop://ricardo' ],
            'proxy' => [ 'proxy:option=value' ],
            'rsync' => [ 'rsync://host.com:55' ],
            'rmi' => [ 'rmi://host.com' ],
            's3' => [ 's3://mybucket/puppy.jpg' ],
            'smb' => [ 'smb://workgroup;user:password@server/share/folder/file.txt' ],
            'sms' => [ 'sms:+15105550101?body=hello%20there' ],
            'ssh' => [ 'ssh://ricardo@host.com' ],
            'telnet' => [ 'telnet://ricardo:senha@host.com' ],
            'trueconf' => [ 'trueconf:target@server.com' ],
            'udp' => [ 'udp://host.com:55' ],
            'urn' => [ 'urn:isbn:0451450523' ],
            'vnc' => [ 'vnc://ricardo@host.com' ],
            'ws' => [ 'ws:hierarchical' ],
            'xmpp' => [ 'xmpp:ricardo@host.com', ]
        ];
    }

    /**
     * @test
     * @dataProvider externalPathList
    */
    public function isNotLocalPath(string $path): void
    {
        $instance = new Path($path);
        $this->assertFalse($instance->isLocalPath());
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
     * @dataProvider relativePathList
     * @dataProvider absolutePathList
    */
    public function isLocalPath(string $path): void
    {
        $instance = new Path($path);
        $this->assertTrue($instance->isLocalPath());
    }

    /** @return array<mixed> */
    public function contextualizedPathList(): array
    {
        return [
            'level zero' => [
                __DIR__ . '/structure',
                'file-zero.txt',
                __DIR__ . '/structure/file-zero.txt'
            ],
            'level one' => [
                __DIR__ . '/structure/level-one',
                'file-one.txt',
                __DIR__ . '/structure/level-one/file-one.txt'
            ],
            'level two' => [
                __DIR__ . '/structure/level-one',
                'level-two/file-two.txt',
                __DIR__ . '/structure/level-one/level-two/file-two.txt'
            ],
            'relative resolution' => [
                __DIR__ . '/structure/level-one',
                'level-two/../file-one.txt',
                __DIR__ . '/structure/level-one/file-one.txt'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider contextualizedPathList
     */
    public function getRealPath(string $contextPath, string $path, string $fullPath): void
    {
        $instance = new Path($path);
        $this->assertEquals($fullPath, $instance->getRealPath($contextPath));
    }

    /** @test */
    public function getRealPathWithoutContext(): void
    {
        $instance = new Path(__DIR__ . '/structure/level-one');
        $this->assertEquals(__DIR__ . '/structure/level-one', $instance->getRealPath());
    }

    /** @test */
    public function getRealPathWithoutContextException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The path without context must be absolute');

        $instance = new Path('structure/level-one');
        $instance->getRealPath();
    }

    /** @return array<mixed> */
    public function decontextualizedPathList(): array
    {
        return [
            'relative out of context' => [ __DIR__ . '/structure/level-one/level-two', '../file-one.txt' ],
            'path inexistent' => [ __DIR__ . '/structure/level-one', '/dir/level-two/file-two.txt' ],
        ];
    }

    /**
     * @test
     * @dataProvider decontextualizedPathList
     */
    public function getRealPathException(string $contextPath, string $path): void
    {
        $fullPath = $contextPath
            . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "The given path '{$fullPath}' is out of context '{$contextPath}'"
        );

        $instance = new Path($path);
        $instance->getRealPath($contextPath);
    }

    /** @test */
    public function getRealPathAbsoluteException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The context path must be absolute');

        $instance = new Path('any/path');
        $instance->getRealPath('/../path');
    }

    // /** @test */
    // public function getRealPathLocalException(): void
    // {
    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage('The context path must be local');

    //     $instance = new Path('any/path');
    //     $instance->getRealPath('http://host.com');
    // }

    /** @return array<mixed> */
    public function formatRealPathList(): array
    {
        return [
            [ __DIR__ . '/structure', 'level-one' ],
            [ __DIR__ . '/structure', '/level-one' ],
            [ __DIR__ . '/structure', 'level-one/' ],
            [ __DIR__ . '/structure', '/level-one/' ],

            [ __DIR__ . '/structure/', 'level-one' ],
            [ __DIR__ . '/structure/', '/level-one' ],
            [ __DIR__ . '/structure/', 'level-one/' ],
            [ __DIR__ . '/structure/', '/level-one/' ],
        ];
    }

    /**
     * @test
     * @dataProvider formatRealPathList
     */
    public function formatRealPath(string $contextPath, string $path): void
    {
        $instance = new Path($path);
        $this->assertEquals(__DIR__ . '/structure/level-one', $instance->getRealPath($contextPath));
    }
}
