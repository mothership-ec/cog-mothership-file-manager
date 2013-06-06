<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\Event\Event;
use Message\Mothership\FileManager\Event\FileEvent;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\Cog\Filesystem\File as FilesystemFile;
use Message\User\User;

use Symfony\Component\HttpFoundation\File;

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
	public function __construct(Loader $loader, DBQuery $query, DispatcherInterface $eventDispatcher, User $user)
	{
		$this->_loader = $loader;
		$this->_query  = $query;
		$this->_eventDispatcher = $eventDispatcher;
		$this->_user = $user;
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

		// detect the type
		$type = new Type;
		$typeID = $type->guess($file);

		$dimensionX = null;
		$dimensionY = null;

		// This should be abstracted at some point.
		if($typeID === Type::IMAGE) {
			// get the dimensions for an image
			list($dimensionX, $dimensionY) = getimagesize($file->getPathname());
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
				created_by  = ?i,
				type_id     = ?i,
				checksum    = ?s,
				preview_url = ?sn,
				dimension_x = ?in,
				dimension_y = ?in,
				alt_text    = ?s,
				duration    = ?in
		', array(
			$file->getPathname(),
			$file->getFilename(),
			$file->getExtension(),
			$file->getSize(),
			$this->_user->id,
			$typeID,
			$file->getChecksum(),
			null, // Preview image for videos
			$dimensionX, // Image or video dimensions in x
			$dimensionY, // Image or video dimensions in y
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

	public function move($upload)
	{
		// Move the file to the public dir and save it to the DB
		$filePath = 'cog://public/files/';
		$fileName = $upload->getClientOriginalName();

		// Check that the file doesnt exist in the destination
		if(file_exists($filePath.$fileName)) {
			// make a new (probably) unique filename
			$parts = pathinfo($fileName);
			$fileName = $parts['filename'].'-'.substr(uniqid(), 0, 8).'.'.$parts['extension'];
		}

		// Move her into position
		$upload->move($filePath, $fileName);

		$file = new FileSystemFile($filePath.$fileName);
	}

	/**
	 * Remove any files that got moved into the file directory but we
	 * couldnt save to the database.
	 *
	 * @param  \SplFileInfo $file The file that should have been saved to the DB
	 *
	 * @return void
	 */
	public function cleanup($file)
	{
		if(file_exists($file->getPathname())) {
			unlink($file->getPathname());
		}
	}

	/**
	 * Checks to see if a file is already in the system based on it's checksum.
	 *
	 * @param  Filesystem\File $file The file to check
	 *
	 * @return boolean|int          Returns the file ID if the checksum already 
	 *                              exists, false if it doesn't.
	 */
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

	/**
	 * Fires off file create events
	 *
	 * @param  FileSystem\File $file The newly create file
	 *
	 * @return void
	 */
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

	protected function _detectType(FilesystemFile $file)
	{
		// Get the files mimetype
		


	}
}