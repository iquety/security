<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Security\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /** @return array<mixed> */
    public function nonAbsolutePathList(): array
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
            [ 'admin://etc/default/grub' ],
            [ 'app://com.foo.bar/index.html' ],
            [ 'file://host.com/file.txt' ],
            [ 'ftp://host.com' ],
            [ 'http://host.com' ],
            [ 'imap://ricardo@host.com/command' ],
            [ 'jar:user-naitis' ],
            [ 'javascript:alert(1)' ],
            [ 'jdbc:mysql://host:port/database?params' ],
            [ 'ldap://ldap1.example.net:6666/o=University%20of%20Michigan' ],
            [ 'mailto:jsmith@example.com?subject=A%20Test&body=My%20idea%20is%3A%20%0A' ],
            [ 'nntp://host.com:55' ],
            [ 'pop://ricardo' ],
            [ 'proxy:option=value' ],
            [ 'rsync://host.com:55' ],
            [ 'rmi://host.com' ],
            [ 's3://mybucket/puppy.jpg' ],
            [ 'smb://workgroup;user:password@server/share/folder/file.txt' ],
            [ 'sms:+15105550101?body=hello%20there' ],
            [ 'ssh://ricardo@host.com' ],
            [ 'telnet://ricardo:senha@host.com' ],
            [ 'trueconf:target@server.com' ],
            [ 'udp://host.com:55' ],
            [ 'urn:isbn:0451450523' ],
            [ 'vnc://ricardo@host.com' ],
            [ 'ws:hierarchical' ],
            [ 'xmpp:ricardo@host.com', ]
        ];
    }

    /**
     * @test
     * @dataProvider nonAbsolutePathList
     */
    public function contextAbsolutePathException(string $path): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The context path must be absolute');

        new Filesystem($path);
    }
}
