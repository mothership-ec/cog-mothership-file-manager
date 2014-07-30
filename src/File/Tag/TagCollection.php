<?php

namespace Message\Mothership\FileManager\File\Tag;

use Message\Mothership\FileManager\File\Loader as FileLoader;
use Message\Mothership\FileManager\File\File;
use Message\Cog\ValueObject\Collection;

/**
 * Collection of tags for the File
 * 
 * @author Sam Trangmar-Keates <sam@message.co.uk>
 */
class TagCollection extends Collection
{
	protected $_file;
	protected $_loaded = false;
	protected $_fileLoader;

	public function __construct(File $file, FileLoader $fileLoader)
	{
		$this->_file       = $file;
		$this->_fileLoader = $fileLoader;
		parent::__construct([]);
	}

	/**
	 * @{inheritdocs}
	 */
	public function get($e)
	{
		if (!$this->_loaded) {
			$this->_loadTags();
		}

		return parent::get($e);
	}

	/**
	 * @{inheritdocs}
	 */
	public function add($e)
	{
		if (!$this->_loaded) {
			$this->_loadTags();
		}

		return parent::add($e);
	}

	/**
	 * @{inheritdocs}
	 */
	public function remove($e)
	{
		if (!$this->_loaded) {
			$this->_loadTags();
		}

		return parent::remove($e);
	}

	/**
	 * @{inheritdocs}
	 */
	public function exists($e)
	{
		if (!$this->_loaded) {
			$this->_loadTags();
		}

		return parent::exists($e);
	}

	/**
	 * @{inheritdocs}
	 */
	public function all()
	{
		if (!$this->_loaded) {
			$this->_loadTags();
		}

		return parent::all();
	}

	/**
	 * Loads the tags
	 */
	protected function _loadTags()
	{
		if ($this->_loaded) {
			return;
		}

		$tags = $this->_fileLoader->getTagsForFile($this->_file);

		foreach ($tags as $tag) {
			parent::add($tag);
		}
		$this->_loaded = true;
	}
}