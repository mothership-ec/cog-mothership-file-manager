<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\Event;
use Message\Cog\ValueObject\DateTimeImmutable;
use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;
use Message\User\UserInterface;

/**
 * Decorator for editing files.
 *
 * @author Danny Hannah <danny@message.co.uk>
 */
class Edit
{
	protected $_query;
	protected $_eventDispatcher;
	protected $_currentUser;

	/**
	 * Constructor.
	 *
	 * @param DBQuery             $query to run the DB queries
	 * @param DispatcherInterface $eventDispatcher The event dispatcher
	 * @param UserInterface       $user            To fire the events
	 */
	public function __construct(DBQuery $query, DispatcherInterface $eventDispatcher, UserInterface $user)
	{
		$this->_query           = $query;
		$this->_eventDispatcher = $eventDispatcher;
		$this->_currentUser     = $user;
	}

	/**
	 * Update a file in the database.
	 *
	 * Only alt text and tags are updated.
	 *
	 * @param  File $file The file with updated properties
	 *
	 * @return File|false The updated instance returned from the edit event
	 */
	public function save(File $file)
	{
		$file->authorship->update(new DateTimeImmutable, $this->_currentUser->id);

		$this->_query->run('
			UPDATE
				file
			SET
				updated_at = :updatedAt?i,
				updated_by = :updatedBy?in,
				alt_text = :altText?s
			WHERE
				file_id = :fileID?i
		', array(
			'updatedAt' => $file->authorship->updatedAt()->getTimestamp(),
			'updatedBy' => $file->authorship->updatedBy(),
			'altText' 	=> $file->altText,
			'fileID'	=> $file->id,
		));

		// Delete all the tags and then add the new ones in
		if ($file->tags) {
			$this->_query->run('
				DELETE FROM
					file_tag
				WHERE
					file_id = ?i
			', $file->id);

			$inserts = array();
			$values = '';
			end($file->tags);
			$lastKey = key($file->tags);
			foreach ($file->tags as $k => $tagName) {
				$values .= '(?i, ?s)'.($lastKey == $k ? '' : ',');
				$inserts[] = $file->id;
				$inserts[] = $tagName;
			}

			$this->_query->run('
				INSERT INTO
					file_tag (file_id, tag_name)
				VALUES
					' . $values
			, $inserts);
		}

		// Initiate the event file
		$event = new Event($file);

		// Dispatch the file edited event
		$this->_eventDispatcher->dispatch(
			Event::EDIT,
			$event
		);

		return $event->getFile();
	}
}