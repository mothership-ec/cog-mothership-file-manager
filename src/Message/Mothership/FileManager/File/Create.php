<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\Event\Event;
use Message\Mothership\FileManager\Event\FileEvent;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\Mothership\FileManager\File\Loader;

class Create
{

	protected $_query;
	protected $_eventDispatcher;

	/**
	 * Initiallise the object and load dependancies.
	 *
	 * @param Loader             $loader load the File
	 * @param DBQuery             $query run the DB queries
	 * @param DispatcherInterface $eventDispatcher fire an event
	 */
	public function __construct(Loader $loader, DBQuery $query, DispatcherInterface $eventDispatcher)
	{
		$this->_loader = $loader;
		$this->_query = $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	/**
	 * Save the passed through data into the database and return a new instance
	 * of the saved file object and return it.
	 *
	 * @param  array 	$file data to be saved
	 * @return File 	return the saved File object
	 */
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

		// Get the fileID from the insertID
		$fileID = $result->id();

		// Load the file we just saved as an object and return it.
		$file = $this->_loader->getByID($fileID);

		// Initiate the event file
		$event = new FileEvent($file);

		// Dispatch the file created event
		$this->_eventDispatcher->dispatch(
			FileEvent::CREATE,
			$event
		);

		// Return the object
		return $file;

	}
}