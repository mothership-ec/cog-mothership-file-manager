<?php

namespace Message\Mothership\FileManager\Event;

/**
 * Event class for events relating to files.
 *
 * @author Danny Hannah <danny@message.co.uk>
 */
class Event extends \Message\Cog\Event\Event
{
	const CREATE  = 'file_manager.file.create';
	const EDIT    = 'file_manager.file.edit';
	const DELETE  = 'file_manager.file.delete';
	const RESTORE = 'file_manager.file.restore';

	protected $_file;

	/**
	 * Constructor.
	 *
	 * @see setFile()
	 *
	 * @param File $file The relevant file for this event
	 */
	public function __construct(File $file)
	{
		$this->setFile($file);
	}

	/**
	 * Get the file set for this event.
	 *
	 * @return File
	 */
	public function getFile()
	{
		return $this->_file;
	}

	/**
	 * Set the file for this event.
	 *
	 * @param File $file
	 */
	public function setFile(File $file)
	{
		$this->_file = $file;
	}
}