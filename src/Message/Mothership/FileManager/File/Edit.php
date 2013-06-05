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
		$file->authorship->update($date, $userID);

		$result = $this->_query->run('
			UPDATE
				file
			SET
				updated_at = :updatedAt?i,
				updated_by = :updatedBy?i,
				alt_text = :altText?s
			WHERE
				file_id = :fileID?i
		', array(
			'updatedAt' => $file->authorship->updatedAt()->getTimestamp(),
			'updatedBy' => $file->authorship->updatedBy(),
			'altText' 	=> $file->altText,
			'fileID'	=> $file->fileID,
		));

		// Delete all the tags and then add the new ones in
		if ($file->tags) {
			$result = $this->_query->run('
				DELETE FROM
					file_tag
				WHERE
					file_id = ?',
				array($file->fileID)
			);

			$inserts = array();
			$values = '';
			end($file->tags);
			$lastKey = key($file->tags);
			foreach ($file->tags as $k => $tagName) {
				$values .= '(?i, ?s)'.($lastKey == $k ? '' : ',');
				$inserts[] = $file->fileID;
				$inserts[] = $tagName;
			}

			$result = $this->_query->run('
				INSERT INTO
					file_tag (file_id, tag_name)
				VALUES
					'.$values.'',
				$inserts
			);
		}

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