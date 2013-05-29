<?php

namespace Message\Mothership\FileManager\File;

use Message\Cog\DB\Query;

class Edit 
{
	/**
	 * @access public
	 */
	protected $_query;

	public function __construct(Query $_query)
	{
		$this->_query = $_query;
	}

	/**
	 * Saves file information to database
	 */
	public function save()
	{
		$this->_query->run("");

		if (!$this->_query->run) {
			throw new DatabaseException('Persisting to database failed.');
		} else {
			return $this;
		}
	}
}