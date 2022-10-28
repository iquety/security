<?php

declare(strict_types=1);

namespace Iquety\Security;

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

        $this->contextPath = $context->getContextPath();
    }

    public function getContextPath(): Path
    {
        return new Path($this->contextPath);
    }

    /** @return array<int,Path> */
    public function getDirectoryContents(string $directoryPath): array
    {
        $directory = $this->getContextPath()->addNodePath($directoryPath);

        $nodePath = $directory->getNodePath();

        try {
            $path = $directory->getAbsolutePath();
        } catch (RuntimeException) {
            $dirname = $directory->getPath();
            throw new RuntimeException("Directory {$dirname} does not exist");
        }

        $scanned = scandir($path);

        if ($scanned === false) {
            return [];
        }

        if ($scanned === ['.',  '..']) {
            return [];
        }

        $list = [];

        foreach ($scanned as $relativePath) {
            if (in_array($relativePath, ['.',  '..', '.gitkeep']) === true) {
                continue;
            }

            $list[] = $this->getContextPath()
                ->addNodePath($nodePath)
                ->addNodePath($relativePath);
        }

        return $list;
    }

    /** @return array<int,Path> */
    public function getDirectoryFiles(string $directoryPath): array
    {
        $files = array_filter(
            $this->getDirectoryContents($directoryPath),
            fn($path) => is_file($path->getPath())
        );
        return array_values($files);
    }

    /** @return array<int,Path> */
    public function getDirectorySubdirs(string $directoryPath): array
    {
        $dirs = array_filter(
            $this->getDirectoryContents($directoryPath),
            fn($path) => is_dir($path->getPath())
        );
        return array_values($dirs);
    }

    public function getFileContents(string $filePath): string
    {
        $file = $this->getContextPath()->addNodePath($filePath);

        try {
            $path = $file->getAbsolutePath();
        } catch (RuntimeException) {
            $filename = $file->getPath();
            throw new RuntimeException("File {$filename} does not exist");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                'Could not get the contents of the file'
            );
            // @codeCoverageIgnoreEnd
        }

        return $contents;
    }

    /** @return array<int,string> */
    public function getFileRows(string $filePath): array
    {
        $file = $this->getContextPath()->addNodePath($filePath);

        try {
            $path = $file->getAbsolutePath();
        } catch (RuntimeException) {
            $filename = $file->getPath();
            throw new RuntimeException("File {$filename} does not exist");
        }

        return array_map(fn($row) => trim($row), file($path) ?: []);
    }

    public function getFilePermissions(string $filePath): string
    {
        $file = $this->getContextPath()->addNodePath($filePath);

        $path = $file->getAbsolutePath();

        $permissions = fileperms($path);
        return substr(sprintf('%o', $permissions), -4);
    }

    public function isDirectory(string $directoryPath): bool
    {
        if ((new Path($directoryPath))->isLocalPath() === false) {
            return false;
        }

        $directory = $this->getContextPath()->addNodePath($directoryPath);

        try {
            $path = $directory->getAbsolutePath();
        } catch (RuntimeException) {
            return false;
        }

        return is_dir($path);
    }

    public function isFile(string $filePath): bool
    {
        $file = $this->getContextPath()->addNodePath($filePath);

        try {
            $path = $file->getAbsolutePath();
        } catch (RuntimeException) {
            return false;
        }

        return is_file($path);
    }

    public function isReadable(string $targetPath): bool
    {
        $target = $this->getContextPath()->addNodePath($targetPath);

        try {
            $path = $target->getAbsolutePath();
        } catch (RuntimeException) {
            return false;
        }

        return is_readable($path);
    }

    public function isWritable(string $targetPath): bool
    {
        $target = $this->getContextPath()->addNodePath($targetPath);

        try {
            $path = $target->getAbsolutePath();
        } catch (RuntimeException) {
            return false;
        }

        return is_writable($path);
    }

    /**
     * As permissões funcionam de forma octal, somando os valores para obter a
     * configuração desejada, onde 4 = Ler, 2 = Escrever e 1 = Executar.
     *
     * Por exemplo:
     * 0    cannot read, write or execute
     * 1    can only execute
     * 2    can only write
     * 3    can write and execute
     * 4    can only read
     * 5    can read and execute
     * 6    can read and write
     * 7    can read, write and execute
     *
     * O argumento $octalPermissions deve ser formado por 4 digitos, por ex: 0765,
     * onde 7 para o dono do arquivo, 6 para o grupo e 5 para os outros usuários
     *
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function changePermissions(string $targetPath, int $octalPermissions = 0755): void
    {
        $target = $this->getContextPath()->addNodePath($targetPath);

        $path = $target->getAbsolutePath();

        if (chmod($path, $octalPermissions) === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                'Could not change requested object permissions'
            );
            // @codeCoverageIgnoreEnd
        }

        clearstatcache();
    }

    public function makeDirectory(string $directoryPath): void
    {
        if ($this->isDirectory($directoryPath) === true) {
            return;
        }

        if ((new Path($directoryPath))->isLocalPath() === false) {
            throw new InvalidArgumentException(
                'The path specified for the directory to be created is invalid'
            );
        }

        if ((new Path(DIRECTORY_SEPARATOR . $directoryPath))->isRelativePath() === true) {
            throw new InvalidArgumentException(
                'The directory path to be created cannot be relative'
            );
        }

        $fullpath = $this->getContextPath()->addNodePath($directoryPath)->getPath();

        if (mkdir($fullpath, 0777, true) === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                'The relevant permissions prevent creating the directory'
            );
            // @codeCoverageIgnoreEnd
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
        if ((new Path(DIRECTORY_SEPARATOR . $filePath))->isRelativePath() == true) {
            throw new InvalidArgumentException('The file path path must be absolute');
        }

        $file = $this->getContextPath()->addNodePath($filePath);

        // cria o diretório, se não existir
        $nodePath = new Path($file->getNodePath());
        if ($this->isDirectory($nodePath->getDirectory()) === false) {
            $this->makeDirectory($nodePath->getDirectory());
        }

        if (file_put_contents($file->getPath(), $contents, $flag) === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not add data to file');
            // @codeCoverageIgnoreEnd
        }
    }
}
