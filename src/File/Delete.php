<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\File;
use Message\Mothership\FileManager\File\Event;

use Message\User\UserInterface;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\ValueObject\DateTimeImmutable;
use Message\Cog\DB\Query as Query;

/**
 * Decorator for deleting & restoring files.
 *
 * @author Ewan Valentine <ewan@message.co.uk>
 */
class Delete
{
	protected $_query;
	protected $_eventDispatcher;
	protected $_currentUser;

	/**
	 * Constructor.
	 *
	 * @param Query               $query           Database query instance
	 * @param DispatcherInterface $eventDispatcher The event dispatcher
	 * @param UserInterface       $user            The currently logged in user
	 */
	public function __construct(Query $query, DispatcherInterface $eventDispatcher, UserInterface $user)
	{
		$this->_query 			= $query;
		$this->_eventDispatcher = $eventDispatcher;
		$this->_currentUser     = $user;
	}

	/**
	 * Mark a file as deleted.
	 *
	 * @param File $file The file to be deleted
	 *
	 * @return File      The deleted file returned from the delete event
	 */
	public function delete(File $file)
	{
		$file->authorship->delete(new DateTimeImmutable, $this->_currentUser->id);

	
		// This code will mark a file as deleted so that it can be restored in future.

		$this->_query->run('
			UPDATE
				file
			SET
				deleted_at = :dl_at?d,
				deleted_by = :dl_by?in
			WHERE
				file_id = :file_id?i
		', array(
			'dl_at'   => $file->authorship->deletedAt(),
			'dl_by'   => $file->authorship->deletedBy(),
			'file_id' => $file->id,
		));

		$event = new Event($file);

		$this->_eventDispatcher->dispatch(
			Event::DELETE,
			$event
		);

		return $event->getFile();
	}

	/**
	 * Restore a previously deleted file.
	 *
	 * @param  File   $file The file to restore
	 *
	 * @return File         The restored file returned from the restore event
	 */
	public function restore(File $file)
	{
		$file->authorship->restore();

		$this->_query->run('
			UPDATE
				file
			SET
				deleted_at = NULL,
				deleted_by = NULL
			WHERE
				file_id = ?i
		', $file->id);

		$event = new Event($file);

		$this->_eventDispatcher->dispatch(
			Event::RESTORE,
			$event
		);

		return $event->getFile();
	}
}