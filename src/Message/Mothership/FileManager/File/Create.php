<?php

namespace Message\Mothership\FileManager\File;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\Mothership\FileManager\File\Loader;

class Create
{

	protected $_query;

	public function __construct(DBQuery $query, DispatcherInterface $eventDispatcher)
	{
		$this->_query = $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	public function save(array $file)
	{
		$result = $this->_query->run('
			INSERT INTO
				file
			SET
				url = ?s,
				name = ?s,
				extension = ?s,
				file_size = ?s,
				created_at = UNIX_TIMESTAMP(),
				created_by = 1,
				type_id = ?i,
				checksum = ?s,
				preview_url = ?s,
				dimension_x = ?i,
				dimension_y = ?i,
				alt_text = ?s,
				duration = ?i
		', array(
			$file['url'],
			$file['name'],
			$file['extension'],
			$file['file_size'],
			$file['type_id'],
			$file['checksum'],
			$file['preview_url'],
			$file['dimension_x'],
			$file['dimension_y'],
			$file['alt_text'],
			$file['duration'],
		));

		$fileID = $result->id();
		$file = new Loader('gb',$this->_query);
		return $file->getByID($fileID);

	}
}