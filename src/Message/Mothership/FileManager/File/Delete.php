<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File;
use Message\Cog\DB\Query;

	/**
	 * Author Ewan Valentine <ewan@message.co.uk> 
	 * Copyright Message 2013
	 *
	 * @return $this->_file 
	 * 
	 */
class Delete
{

	protected $_file;
	protected $_query;

	public function __construct(File $file, Query $query)
	{
		$this->_file = $file;
		$this->_query = $query;
	}

	public function delete()
	{

		$date = new \Datetime;
		$user = 1;

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

		$this->_file->deletedAt = $date;
		$this->_file->deletedBy = $user;

		return $this->_file;
	}
}