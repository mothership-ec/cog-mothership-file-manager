<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File;
use Message\Cog\DB\Query;

class Delete
{

	protected $_file;

	public function __construct(File $file, Query $query)
	{
		$this->_file = $file;
		$this->_query = $query;

	}

	public function delete()
	{

	}
}