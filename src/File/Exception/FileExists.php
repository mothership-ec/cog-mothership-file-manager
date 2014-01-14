<?php

namespace Message\Mothership\FileManager\File\Exception;

class FileExists extends \Exception
{
	protected $_fileId;

	public function __construct($message, $fileId, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->_fileId = $fileId;
	}

	public function getFileId()
	{
		return $this->_fileId;
	}
}
