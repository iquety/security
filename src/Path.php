<?php

declare(strict_types=1);

namespace Freep\Security;

use InvalidArgumentException;

class Path
{
    private string $path = '';

    public function __construct(string $path)
    {
        $this->path = trim($path);
    }

    public function getRealPath(string $contextPath = ''): string
    {
        $messageAbsolute = 'The context path must be absolute';

        if ($contextPath === '') {
            $contextPath = $this->path;
            $messageAbsolute = 'The path without context must be absolute';
        }

        $context = new Path($contextPath);

        if ($context->isRelativePath() === true) {
            throw new InvalidArgumentException($messageAbsolute);
        }

        // if ($context->isLocalPath() === false) {
        //     throw new InvalidArgumentException('The context path must be local');
        // }

        $securePath = $this->getSecurePath($contextPath, $this->path);

        $realPath = realpath($securePath);

        if (
            $realPath === false // caminho sem resolução
            || strpos($realPath, $contextPath) === false // caminho acima do contexto
        ) {
            throw new InvalidArgumentException('The given path is out of context');
        }

        return $realPath;
    }

    private function getSecurePath(string $contextPath, string $path): string
    {
        if ($contextPath === $path) {
            return $path;
        }

        return $contextPath
            . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);
    }

    /** @see https://en.wikipedia.org/wiki/List_of_URI_schemes */
    public function isLocalPath(): bool
    {
        $blockedProtocols = implode('|', [
            'admin:',
            'app:',
            'file:',
            'ftp:',
            'http:',
            'imap:',
            'jar:',
            'javascript:',
            'jdbc:',
            'ldap:',
            'mailto:',
            'nntp:',
            'pop:',
            'proxy:',
            'rsync:',
            'rmi:',
            's3:',
            'smb:',
            'sms:',
            'ssh:',
            'telnet:',
            'trueconf:',
            'udp:',
            'urn:',
            'vnc:',
            'ws:',
            'xmpp:',
        ]);

        $matches = [];
        if (preg_match("/^({$blockedProtocols})/", $this->path, $matches) === 1) {
            return false;
        }

        return true;
    }

    public function isRelativePath(): bool
    {
        return preg_match('#(\.\.)|(\./)#', $this->path) === 1
            || $this->path[0] !== '/';
    }
}
