<?php

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\File;
use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as Query;

	/**
	 * Author Ewan Valentine <ewan@message.co.uk> 
	 * Copyright Message 2013
	 *
	 * Basic file deletion class for database records
	 * Saves who deleted a file and when.
	 */
class Delete
{


	protected $_query;
	protected $_eventDispatcher;

	/**
	 * @access public
	 * @param Query $query
	 */
	public function __construct(Loader $loader, Query $query, DispatcherInterface $eventDispatcher)
	{
		$this->_loader			= $loader;
		$this->_query 			= $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	/**
	 * Sets file in database marked as deleted.
	 * Keeps a record of who a file was deleted by and when.
	 *
	 * @return $this->_file
	 * @access public
	 * @param $user
	 */
	public function delete(File $file)
	{
	
		$file->authorship->delete(new \Datetime, 1);

		/** Query to set deletion info */
		$result = $this->_query->run('
			UPDATE
				file
			SET
				deleted_at = :dl_at?i,
				deleted_by = :dl_by?i
			WHERE
				file_id = :file_id?i
		', array(
				'dl_at' 	=> $file->authorship->deletedAt()->getTimestamp(),
				'dl_by' 	=> $file->authorship->deletedBy(),
				'file_id' 	=> $file->id,
			));

		$this->_eventDispatcher->dispatch(
			FileEvent::DELETE,
			new FileEvent($file)
		);

		return $eventDispatcher->getFile();
	}

	public function restore()
	{

		$file->authorship->restore();

		$result = $this->_query->run('
			UPDATE 
				file
			SET
				deleted_at = NULL,
				deleted_by = NULL
			WHERE
				file_id = file_id?i
		', array(
				'up_at'		=> $file->authorship->updated_at()->getTimestamp(),
				'up_by' 	=> $file->authorship->updated_by()->getTimestamp(),
				'file_id' 	=> $file-id,
			));

		$this->_eventDispatcher->dispatch(
			FileEvent::RESTORE,
			new FileEvent($file)
		);

		return $eventDispatcher->getFile();
	}
}



