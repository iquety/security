# Filesystem

--page-nav--

## Goal

This class is used for file and directory manipulation. The security issue happens when you need to look up the path of a file using a value provided by the user. Relative paths can allow speculation of server private content.

Of course, a properly configured server is capable of preventing this type of attack, but human errors can happen. For this reason, it makes sense to handle all values coming from the system user.

### Functionalities

When building a new `Filesystem` object, it is necessary to provide a **context**, that is, a valid directory that will serve as a limit for manipulating files and directories.

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
$instance = new Filesystem(__DIR__ . '/diretoryo/real');

// gets the contents of the file __DIR__ . '/directory/real/to/my/file.txt
$content = $instance->getFileContents('to/my/file.txt'); 

// even if a file exists outside the scope, it will not be possible to access it.
$content = $instance->getFileContents('../../file.txt');
// an exception will be thrown:
// File /fulano/file.txt does not exist
```

In other words, only files and directories that are inside the context directory can be manipulated by the `Filesystem` instance.

The available methods are:

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

--page-nav--
