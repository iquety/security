<?php

declare(strict_types=1);

namespace Tests;

use Freep\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemPathsTest extends TestCase
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
        ];
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
     * @dataProvider relativePathList
    */
    public function isRelativePath(string $path): void
    {
        $instance = new Filesystem(__DIR__);
        $this->assertTrue($instance->isRelativePath($path));
    }

    /**
     * @test 
     * @dataProvider absolutePathList
    */
    public function isNotRelativePath(string $path): void
    {
        $instance = new Filesystem(__DIR__);
        $this->assertFalse($instance->isRelativePath($path));
    }

    /**
     * @test 
     * @dataProvider externalPathList
    */
    public function isNotLocalPath(string $path): void
    {
        $instance = new Filesystem(__DIR__);
        $this->assertFalse($instance->isLocalPath($path));
    }

    /**
     * @test 
     * @dataProvider relativePathList
     * @dataProvider absolutePathList
    */
    public function isLocalPath(string $path): void
    {
        $instance = new Filesystem(__DIR__);
        $this->assertTrue($instance->isLocalPath($path));
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
            ],
        ];
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
     * @dataProvider contextualizedPathList
     */
    public function getRealPath(string $contextPath, string $path, string $fullPath): void
    {
        $instance = new Filesystem($contextPath);
        $this->assertEquals($fullPath, $instance->getRealPath($path));
    }

    /**
     * @test
     * @dataProvider decontextualizedPathList
     */
    public function getRealPathException(string $contextPath, string $path): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given path is out of context');

        $instance = new Filesystem($contextPath);
        $instance->getRealPath($path);
    }

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
        $instance = new Filesystem($contextPath);
        $this->assertEquals(__DIR__ . '/structure/level-one', $instance->getRealPath($path));
    }

    /** @return array<mixed> */
    public function formatContextPathList(): array
    {
        return [
            [ __DIR__ . '/structure' ],
            [ __DIR__ . '/structure/' ],
        ];
    }

    /** 
     * @test
     * @dataProvider formatContextPathList
     */
    public function contextPath(string $contextPath): void
    {
        $instance = new Filesystem($contextPath);
        $this->assertEquals(__DIR__ . '/structure', $instance->getContextPath());
    }
}
