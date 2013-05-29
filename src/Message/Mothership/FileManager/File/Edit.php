<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\Event\FileEvent;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;

class Edit
{

	protected $_query;
	protected $_eventDispatcher;

	/**
	 * Populate the class with the dependencies.
	 *
	 * @param DBQuery             $query to run the DB queries
	 * @param DispatcherInterface $eventDispatcher To fire the events
	 */
	public function __construct(DBQuery $query, DispatcherInterface $eventDispatcher)
	{
		$this->_query = $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	/**
	 * Update changes to a file object into the DB. Method also fires FileEvent
	 *
	 * @todo make $userID an instance of User; $data could also be cloned instance of File too
	 * which would make it easier when passing in the data?
	 *
	 * @param  File   $file The $file object that needs to be updated
	 * @param  array $data 	An array of changes that have been made
	 * @param  int $userID 	The userId who made the change
	 *
	 * @return File|false 	updated instance of the $file or false is the file couldn't be updated
	 */
	public function save(File $file, $data, $userID = 1)
	{
		// Update any properties that have changed and update them on the
		// $file object
		foreach ($data as $key => $value) {
			if (isset($file->{$key}) && $file->{$key} != $value) {
				$file->{$key} = $value;
			}
		}

		$result = $this->_query->run('
			UPDATE
				file
			SET
				url = ?s,
				name = ?s,
				extension = ?s,
				file_size = ?s,
				updated_at = UNIX_TIMESTAMP(),
				updated_by = ?i,
				type_id = ?i,
				checksum = ?s,
				preview_url = ?s,
				dimension_x = ?i,
				dimension_y = ?i,
				alt_text = ?s,
				duration = ?i
			WHERE
				file_id = ?i
		', array(
			$file->url,
			$file->name,
			$file->extension,
			$file->fileSize,
			$userID,
			$file->typeID,
			$file->checksum,
			$file->previewUrl,
			$file->dimensionX,
			$file->dimensionY,
			$file->altText,
			$file->duration,
			$file->fileID,
		));

		// Initiate the event file
		$event = new FileEvent($file);

		// Dispatch the file created event
		$this->_eventDispatcher->dispatch(
			FileEvent::EDIT,
			$event
		);

		return $result->affected() ? $file : false;

	}
}