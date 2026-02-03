# Path

[◂ Filesystem](01-filesystem.md) | [Sumário da Documentação ▸](indice.md)
-- | --

## Funcionalidades

Na construção de um novo objeto `Path`, é preciso fornecer um **contexto**, ou seja, um diretório válido que servirá de limite para manipulação de caminhos.

Em outras palavras, apenas os nós de arquivos e diretórios que estiverem dentro do diretório de contexto poderão ser acessados pela instância de `Path`.

Se o contexto for o caminho para um arquivo, o contexto será ignorado e a manipulacao funcionará levando em consideracao que o caminho do arquivo é seguro.

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
$instance = new Path(__DIR__ . '/diretorio/real');

// adicionar o caminho até o arquivo __DIR__ . '/arquivo.txt
$instance->addNodePath('/../../arquivo.txt');

// mesmo que um arquivo exista fora do escopo não será possível acessá-lo
$instance->getAbsolutePath();
// exceção será lançada:
// Cannot get absolute path outside the scope of the '/fulano/diretorio/real' context
```

Os métodos disponíveis são:

- addNodePath
- getAbsolutePath
- getContextPath
- getDirectory
- getExtension
- getFile
- getName
- getNodePath
- getPath
- isLocalPath
- isRelativePath

[◂ Filesystem](01-filesystem.md) | [Sumário da Documentação ▸](indice.md)
-- | --
