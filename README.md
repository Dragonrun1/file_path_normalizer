# README.md

Master: [![Build Status](https://travis-ci.org/Dragonrun1/file_path_normalizer.svg?branch=master)](https://travis-ci.org/Dragonrun1/file_path_normalizer)

Master: [![Coverage Status](https://img.shields.io/coveralls/Dragonrun1/file_path_normalizer.svg?branch=master)](https://coveralls.io/r/Dragonrun1/file_path_normalizer?branch=master)

File_path_normalizer is a class used to normalize PHP file paths without several
of the shortcomings of the built-in functions.


## Why you should use it

So you maybe wondering why a class like this is even needed since PHP has many
built-in functions that let you do almost anything needed to clean up a given
file name and path. PHP does have many functions that are made to work with
paths and file names plus a wide range of string and regex functions that can be
useful, however there are many edge cases and limitations in these functions can
lead to bugs and, in the case of any web based applications, possible security
issues when parts of the filesystem are unexpectedly exposed.

Just like in a web page where you must protect from Javascript XSS and possible
database exposure, you must also ensure where user input might be used to form 
file system paths or names that it is clean and not doing something unexpected.

Even if you are not forming paths using any user input the OS you use for PHP
affects the file path and how many of the file functions work. Probably one of
the best know differences is the use of back-slashes and drive letters in
Windows paths vs forward slashes and the unified file paths of Linux/Unix and
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

This code could end up deleting all the files in the current directory on
Windows systems depending on the privileges of the user running the code.

Here's an Unix version of the same thing.

```
<?php
$path = '/bin/bash -c "rm -f *.*"';
print shell_exec($path);
```

Now most good developers would do some checks on the file path something like
this for example:

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
resolve their path using `realpath()` but there are some edge cases where it can
return some unexpected results. 

In this example I'll give several of the known edge cases you might need to
handle.

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

As you can see from these examples, great care is needed when using `realpath()`.
Some of the other file functions have similar issues, especially when dealing
with relative paths. In the next section I'll show how `FilePathNormalizer` can
help solve some of these edge cases and act like an improved `realpath()`
replacement in many cases.

## How the class helps

First here are a few of the reasons programmers use relative paths:

  * Relative paths are usually shorter and so easier to work with.
  * The path is wholly or in part taken from user input and maybe relative.
  * Using relative paths means the actual location where the application is
  installed doesn't really matter -- only that the internal application
  directory structure is always the same.
  
There probably are some other reasons but the ones above are some of the most
common ones I see most often in both my code and other developers' code. We also
know from the section above that though `realpath()` can be used it has some
issues you must resolve if you are going use it.

One way to work around these issues would be to wrap `realpath()` in a class and
try adding code to handle all of the undesired problems. Instead I choose a
strategy that doesn't even using it but that has the most of same abilities I
like of `realpath()` but without some of its shortcomings.

I also wanted something that works with both Windows and Unix paths but allows
me act like I'm always working with Unix paths.

The best way to show why I think `FilePathNormalizer` is better would be with
some examples of how it handles some of the edge cases from above.

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

When run on Windows systems, PHP allows both front-slashes(FS) and
back-slashes(BS) to be used for directory separators in a path. It generally
_not_ a good idea to use both in a single path but seems to work in some cases.
When run on Unix-based systems, PHP treats BS not as separators but as escapes
or merely even part of the name in that piece of the path in some cases. Since
PHP and Windows itself actually allows you to use FSes its best to just replace
any BSes with FSes and not have to worry about mixing them.

Note that though Windows itself does allow either `cmd.exe` and
`Windows Explorer` only seem to understand and allow BSes as separators.The
first thing that `FilePathNormalizer` does is to convert all BSes into FSes
which makes working with the paths much easier overall. Another thing
`normalizePath()` does is to normalize the path with a trailing FS so a path
like `/my/web/app` would become `/my/web/app/`. No more 'Do I need a separator
or not?' questions when adding file names to a path.

## Not Just Local

File functions in PHP are in many ways unique when compared with many other
programming languages in they aren't just used to access files on the local
system but, if allowed, they can be used to access file on another system. Using
something like
`https://github.com/Dragonrun1/file_path_normalizer/blob/master/FilePathNormalizer.php`
is possible. In PHP the `https://` part is called a wrapper and in some cases
you can even use multiple wrappers with each being past the result of the inner
wrapper. For example you could do:
`zip://ftp://my/ftp/site/sample.zip` and PHP will use FTP to get the
`sample.zip` file from the site and than unzip it as well as long as both
wrappers are installed. Most wrapper handlers are written in C code but they can
be written in PHP as well. A good example of this is
[vsfStream](https://github.com/mikey179/vfsStream) which can be used as a
virtual file system for unit testing.

In the next section I'll go over wrappers in more detail and how
`FilePathNormalizer` does basic validating of them for you.

## Wrappers

Wrappers can be used for many things like compression and decompression `zip://`
or determining the default port and way of connecting to another system
`http://` vs `https://`. Also like stated in the last section in some case you
are allowed to nest them. `FilePathNormalizer` doesn't try to do a lot of
validating but it does insure that any include wrappers follow the basic pattern
of starting with an alphabetic character which is followed by one or more
alphanumeric characters and ends with `://` and does allows nest wrappers. One
thing is it only validate them at the beginning of the path so something like
`http://a.amazing.site/ftp://not.so.amazing.site` will only see the `http://`
part as the wrapper with the rest just being part of the normal path.

## At The Root

So with the root part of the path you have two camps. One is Windows and the
other is Unix and everyone else. There is a small camp of original Mac folks
that use names and ':'s but since OSX Apple has largely moved them to FS like
all the rest I'll ignore them.

This part of the path is actually fairly easy to handle as it can only be a
single letter followed by `:/` on Windows or just FS for everyone else. All of
the BSes have already been handled before this point.

## Summary

So hopefully the above text and example have helped you better understand paths
and how `FilePathNormalizer` can help you deal with them better.

## Installing

File Path Normalizer is available through
[Packagest](https://packagist.org/packages/dragonrun1/file_path_normalizer)
and can be installed using composer:

```shell
composer require dragonrun1/file_path_normalizer
```

## Backward Compatibility Breaking Changes

With version 2.0.0 there have been some BC changes application developers should
be aware of:

  * Minimum PHP version is now 7.0.0. Time to start leading into the future.
  * Legacy use of bool option on normalizeFile() and normalizePath has been dropped.
  * Enabling or allowing VFSStream with absolute path is no longer allowed. Have
  this exception was more confusing than useful.
  * Options are now validated and many combinations that might have been allowed
  before but were not attended now cause an exception to be thrown.
  * With changed to VFSStream and absolute path plus changes to wrapper and
  VFSStream allowed interact the default has changed to:
  ``` const MODE_DEFAULT = self::ABSOLUTE_REQUIRED | self::VFS_DISABLED | self::WRAPPER_ALLOWED;```
  * Better checking of wrappers to close some additional edge cases.

## New Features

With version 2.0.0 some new features have been added.

  * New PathInfo class was extracted and can now be used by application
  developers for their other path related needs. It makes the same `wrappers`,
  `root` and `dirs` parts that are used internally available so you can for
  example strip off all wrappers or strip off absolute part of path.
  * All testing has been re-written to use PHPSpec examples instead of having
  mix of PHPUnit tests and PHPSpec examples.
  * New PHPSpec examples have been add which has greatly increased code coverage
  and code quality.
  * New and updated interfaces available for all aspects of the library.
  * New PathInfoTrait to make adding it to your own code easier.
  * FilePathNormalizerTrait::getFpn() is now public.
