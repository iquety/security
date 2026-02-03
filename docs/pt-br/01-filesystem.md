# Filesystem

[◂ Sumário da Documentação](indice.md) | [Path ▸](02-path.md)
-- | --

## Objetivo

Esta classe é usada para manipulação de arquivos e diretórios. O problema de segurança acontece quando é necessário consultar o caminho de um arquivo usando um valor fornecido pelo usuário. Caminhos relativos podem permitir a especulação de conteúdo privado do servidor.

Claro que um servidor corretamente configurado é capaz de impedir este tipo de ataque, mas erros humanos podem acontecer. Por esse motivo, faz sentido tratar todos os valores provenientes do usuário do sistema.

### Funcionalidades

Na construção de um novo objeto `Filesystem`, é preciso fornecer um **contexto**, ou seja, um diretório válido que servirá de limite para manipulação de arquivos e diretórios.

Considere a seguinte estrutura:

```txt
/fulano/
   |- diretorio/
   |    |
   |    |- real/
   |         |
   |         |- para/
   |              |
   |              |- meu/
   |                   |
   |                   |- arquivo.txt
   |- arquivo.txt
```

```php
// define um escopo para navegar apenas no diretório __DIR__ . '/diretorio/real'
$instance = new Filesystem(__DIR__ . '/diretorio/real');

// obtém o conteúdo do arquivo __DIR__ . '/diretorio/real/para/meu/arquivo.txt
$content = $instance->getFileContents('para/meu/arquivo.txt'); 

// mesmo que um arquivo exista fora do escopo não será possível acessá-lo
$content = $instance->getFileContents('../../arquivo.txt');
// exceção será lançada:
// File /fulano/arquivo.txt does not exist
```

Em outras palavras, apenas os arquivos e diretórios que estiverem dentro do diretório de contexto poderão ser manipulados pela instância de `Filesystem`.

Os métodos disponíveis são:

- getContextPath
- getDirectoryContents
- getDirectoryFiles
- getDirectorySubdirs
- getFileContents
- getFileRows
- getFilePermissions
- isDirectory
- isFile
- isReadable
- isWritable
- changePermissions
- makeDirectory
- setFileContents
- appendFileContents

[◂ Sumário da Documentação](indice.md) | [Path ▸](02-path.md)
-- | --
