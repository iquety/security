<?php

declare(strict_types=1);

namespace Iquety\Security;

use InvalidArgumentException;
use RuntimeException;

class Path
{
    private string $contextPath = '';

    private string $nodePath = '';

    /** @var array<string,string> */
    private array $info = [];

    public function __construct(string $contextPath)
    {
        $this->contextPath = trim($contextPath);

        $this->parsePathInfo($this->contextPath);
    }

    private function parsePathInfo(string $path): void
    {
        if (in_array($path, ['', DIRECTORY_SEPARATOR]) === true) {
            $this->info = [
                'path'      => $path,
                'directory' => '',
                'file'      => '',
                'name'      => '',
                'extension' => ''
            ];

            return;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        $pathInfo = (array)pathinfo($path);

        $this->info = [
            'path'      => $path,
            'directory' => $pathInfo['dirname'] ?? '',
            'file'      => $pathInfo['basename'],
            'name'      => $pathInfo['filename'],
            'extension' => $pathInfo['extension'] ?? ''
        ];
    }

    public function addNodePath(string $subpath): self
    {
        if ($subpath === '') {
            return $this;
        }

        if ($this->getExtension() !== '') {
            throw new RuntimeException('Cannot add a new node to a file path');
        }

        $subpath = trim($subpath);

        $this->nodePath .= $this->nodePath === ''
            ? $subpath
            : DIRECTORY_SEPARATOR . $subpath;

        $this->nodePath = ltrim($this->nodePath, DIRECTORY_SEPARATOR);

        $fullPath    = '';

        if ($this->getPath() === '') {
            $fullPath = rtrim($subpath, DIRECTORY_SEPARATOR);
        }

        if ($this->getPath() === DIRECTORY_SEPARATOR) {
            $fullPath = DIRECTORY_SEPARATOR . trim($subpath, DIRECTORY_SEPARATOR);
        }

        if ($fullPath === '') {
            $fullPath = $this->getPath()
                . DIRECTORY_SEPARATOR
                . trim($subpath, DIRECTORY_SEPARATOR);
        }

        $this->parsePathInfo($fullPath);

        return $this;
    }

    public function getAbsolutePath(): string
    {
        $path = $this->getPath();

        $realPath = realpath($path);

        if ($realPath === false) {
            throw new RuntimeException("The '$path' path cannot be resolved");
        }

        if ($this->getNodePath() === '') {
            return $realPath;
        }

        $realContext = (string)realpath($this->getContextPath());

        if (str_starts_with($realPath, $realContext) === false) {
            throw new RuntimeException(
                "Cannot get absolute path outside "
                . "the scope of the '{$realContext}' context"
            );
        }

        return $realPath;
    }

    public function getContextPath(): string
    {
        return $this->contextPath;
    }

    public function getDirectory(int $levels = 1): string
    {
        if ($levels <= 0) {
            throw new InvalidArgumentException('Directory levels must be greater than zero');
        }

        if ($levels === 1) {
            return $this->info['directory'];
        }

        $dirname = dirname($this->getPath(), $levels);

        if ($dirname === '.') {
            $dirname = '';
        }

        if ($this->getNodePath() === '') {
            return $dirname;
        }

        if (str_starts_with($dirname, $this->getContextPath()) === false) {
            throw new RuntimeException(
                "Cannot get information from a directory outside "
                . "the scope of the '{$this->contextPath}' context"
            );
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

    public function getNodePath(): string
    {
        return ltrim($this->nodePath, DIRECTORY_SEPARATOR);
    }

    public function getPath(): string
    {
        return $this->info['path'];
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
        if (preg_match("/^({$blockedProtocols})/", $this->getPath(), $matches) === 1) {
            return false;
        }

        return true;
    }

    public function isRelativePath(): bool
    {
        $path = $this->getPath();

        return preg_match('#(\.\.)|(\./)#', $path) === 1
            || str_starts_with($path, DIRECTORY_SEPARATOR) === false
            || str_ends_with($path, '.') ===  true;
    }
}
