<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\Event\Event;
use Message\Mothership\FileManager\Event\FileEvent;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\Cog\Filesystem\File as FilesystemFile;

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
		$this->_query  = $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	/**
	 * Save the passed through data into the database and return a new instance
	 * of the saved file object and return it.
	 *
	 * @param  array 	$file data to be saved
	 * @return File 	return the saved File object
	 */
	public function save(FilesystemFile $file)
	{
		// Instead of allowing the file to be uploaded again we thrown an exception
		if($id = $this->existsInFileManager($file)) {
			throw new Exception\FileExists('File already exists in File Manager', $id);
		}

		$result = $this->_query->run('
			INSERT INTO
				file
			SET
				url         = ?s,
				name        = ?s,
				extension   = ?s,
				file_size   = ?s,
				created_at  = UNIX_TIMESTAMP(),
				created_by  = 1,
				type_id     = ?i,
				checksum    = ?s,
				preview_url = ?sn,
				dimension_x = ?i,
				dimension_y = ?i,
				alt_text    = ?s,
				duration    = ?in
		', array(
			$file->getPathname(),
			$file->getFilename(),
			$file->getExtension(),
			$file->getSize(),
			1,
			$file->getChecksum(),
			null, // Preview image for videos
			100, // Image or video dimensions in x
			100, // Image or video dimensions in y
			'', // Alt text is always empty
			null, // Duration in seconds for videos
		));

		// Get the fileID from the insertID
		$fileID = $result->id();

		// Load the file we just saved as an object and return it.
		$file = $this->_loader->getByID($fileID);

		$this->_dispatchEvents($file);

		// Return the object
		return $file;
	}

	public function existsInFileManager(FilesystemFile $file)
	{
		$checksum = $file->getChecksum();

		$result = $this->_query->run('
			SELECT
				file_id
			FROM
				file
			WHERE
				checksum = ?s
			LIMIT 1
		', $checksum);

		return count($result) ? $result->value() : false;
	}

	protected function _dispatchEvents($file)
	{
		// Initiate the event file
		$event = new FileEvent($file);

		// Dispatch the file created event
		$this->_eventDispatcher->dispatch(
			FileEvent::CREATE,
			$event
		);
	}
}