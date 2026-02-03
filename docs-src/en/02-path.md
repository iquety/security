# Path

--page-nav--

## Functionalities

When building a new `Path` object, it is necessary to provide a **context**, that is, a valid directory that will serve as a limit for manipulating paths.

In other words, only file and directory nodes that are inside the context directory can be accessed by the `Path` instance.

If the context is the path to a file, the context will be ignored and the manipulation will work assuming the file path is safe.

Considere a seguinte estrutura:

```txt
/fulano/
   |- directory/
   |    |
   |    |- real/
   |         |
   |         |- to/
   |              |
   |              |- my/
   |                   |
   |                   |- file.txt
   |- file.txt
```

```php
// defines a scope to navigate only within the directory __DIR__ . '/directory/real'
$instance = new Path(__DIR__ . '/diretorio/real');

// add the path to the file __DIR__ . '/file.txt
$instance->addNodePath('/../../arquivo.txt');

// even if a file exists outside the scope, it will not be possible to access it.
$instance->getAbsolutePath();
// an exception will be thrown:
// Cannot get absolute path outside the scope of the '/fulano/directory/real' context
```

The available methods are:

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

--page-nav--
