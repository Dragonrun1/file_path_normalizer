README.md
=========

File_path_normalizer is a class used to normalize PHP file paths without several of the shortcomings of the
built-in functions.

## Why you should use it

So you maybe wondering why a class like this is even needed since PHP has many
built-in functions that let you do almost anything needed to clean up a given
file name and path. While PHP does have many functions that are made
to work with paths and file names, it also has a wide range of string and
regex functions as well that can be useful. However, the many edge cases and 
limitations of these functions can lead to bugs and, in the case of web based 
applications, possible security issues when parts of the filesystem are unexpectedly
exposed.

Just like in a web page where you must protect from Javascript XSS and possible
database exposure, you must also ensure where user input might be used to form 
file system paths or names that it is clean and not doing something unexpected.

Even if you are not forming paths using any user input the OS you use for PHP
affects the file path and how many of the file functions work. Probably one of
the best know differences is the use of back-slashes and drive letters in
Windows paths but forward slashes and the unified file paths of Linux/Unix and
Mac OS X.

Below are some examples to make the issues clearer.

## Issue Examples

_NOTE: The examples used here are to in no way be considered well written code
that should be actually used in your own code but are made to be as simple as
possible while showing some of the possible issues._

```
<?php
$path = 'C:\Windows\System32\cmd.exe /c dir';
print shell_exec($path);
```
On a Unix like system this will probably cause an error to be reported and PHP
to exit but on a Windows system it normally would show a directory listing of
the current directory. This would probably not be a big issue in most cases but
what if it was the following:

```
<?php
$path = 'C:\\Windows\\System32\\cmd.exe /c del /q *.*';
print shell_exec($path);
```

This code could end up deleting all the files in the current directory on Windows
systems depending on the privileges of the user running the code.

Here's an Unix version of the same thing.

```
<?php
$path = '/bin/bash -c "rm -f *.*"';
print shell_exec($path);
```

Now most good developers would do some checks on the file path in most cases
something like this for example:

```
<?php
$allowedPath = '/my/web/app/';
$path = '/bin/bash -c "ls -al"';
if (false === strpos($path, $path)) {
    $mess = 'Illegal file path detected must be in ' . $allowedPath;
    throw Exception($mess);
}
print shell_exec($path);
```

That seems to be okay but how about a different path as in this example:

```
<?php
$allowedPath = '/my/web/app/';
$path = '/my/web/app/../../../bin/bash -c "ls -al"';
if (false === strpos($path, $path)) {
    $mess = 'Illegal file path detected must be in ' . $allowedPath;
    throw Exception($mess);
}
print shell_exec($path);
```

Protecting against something like that is much harder. Most programmers try to
resolve their path using `realpath()` but there are some are some edge cases
where it can return some unexpected results. 

In this example I'll give several of the known edge cases you might need to handle.

```
<?php
// The current directory is '/my/web/app/'
$path = '/my/web/app/../../../bin/bash';
print realpath($path) . PHP_EOL;
// result: /bin/bash

// The current directory is '/my/web/app/'
$path = '../../../../iDoNotExist';
print realpath($path) . PHP_EOL;
// result is boolean `false` which is turned into an empty string by print.

// The current directory is '/my/app/'
$path = '/my/app/existingFile';
print realpath($path) . PHP_EOL;
// result: /my/app/existingFile
unlink($path);
print realpath($path) . PHP_EOL;
/*
result: /my/app/existingFile
realpath() and several other functions trigger caching of the resulting paths
which are not updated by other PHP file operations and would ignore any changes
happening outside of PHP as well.
*/

/*
Given the follow directory structure:

/my/app/
/DoIExist
/OtherFile

and the current directory is '/my/app/'
*/
$path = '../../../DoIExist';
print realpath($path) . PHP_EOL;
/*
The result in this case is /DoIExist
but what if you have to following directory structure:

/my/app/
/OtherFile

In this case the result is false again. The reason is once realpath() resolves
up to the top of the directory structure it in effect treats any additional
'../' like they are './' instead which means they are basically ignored.
*/
```

As you can see from these examples, great care is needed when using realpath().
Some of the other file functions have similar issues, especially when dealing
with relative paths. In the next section I'll show how `FilePathNormalizer` can
help solve some of these edge cases and act like an improved realpath()
replacement in many cases.

## How the class helps

First here are a few of the reasons programmers use relative paths:

  * Relative paths are usually shorter and so easier to work with.
  * The path is wholly or in part taken from user input and maybe relative.
  * Using relative paths means the actual location where the application is
  installed doesn't really matter -- only that the internal application directory
  structure is always the same.
  
There probably are some other reasons but the ones above are some of the most
common ones I see most often in both my code and other developers' code. We also
know from the section above that though `realpath()` can be used it has some
issues you need to must resolve if you are going use it.

One way to work around these issues would be to wrap `realpath()` in a
class and try adding code to handle all of the undesired problems. Instead I
choose a different strategy by NOT even using it and trying to make something that has
the most of same abilities I like of `realpath()` but without the shortcomings.

I also wanted something that works with both Windows and Unix paths but allows me
act like I'm always working with Unix paths.

So the best way I know to show why I think `FilePathNormalizer` is better is
with examples of how it handles some of the edge cases I give above.

```
<?php
$fpn = new FilePathNormalizer();

// The current directory is '/my/web/app/'
$path = '/my/web/app/../../../bin/bash';
print realpath($path) . PHP_EOL;
// result: /bin/bash
print $fpn->normalizeFile($path) . PHP_EOL;
// The same result as above.
print $fpn->normalizePath($path) . PHP_EOL;
// result: /bin/bash/
// Note the added end '/' when using normalizePath(). 

// The current directory is '/my/web/app/'
$path = '../../../../iDoNotExistFile';
print realpath($path) . PHP_EOL;
// result is boolean `false` which is turned into an empty string by print.
try {
    print $fpn->normalizeFile($path) . PHP_EOL;
} catch (DomainException $e) {
    print $e->getMessage() . PHP_EOL;
}
// results in a catchable exception because relative paths above the root
// directory are NOT allowed.

// The current directory is '/my/app/'
$path = '/my/app/existingFile';
print realpath($path) . PHP_EOL;
// result: /my/app/existingFile
print $fpn->normalizeFile($path) . PHP_EOL;
// Same result as above.
unlink($path);
print realpath($path) . PHP_EOL;
/*
result: /my/app/existingFile
realpath() and several other functions trigger caching of the resulting paths
which are not updated by other PHP file operations and would ignore any changes
happening outside of PHP as well.
*/
print $fpn->normalizeFile($path) . PHP_EOL;
/*
Same result as as realpath() but for a different reason. normalizeFile() and
normalizePath() don't look at the file system to see if the resulting path
exists or NOT. They are only meant to insure the path should be valid and clean
and leave to testing for is they exist in the file system up to the user. Using
functions like file_exists() which doesn't use the cache is recommended.
*/
```

The other couple of cases from above also will result in a `DomainException`
since they also try going 'above' the root path. Next I'll go over some of the
other things the class does to make working with paths easier.


## Dealing With Path Separators

When run on Windows systems, PHP allows both front-slashes and back-slashes to
be used for directory separators in a path. It generally not a good idea to use
both in a single path but it does seem to work most of the time. When run on Unix-
based systems, PHP treats back-slashes not as separators but as escapes or merely
even part of the name in that piece of the path in some cases. Since PHP and
Windows itself actually allows you to use front-slashes it best to simply replace any
back-slashes with front-slashes and not have to worry about mixing them.

Note that though Windows itself does allow both `cmd.exe` and `Windows Explorer` 
only seem to understand and allow back-slashes as separators. The first thing that
`FilePathNormalizer` do is to convert all back-slashes into forward-slashes which 
makes working with them much easier overall.

I don't understand this paragraph. Why is it so helpful in this case? Can you rewrite this a little more concisely?
Other area where `normalizePath()` helps to make things simpler is it always
makes sure the path ends with a forward-slash so if you are adding a file name 
to it there is never a need to add one yourself which you usually needed to do before.
This in some cases cause paths to have two FSes together which doesn't normal cause a
problem as the second one is ignore or treated like './' which is a no-op in the
middle of a path. This also helps keep many cases of path with file names from
cause some problems like when you receive `/bin/bash -c ...` it would be turned
into `/bin/bash -c .../` which in most cases will cause an error instead of
executing like was intended. Note that it is still possible craft paths with
command file names that will execute unexpected things but it does make it a
little harder in most cases.

## Summary


