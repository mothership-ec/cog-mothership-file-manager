<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File;
use Message\Cog\DB\Query;

	/**
	 * Author Ewan Valentine <ewan@message.co.uk> 
	 * Copyright Message 2013
	 *
	 * Basic file deletion class for database records
	 * Saves who deleted a file and when.
	 *
	 * @todo remove dummy user
	 */
class Delete
{

	/**
	 * @access protected 
	 */
	protected $_file;

	/**
	 * @access protected 
	 */
	protected $_query;

	/**
	 * @access public
	 * @param File $file Query $query
	 */
	public function __construct(File $file, Query $query)
	{
		$this->_file = $file;
		$this->_query = $query;
	}

	/**
	 * Sets file in database marked as deleted.
	 * Keeps a record of who a file was deleted by and when.
	 *
	 * @return $this->_file
	 * @access public
	 */
	public function delete()
	{

		/** new Datatime */
		$date = new \Datetime;

		/** Dummy user */
		$user = 1;

		/** Query to set deletion info */
		$result = $this->_query->run("
			UPDATE
				file
			SET
				updated_at = ?i,
				updated_by = ?i,
				deleted_at = ?i,
				deleted_by = ?i
			WHERE
				file_id = ?i
		", array(
				$date,
				$user,
				$date,
				$user,
				$this->_file->id
			));

		/** Returns deletion date */
		$this->_file->deletedAt = $date;

		/** Returns deleted by */
		$this->_file->deletedBy = $user;

		return $this->_file;
	}
}