<?php

namespace Message\Mothership\FileManager\File;

use Message\Cog\DB\Query;

class Loader 
{
	
	protected $_locale;
	protected $_query;

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
		$return = array();

		if (!is_array($fileID)) {
			return $this->_load($fileID);
		} else {
			foreach ($fileID as $id) {
				$return[] = $this->_load($id);
			}
		}

		return array_filter($return);
	}

	/** 
	 * Returns all the files of a certain file type id
	 * 
	 * @param  int 	$typeID
	 * @return array|File 	Array of File objects, or a single File object
	 */
	public function getByType($typeID)
	{
		$this->_query->run('
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

	public function getBySearchTerm($term)
	{
		// We would want to do something clever in here.

	}

	/**
	 * Return all files in an array
	 * @return Array|File|false - 	returns either an array of File objects, a 
	 * 								single file object or false
	 */
	public function getAll()
	{
		$this->_query->run('
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

	public function getByUser(\User $user)
	{
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

	public function setSort(\Sorter $sorter)
	{

	}

	public function setPaging(\Pager $pager)
	{

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

	protected function _load($fileID)
	{
		$result = $this->_query->run('
			SELECT
				file.file_id AS fileID,
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
				file.file_id = ?', array($fileID)
		);

		if (count($result)) {
			$file = new File;
			$file = $result->bind($file);
			
			if ($file->deletedAt && !$this->_loadDeleted) {
				return false;
			}

			$file->createdAt = new \DateTime(date('c',$file->createdAt));

			if ($file->updatedAt) {
				$file->updatedAt = new \DateTime(date('c',$file->updatedAt));
			}

			if ($file->deletedAt) {
				$file->deletedAt = new \DateTime(date('c',$file->deletedAt));
			}
			return $file;
		}

		return false;

	}

}