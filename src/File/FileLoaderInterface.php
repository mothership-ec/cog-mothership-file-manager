<?php

namespace Message\Mothership\FileManager\File;

use Message\User\UserInterface;

/**
 * Interface FileLoaderInterface
 * @package Message\Mothership\FileManager\File
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Interface representing a
 */
interface FileLoaderInterface
{
	public function getByID($fileID);

	/**
	 * Returns all the files of a certain file type id
	 *
	 * @param  int 	$typeID
	 * @return array|File 	Array of File objects, or a single File object
	 */
	public function getByType($typeID);

	/**
	 * Load files by their filename
	 *
	 * @param string $filename
	 * @throws \InvalidArgumentException    Throws exception if $filename is not a string
	 *
	 * @return array|bool|File
	 */
	public function getByFilename($filename);

	/**
	 * Load all files with a certain extension
	 *
	 * @param string $ext
	 * @throws \InvalidArgumentException    Throws exception if $ext is not a string
	 *
	 * @return array|bool|File
	 */
	public function getByExtension($ext);

	/**
	 * Find results based on the search term
	 *
	 * @param  string $term search terms
	 * @return array|File 	Array of File objects, or a single File object
	 */
	public function getBySearchTerm($term);

	/**
	 * Return all files in an array
	 * @return Array|File|false - 	returns either an array of File objects, a
	 * 								single file object or false
	 */
	public function getAll();

	/**
	 * @param UserInterface $user
	 * @return mixed
	 */
	public function getByUser(UserInterface $user);

	/**
	 * Toggle whether or not to load deleted files
	 *
	 * @param bool $bool 	true / false as to whether to include deleted items
	 * @return 	$this 		Loader object in order to chain the methods
	 */
	public function includeDeleted($bool);
}