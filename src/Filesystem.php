<?php

declare(strict_types=1);

namespace Freep\Security;

use InvalidArgumentException;
use RuntimeException;

class Filesystem
{
    private string $contextPath = '';

    public function __construct(string $contextPath)
    {
        $context = new Path($contextPath);

        if ($context->isRelativePath() === true) {
            throw new InvalidArgumentException('The context path must be absolute');
        }

        if ($context->isLocalPath() === false) {
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
    public function getDirectoryContents(string $directoryPath): array
    {
        $directory = new Path($directoryPath);
        $path = $directory->getRealPath($this->getContextPath());

        $scanned = scandir($path);

        if ($scanned === false) {
            return [];
        }

        $list = [];

        foreach ($scanned as $relativePath) {
            if (in_array($relativePath, ['.',  '..', '.gitkeep']) === true) {
                continue;
            }

            $list[] = $path . DIRECTORY_SEPARATOR . $relativePath;
        }

        return $list;
    }

    public function getFileContents(string $filePath): string
    {
        $file = new Path($filePath);
        $path = $file->getRealPath($this->getContextPath());

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException(
                'Could not get the contents of the file'
            );
        }

        return $contents;
    }

    /** @return array<int,string> */
    public function getFileRows(string $filePath): array
    {
        $file = new Path($filePath);
        $path = $file->getRealPath($this->getContextPath());

        return array_map(fn($row) => trim($row), file($path) ?: []);
    }

    public function isDirectory(string $directoryPath): bool
    {
        $directory = new Path($directoryPath);

        try {
            $path = $directory->getRealPath($this->getContextPath());
        } catch (InvalidArgumentException) {
            return false;
        }

        return is_dir($path);
    }

    public function isFile(string $filePath): bool
    {
        $file = new Path($filePath);

        try {
            $path = $file->getRealPath($this->getContextPath());
        } catch (InvalidArgumentException) {
            return false;
        }

        return is_file($path);
    }

    public function isReadable(string $targetPath): bool
    {
        $target = new Path($targetPath);

        try {
            $path = $target->getRealPath($this->getContextPath());
        } catch (InvalidArgumentException) {
            return false;
        }

        return is_readable($path);
    }

    public function isWritable(string $targetPath): bool
    {
        $target = new Path($targetPath);

        try {
            $path = $target->getRealPath($this->getContextPath());
        } catch (InvalidArgumentException) {
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
    public function changePermissions(string $targetPath, int $octalPermissions = 0755): void
    {
        $target = new Path($targetPath);

        $path = $target->getRealPath($this->getContextPath());

        if (chmod($path, $octalPermissions) === false) {
            throw new RuntimeException(
                'Could not change requested object permissions'
            );
        }
    }

    public function makeDirectory(string $directoryPath): void
    {
        $path = new Path($directoryPath);

        if ($this->isDirectory($directoryPath) === true) {
            return;
        }

        if ($path->isLocalPath() === false) {
            throw new InvalidArgumentException(
                'The path specified for the directory to be created is invalid'
            );
        }

        if ($path->isRelativePath() === true) {
            throw new InvalidArgumentException(
                'The directory path to be created cannot be relative'
            );
        }

        $fullpath = $this->getContextPath() . DIRECTORY_SEPARATOR . $directoryPath;

        if (mkdir($fullpath, 0777, true) === false) {
            throw new RuntimeException(
                'The relevant permissions prevent creating the directory'
            );
        }
    }

    public function setFileContents(string $filePath, string $contents): void
    {
        $this->touchFile($filePath, $contents);
    }

    public function appendFileContents(string $filePath, string $contents): void
    {
        $this->touchFile($filePath, $contents, FILE_APPEND);
    }

    /**
     * @see https://www.php.net/manual/pt_BR/function.file-put-contents.php
     */
    private function touchFile(string $filePath, string $contents, int $flag = 0): void
    {
        $file = new Path($filePath);
        $path = $file->getRealPath($this->getContextPath());

        if (file_put_contents($path, $contents, $flag) === false) {
            throw new RuntimeException('Could not add data to file');
        }
    }
}
