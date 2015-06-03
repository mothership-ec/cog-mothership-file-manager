<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\Cog\Filesystem\File as FilesystemFile;
use Message\User\UserInterface;
use Message\Cog\ValueObject\Authorship;
use Message\Cog\ValueObject\DateTimeImmutable;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Decorator for creating files in the file manager.
 *
 * @author Danny Hannah <danny@message.co.uk>
 */
class Create
{
	protected $_query;
	protected $_eventDispatcher;
	protected $_loader;
	protected $_currentUser;

	/**
	 * Initialise the object and load dependancies.
	 *
	 * @param Loader              $loader load the File
	 * @param DBQuery             $query run the DB queries
	 * @param DispatcherInterface $eventDispatcher fire an event
	 */
	public function __construct(Loader $loader, DBQuery $query,
		DispatcherInterface $eventDispatcher, UserInterface $user)
	{
		$this->_loader          = $loader;
		$this->_query           = $query;
		$this->_eventDispatcher = $eventDispatcher;
		$this->_currentUser     = $user;
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
		$replace = $this->existsInFileManager($file);

		// Detect the file type
		$type = new Type;
		$typeID = $type->guess($file);

		$dimensionX = null;
		$dimensionY = null;

		$authorship = new Authorship;
		$authorship->create(
			new DateTimeImmutable,
			$this->_currentUser->id
		);

		// This should be abstracted at some point
		if (Type::IMAGE === $typeID) {
			list($dimensionX, $dimensionY) = getimagesize($file->getPathname());
		}

		if ($replace) {
			$oldFile = $this->_loader->includeDeleted(true)->getByID($replace);
			$this->_loader->includeDeleted(false);

			// if file is not deleted then reject
			if (!$oldFile->authorship->isDeleted()) {
				throw new Exception\FileExists('File already exists in File Manager', $replace);
			}

			$result = $this->_query->run('
				REPLACE INTO
					file
				SET
					file_id     = :id?i,
					url         = :url?s,
					name        = :name?s,
					extension   = :extension?s,
					file_size   = :size?i,
					created_at  = :createdAt?d,
					created_by  = :createdBy?i,
					type_id     = :typeID?i,
					checksum    = :checksum?s,
					preview_url = :previewUrl?sn,
					dimension_x = :dimX?in,
					dimension_y = :dimY?in,
					duration    = :duration?in,
					alt_text    = :altText?s
			', [
				'id'         => $replace,
				'url'        => $file->getPathname(),
				'name'       => $file->getFilename(),
				'extension'  => $file->getExtension(),
				'size'       => $file->getSize(),
				'createdAt'  => $authorship->createdAt(),
				'createdBy'  => $authorship->createdBy(),
				'typeID'     => $typeID,
				'checksum'   => $file->getChecksum(),
				'previewUrl' => null,              // Preview image for videos
				'dimX'       => $dimensionX,       // Image or video dimensions in x
				'dimY'       => $dimensionY,       // Image or video dimensions in y
				'duration'   => null,              // Duration in seconds for video/audio
				'altText'    => $oldFile->altText, // Preserve alt text
			]);
		} else {
			$result = $this->_query->run('
				INSERT INTO
					file
				SET
					url         = :url?s,
					name        = :name?s,
					extension   = :extension?s,
					file_size   = :size?i,
					created_at  = :createdAt?d,
					created_by  = :createdBy?i,
					type_id     = :typeID?i,
					checksum    = :checksum?s,
					preview_url = :previewUrl?sn,
					dimension_x = :dimX?in,
					dimension_y = :dimY?in,
					duration    = :duration?in
			', array(
				'url'        => $file->getPathname(),
				'name'       => $file->getFilename(),
				'extension'  => $file->getExtension(),
				'size'       => $file->getSize(),
				'createdAt'  => $authorship->createdAt(),
				'createdBy'  => $authorship->createdBy(),
				'typeID'     => $typeID,
				'checksum'   => $file->getChecksum(),
				'previewUrl' => null,        // Preview image for videos
				'dimX'       => $dimensionX, // Image or video dimensions in x
				'dimY'       => $dimensionY, // Image or video dimensions in y
				'duration'   => null,        // Duration in seconds for video/audio
			));
		} 

		// Load the file we just saved as an object
		$file = $this->_loader->getByID($result->id());

		// Initiate the event
		$event = new Event($file);

		// Dispatch the file created event
		$this->_eventDispatcher->dispatch(
			$event::CREATE,
			$event
		);

		// Return the File object from the event
		return $event->getFile();
	}

	public function move(UploadedFile $upload)
	{
		// Move the file to the public dir and save it to the DB
		$filePath = 'cog://public/files/';
		$fileName = $upload->getClientOriginalName();

		// Check that the file doesnt exist in the destination
		if (file_exists($filePath.$fileName)) {
			// make a new (probably) unique filename
			$parts = pathinfo($fileName);
			$fileName = $parts['filename'].'-'.substr(uniqid(), 0, 8).'.'.$parts['extension'];
		}

		// Move her into position
		$upload->move($filePath, $fileName);

		return new FileSystemFile($filePath.$fileName);
	}

	/**
	 * Remove any files that got moved into the file directory but we
	 * couldnt save to the database.
	 *
	 * @param  \SplFileInfo $file The file that should have been saved to the DB
	 *
	 * @return void
	 */
	public function cleanup(\SplFileInfo $file)
	{
		if (file_exists($file->getPathname())) {
			unlink($file->getPathname());
		}
	}

	/**
	 * Checks to see if a file is already in the system based on it's checksum.
	 *
	 * @param  FilesystemFile $file The file to check
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
}