<?php

namespace Message\Mothership\FileManager\File;

use Message\Cog\DB\Entity\EntityLoaderCollection;

class FileProxy extends File
{
	private   $_loadedFields = [];
	
	/**
	 * @{inheritdoc}
	 * @param EntityLoaderCollection $loaderCollection loaders
	 */
	public function __construct()
	{
		parent::__construct();

		// $this->_loaderCollection = $loaderCollection;
	}

	public function getByID($id)
	{
		$_loaderCollection->get('file')->loadBasicByID($id);
	}

	public function getTags()
	{
		return $tags;
	}
}