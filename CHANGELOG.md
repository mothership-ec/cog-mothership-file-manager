# Changelog

## 3.1.1

- Resolve issue where `FileLoader::getAll()` would return an instance of `File` instead of an array with a single `File` instance if there is only one file in the file manager

## 3.1.0

- Added `File\FileLoader` class, which replaces the `File\Loader` class and uses the query builder to generate queries
- Added `File\TagLoader` class for loading file tags, instead of loading them within the file loader
- Added `File\FileLoaderInterface` to ensure that `File\Loader` and `File\FileLoader` have the same set of methods for an easy transition
- `file_manager.file.loader` service returns instance of `File\FileLoader`
- `File\FileProxy` takes `File\TagLoader` instead of `File\Loader` in its constructor, and uses this to load tags
- Deprecated `File\Loader` class
- `File\Loader` class creates instances of `File\File` instead of `File\FileProxy`
- Added `getByFilename()` method to `File\Loader` for loading files by their name
- Added `getByExtension()` method to `File\Loader` for loading files by their extension
- `File\Loader::getByUser()` method takes `Message\User\UserInterface` as main argument instead of non-existance `\User` class

## 3.0.1

- Resolved issue where it would not let you re-upload a deleted file
- Removed unused `File\Loader::setSorter()` method
- Removed unused `File\Loader::setPaging()` method

## 3.0.0

- Initial open sourced release