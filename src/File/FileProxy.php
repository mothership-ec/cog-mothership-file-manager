<?php

namespace Message\Mothership\FileManager\File;

use Message\Cog\DB\Entity\EntityLoaderCollection;

class FileProxy extends File
{
	protected $_loader;
	protected $_loaded = false;
	
	/**
	 * @{inheritdoc}
	 * @param EntityLoaderCollection $loaderCollection loaders
	 */
	public function __construct(Loader $loader)
	{
		$this->_loader = $loader;
		parent::__construct();
	}

	public function getTags()
	{
		if ($this->_loaded) {
			return;
		}

		$tags = $this->_loader->getTagsForFile($this);
		
		if ($tags !== false) {
			$this->_tags = $this->_tags + $tags;
		}

		$this->_loaded = true;

		return parent::getTags();
	}
}