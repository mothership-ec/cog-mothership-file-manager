<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File;
use Message\Cog\DB\Query;

class Delete {

	protected $_file;

	public function __construct(File $file, Query $query)
	{
		$this->_file = $file;
		$this->_query = $query;

	}

	public function delete()
	{

		$date = new \Datetime;
		$user = "";

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

				$this->_file->id;
				$date,
				$user,
				$date,
				$user
			));
	}
}