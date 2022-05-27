<?php

declare(strict_types=1);

namespace Freep\Security;

use InvalidArgumentException;

class Filesystem
{
    private string $contextPath = '';

    public function __construct(string $contextPath)
    {
        if ($this->isRelativePath($contextPath) === true) {
            throw new InvalidArgumentException('The context path must be absolute');
        }

        if ($this->isLocalPath($contextPath) === false) {
            throw new InvalidArgumentException('The context path must be local');
        }

        $this->contextPath = trim($contextPath);
        $this->contextPath = rtrim($contextPath, DIRECTORY_SEPARATOR);
    }

    public function getContextPath(): string
    {
        return $this->contextPath;
    }

    /** @return array<int,string> */
    public function getDirectoryContents(string $path): array
    {
        $path = $this->getRealPath($path);

        $scanned = scandir($path);

        $list = [];

        foreach ($scanned as $relativePath) {
            if (in_array($relativePath, ['.',  '..']) === true) {
                continue;
            }

            $list[] = $path . DIRECTORY_SEPARATOR . $relativePath; 
        }

        return $list;
    }
    
    public function getFileContents(string $path): string
    {
        $path = $this->getRealPath($path);

        return file_get_contents($path);
    }

    /** @return array<int,string> */
    public function getFileRows(string $path): array
    {
        $path = $this->getRealPath($path);

        return array_map(fn($row) => trim($row), file($path)) ?: [];
    }

    public function getRealPath(string $relativePath): string
    {
        $securePath = $this->getContextPath() 
            . DIRECTORY_SEPARATOR 
            . ltrim($relativePath, DIRECTORY_SEPARATOR);

        $realPath = realpath($securePath);

        if ($realPath === false // caminho sem resolução
         || strpos($realPath, $this->getContextPath()) === false // caminho acima do contexto
        ) {
            throw new InvalidArgumentException('The given path is out of context');
        }

        return $realPath;
    }

    public function isDirectory(string $path): bool
    {
        try {
            $path = $this->getRealPath($path);
        } catch(InvalidArgumentException) {
            return false;
        }

        return is_dir($path);
    }

    public function isFile(string $path): bool
    {
        try {
            $path = $this->getRealPath($path);
        } catch(InvalidArgumentException) {
            return false;
        }

        return is_file($path);
    }

    /** @see https://en.wikipedia.org/wiki/List_of_URI_schemes */
    public function isLocalPath(string $path): bool
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

        if (preg_match("/^({$blockedProtocols})/", $path, $matches) === 1) {
            return false;
        }

        return true;
    }

    public function isRelativePath(string $path): bool
    {
        return preg_match('#(\.\.)|(\./)#', $path) === 1;
    }

    public function isReadable(string $path): bool
    {
        try {
            $path = $this->getRealPath($path);
        } catch(InvalidArgumentException) {
            return false;
        }

        return is_readable($path);
    }

    public function isWritable(string $path): bool
    {
        try {
            $path = $this->getRealPath($path);
        } catch(InvalidArgumentException) {
            return false;
        }

        return is_writable($path);
    }

    /**
     * As permissões funcionam de forma octal, somando os valores para obter a
     * configuração desejada, onde 4 = Ler, 2 = Escrever e 1 = Executar.
     * 
     * Por exemplo: 
     * 4 = Ler
     * 5 = Ler (4) + Executar (1)
     * 6 = Ler (4) + Escrever (2)
     * 7 = Ler (4) + Escrever (2) + Executar (7)
     * 
     * O argumento $octalPermissions deve ser formado por 4 digitos, por ex: 0765,
     * onde 7 para o dono do arquivo, 6 para o grupo e 5 para os outros usuários
     * 
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function changePermissions(string $path, int $octalPermissions = 0755): void
    {
        if (chmod($path, $octalPermissions) === false) {
            throw new InvalidArgumentException(
                'Could not change requested object permissions'
            );
        }
    }

    public function makeDirectory(string $path): void
    {
        if ($this->isDirectory($path) === true) {
            return;
        }

        if ($this->isLocalPath($path) === false) {
            throw new InvalidArgumentException(
                'The path specified for the directory to be created is invalid'
            );
        }

        if ($this->isRelativePath($path) === true) {
            throw new InvalidArgumentException(
                'The directory path to be created cannot be relative'
            );
        }

        $fullpath = $this->getContextPath() . DIRECTORY_SEPARATOR . $path;

        if (mkdir($fullpath, 0777, true) === false) {
            throw new InvalidArgumentException(
                'The relevant permissions prevent creating the directory'
            );
        }
    }

    public function setFileContents(string $path, string $contents): void
    {
        $path = $this->getRealPath($path);

        $this->touchFile($path, $contents);
    }

    public function appendFileContents(string $path, string $contents): void
    {
        $path = $this->getRealPath($path);

        $this->touchFile($path, $contents, FILE_APPEND);
    }

    /**
     * @see https://www.php.net/manual/pt_BR/function.file-put-contents.php
     */
    private function touchFile(string $path, string $contents, int $flag = 0): void
    {
        $path = $this->getRealPath($path);

        if (file_put_contents($path, $contents, $flag) === false) {
            throw new InvalidArgumentException('Could not add data to file');
        }
    }
}
