<?php

declare(strict_types=1);

namespace Freep\Security;

use InvalidArgumentException;

class Path
{
    private string $path = '';

    /** @var array<string,string> */
    private array $info = [];

    public function __construct(string $path)
    {
        $this->path = trim($path);

        $this->parsePathInfo($this->path);
    }

    private function parsePathInfo(string $path): void
    {
        if ($path === '') {
            $this->info = [
                'directory' => '',
                'file'      => '',
                'name'      => '',
                'extension' => ''
            ];
            return;
        }

        $pathInfo = (array)pathinfo($path);

        if ($pathInfo['dirname'] === '.') {
            $this->info = [
                'directory' => '',
                'file'      => '',
                'name'      => $path,
                'extension' => ''
            ];
            return;
        }

        $this->info = [
            'directory' => $pathInfo['dirname'],
            'file'      => $pathInfo['basename'],
            'name'      => $pathInfo['filename'],
            'extension' => $pathInfo['extension'] ?? ''
        ];
    }

    public function getDirectory(int $levels = 1): string
    {
        if ($levels === 1) {
            return $this->info['directory'];
        }

        $dirname = dirname($this->path, $levels);

        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }

    public function getExtension(): string
    {
        return $this->info['extension'];
    }

    public function getFile(): string
    {
        return $this->info['file'];
    }

    public function getName(): string
    {
        return $this->info['name'];
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

        $securePath = $this->getSecurePath($contextPath, $this->path);

        $realPath = realpath($securePath);

        if (
            $realPath === false // caminho sem resolução
            || strpos($realPath, $contextPath) === false // caminho acima do contexto
        ) {
            throw new InvalidArgumentException("The given path '$securePath' is out of context '$contextPath'");
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
