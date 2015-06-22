<?php

namespace Message\Mothership\FileManager\File;

use Message\User;
use Message\Cog\DB\Query;
use Message\Cog\ValueObject\DateTimeImmutable;
use Message\Cog\Filesystem\File as FileSystemFile;
use Message\Cog\DB\Result;

/**
 * @deprecated  Preserved for backwards compatibility, and will be removed in version 4.0.0. Use more efficient
 *              FileLoader instead.
 */
class Loader implements FileLoaderInterface
{

	protected $_locale;
	protected $_query;
	protected $_returnAsArray;

	/**
	 * var to toggle the loading of deleted files
	 *
	 * (default value: false)
	 *
	 * @var bool
	 */
	protected $_loadDeleted = false;

	public function __construct(/*\Locale*/ $locale, Query $query)
	{
		$this->_locale = $locale;
		$this->_query = $query;
	}

	/**
	 * Return an array of, or singular File object
	 *
	 * @param  int|array $fileID
	 * @return array|File 	File object
	 */
	public function getByID($fileID)
	{
		$this->_returnAsArray =  is_array($fileID);
		return $this->_load($fileID);
	}

	/**
	 * Returns all the files of a certain file type id
	 *
	 * @param  int 	$typeID
	 * @return array|File 	Array of File objects, or a single File object
	 */
	public function getByType($typeID)
	{
		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
			WHERE
				type_id = ?i',
			array(
				$typeID,
			)
		);

		return count($result) ? $this->getByID($result->flatten()) : false;

	}

	/**
	 * Load files by their filename
	 *
	 * @param string $filename
	 * @throws \InvalidArgumentException    Throws exception if $filename is not a string
	 *
	 * @return array|bool|File
	 */
	public function getByFilename($filename)
	{
		if (!is_string($filename)) {
			throw new \InvalidArgumentException('Filename must be a string, ' . gettype($filename) . ' given');
		}

		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
			WHERE
				`name` = ?s
		', [$filename]);

		return count($result) ? $this->getByID($result->flatten()) : false;
	}

	/**
	 * Load all files with a certain extension
	 *
	 * @param string $ext
	 * @throws \InvalidArgumentException    Throws exception if $ext is not a string
	 *
	 * @return array|bool|File
	 */
	public function getByExtension($ext)
	{
		if (!is_string($ext)) {
			throw new \InvalidArgumentException('Extension must be a string, ' . gettype($ext) . ' given');
		}

		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
			WHERE
				extension = ?s
		', [$ext]);

		$this->_returnAsArray = true;

		return count($result) ? $this->getByID($result->flatten()) : false;
	}

	/**
	 * Find results based on the search term
	 *
	 * @param  string $term search terms
	 * @return array|File 	Array of File objects, or a single File object
	 */
	public function getBySearchTerm($term)
	{
		// Turn the terms into an array
		$terms = explode(' ',$term);

		// Set a bunch of arrays which are used below, seems a lot but it's
		// becasue we have to pass through an array to the sql query so we have to do it twice
		$whereName = array();
		$whereTag = array();
		$whereTerms = array();

		// Loop over the terms and add them to an array to implode in the query
		foreach ($terms as $term) {
			$whereName[]  = ' name LIKE ?s';
			$whereTag[]   = ' tag_name LIKE ?s';
			$whereTerms[] = trim($term);
		}
		// Duplciate and add the same array again and merge it to one, this is
		// because we are looking at both the name and tag name in the query
		$where = array_merge($whereTerms, $whereTerms);

		// Add the wildcard modifiers to each search term
		foreach ($where as $key => $value) {
			$where[$key] = '%' . $value . '%';
		}

		$result = $this->_query->run('
			SELECT
				file.file_id
			FROM
				file
			LEFT JOIN
				file_tag USING (file_id)
			WHERE
				('.implode(' OR', $whereName).')
				OR
				('.implode(' OR', $whereTag).')',
			$where
		);

		// Return the array of results.
		return count($result) ? $this->getByID($result->flatten()) : false;

	}

	/**
	 * Return all files in an array
	 * @return Array|File|false - 	returns either an array of File objects, a
	 * 								single file object or false
	 */
	public function getAll()
	{
		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
		');

		return count($result) ? $this->getByID($result->flatten()) : false;

	}

	public function getByUnused()
	{

	}

	public function getByUser(User\UserInterface $user)
	{
		if ($user instanceof User\AnonymousUser) {
			return false;
		}

		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
			WHERE
				created_by = ?i',
			array(
				$user->id
			)
		);

		return count($result) ? $this->getByID($result->flatten()) : false;

	}

	/**
	 * Toggle whether or not to load deleted files
	 *
	 * @param bool $bool 	true / false as to whether to include deleted items
	 * @return 	$this 		Loader object in order to chain the methods
	 */
	public function includeDeleted($bool)
	{
		$this->_loadDeleted = $bool;
		return $this;
	}

	/**
	 * Gets the tags for a file
	 * @param  File      $file file to load tags for
	 * @return array     tags for file as an array
	 */
	public function getTagsForFile(File $file)
	{
		return $this->_loadTags($file);
	}

	/**
	 * Loads the file data out of the table and loads in into a File Object.
	 *
	 * @param  int|array $fileID fileId of the file to be loaded
	 *
	 * @return File|false return instance of the file is loaded else false
	 */
	protected function _load($fileID)
	{
		$fileIDs = (array) $fileID;

		$result = $this->_query->run('
			SELECT
				file.file_id AS id,
				file.url AS url,
				file.name AS name,
				file.extension AS extension,
				file.file_size AS fileSize,
				file.created_at AS createdAt,
				file.created_by AS createdBy,
				file.updated_at AS updatedAt,
				file.updated_by AS updatedBy,
				file.deleted_at AS deletedAt,
				file.deleted_by AS deletedBy,
				file.type_id AS typeID,
				file.checksum AS checksum,
				file.preview_url AS previewUrl,
				file.dimension_x AS dimensionX,
				file.dimension_y AS dimensionY,
				file.alt_text AS altText,
				file.duration AS duration
			FROM
				file
			WHERE
				file.file_id IN (?ij)
			ORDER BY
				created_at DESC
		', array($fileIDs));

		if (count($result)) {
			return $this->_loadFile($result);
		}

		return false;
	}

	/**
	 * This will load file objects for the results of _load
	 *
	 * @param  Result $results 	Results of files that need to be loaded
	 *
	 * @return array|File 		array or single Page object if only one result
	 */
	protected function _loadFile(Result $results)
	{
		$files = $results->bindTo(
			'\Message\Mothership\FileManager\File\File',
			[$this]
		);

		foreach ($results as $key => $result) {

			// Remove the file if it is deleted unless we are loading deleted files
			if ($result->deletedAt && !$this->_loadDeleted) {
				unset($files[$key]);
				continue;
			}

			$files[$key]->authorship->create(new DateTimeImmutable('@'.$result->createdAt), $result->createdBy);

			if ($result->updatedAt) {
				$files[$key]->authorship->update(new DateTimeImmutable('@'.$result->updatedAt), $result->updatedBy);
			}

			if ($result->deletedAt) {
				$files[$key]->authorship->delete(new DateTimeImmutable('@'.$result->deletedAt), $result->deletedBy);
			}

			$files[$key]->file = new FileSystemFile($files[$key]->url);

			// Force type to be an integer
			$files[$key]->typeID = (int) $files[$key]->typeID;

			$tags = $this->_loadTags($files[$key]);

			if ($tags) {
				$files[$key]->setTags($tags);
			}
		}

		return count($files) == 1 && !$this->_returnAsArray ? $files[0] : $files;
	}

	protected function _loadTags(File $file)
	{
		$tags = array();

		$result = $this->_query->run('
			SELECT
				file_tag.tag_name
			FROM
				file_tag
			WHERE
				file_tag.file_id = ?i', array($file->id)
		);

		if (count($result)) {
			$tags = $result->flatten();
		}

		return $tags;

	}

}