<?php

namespace Message\Mothership\FileManager\File\Exception;

class BannedType extends \Exception
{
	protected $_file;

	public function __construct($message, $file, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->_file = $file;
	}
}
