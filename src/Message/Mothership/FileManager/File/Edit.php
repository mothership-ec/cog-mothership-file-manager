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
	 * @param  File   	$file The $file with updated properties
	 * @param  int 		$userID 	The userId who made the change
	 *
	 * @return File|false 	updated instance of the $file or false is the file couldn't be updated
	 */
	public function save(File $file, $userID = 1)
	{

		// Set the updated date on the object
		$date = new \DateTime;
		$file->updatedAt = $date->getTimestamp();
		$file->updatedBy = $userID;

		$result = $this->_query->run('
			UPDATE
				file
			SET
				url = :url?s,
				name = :name?s,
				extension = :extension?s,
				file_size = :fileSize?s,
				updated_at = :updatedAt?i,
				updated_by = :updatedBy?i,
				type_id = :typeID?i,
				checksum = :checksum?s,
				preview_url = :previewUrl?s,
				dimension_x = :dimensionX?i,
				dimension_y = :dimensionY?i,
				alt_text = :altText?s,
				duration = :duration?i
			WHERE
				file_id = :fileID?i
		', array(
			'url' 			=> $file->url,
			'name' 			=> $file->name,
			'extension' 	=> $file->extension,
			'fileSize' 		=> $file->fileSize,
			'updatedAt' 	=> $file->updatedAt,
			'updateBy' 		=> $file->updatedBy,
			'typeID' 		=> $file->typeID,
			'checksum' 		=> $file->checksum,
			'previewUrl' 	=> $file->previewUrl,
			'dimensionX' 	=> $file->dimensionX,
			'dimensionY' 	=> $file->dimensionY,
			'altText' 		=> $file->altText,
			'duration' 		=> $file->duration,
			'fileID' 		=> $file->fileID,
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