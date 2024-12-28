# This guide helps you to migrate from version 0.x to 1.x

V1.X of Pathogen contains a big refactoring of the class structure and a lot of improvements compared to v0.X.
This guide helps you with understanding the differences including a list of breaking changes.

## Removed Concepts

### Factories

While there still is a factory class in version 1.x, I would call this a complete different concept compared to v0.X.
In v1.X and above, the factory class is an implementation detail, which you don't need to care about. In version v0.X it
was a documented feature, which was overengineered from my perspective.

### Normalizer

Normalization should be just a method on the paths itself. So instead of needing to initialize a PathNormalizer as a
separate object and then normalize a path, you should be able to just use the function `->normalize()` on any path.

### Unix / Windows Paths

Previously paths were distinguishable between Windows and Unix paths. Because of this, the complete class structure was
more complex than it would have to be. If you need paths for Windows, just use the same classes as you would for Linux.
If you need to handle Windows drives, you can use the `DriveAnchored` subclasses of the normal classes.

## Namespace `\Eloquent\Pathogen` becomes `\Pathogen`

I removed the `Eloquent` namespace prefix from all classes. Since most of the code is rewritten, now is the perfect
moment to get rid of the previous maintainer of the package.

## Removed Classes / Interfaces / Traits

| Removed                                                                          | Replacement                                |
|----------------------------------------------------------------------------------|--------------------------------------------|
| Eloquent\Pathogen\Factory\PathFactory                                            | -                                          |
| Eloquent\Pathogen\Factory\PathFactoryInterface                                   | -                                          |
| Eloquent\Pathogen\Factory\Consumer\PathFactoryTrait                              | -                                          |
| Eloquent\Pathogen\FileSystem\AbsoluteFileSystemPathInterface                     | Pathogen\AbsolutePath                      |
| Eloquent\Pathogen\FileSystem\FileSystemPath                                      | Pathogen\Path                              |
| Eloquent\Pathogen\FileSystem\FileSystemPathInterface                             | Pathogen\Path                              |
| Eloquent\Pathogen\FileSystem\PlatformFileSystemPath                              | Pathogen\Path                              |
| Eloquent\Pathogen\FileSystem\RelativeFileSystemPathInterface                     | Pathogen\RelativePath                      |
| Eloquent\Pathogen\FileSystem\Factory\PlatformFileSystemPathFactory               | -                                          |
| Eloquent\Pathogen\FileSystem\Factory\FileSystemPathFactory                       | -                                          |
| Eloquent\Pathogen\FileSystem\Factory\FileSystemPathFactoryInterface              | -                                          |
| Eloquent\Pathogen\FileSystem\Factory\PlatformFileSystemPathFactory               | -                                          |
| Eloquent\Pathogen\FileSystem\Normalizer\FileSystemPathNormalizer                 | -                                          |
| Eloquent\Pathogen\FileSystem\Factory\Consumer\FileSystemPathFactoryTrait         | -                                          |
| Eloquent\Pathogen\FileSystem\Factory\Consumer\PlatformFileSystemPathFactoryTrait | -                                          |
| Eloquent\Pathogen\Normalizer\PathNormalizer                                      | Use ->normalize() (TODO) function on paths |
| Eloquent\Pathogen\Normalizer\PathNormalizerInterface                             | Use ->normalize() (TODO) function on paths |
| Eloquent\Pathogen\Resolver\BasePathResolver                                      | Use ->resolve() (TODO) function on paths   |
| Eloquent\Pathogen\Resolver\BasePathResolverInterface                             | Use ->resolve() (TODO) function on paths   |
| Eloquent\Pathogen\Resolver\FixedBasePathResolver                                 | Use ->resolve() (TODO) instead             |
| Eloquent\Pathogen\Resolver\PathResolverInterface                                 | Use ->resolve() (TODO) instead             |





- FileSystemPath
- FileSystemPathInterface


## Further Breaking Changes

- Functions `contains()`, `matches()`, `endsWith()`, `startsWith()`, `nameMatches()`, `nameStartsWith()` and `nameContains()` do not support value null for parameter `$caseSensitive` anymore.
- Trailing slashes are not stripped anymore.
- 