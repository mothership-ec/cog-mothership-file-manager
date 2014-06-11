<?php

namespace Message\Mothership\FileManager\File;

use Message\ImageResize\ResizableInterface;

use Message\Cog\Filesystem\File as FileSystemFile;
use Message\Cog\ValueObject\Authorship;

/**
 * Represents the properties of a single File.
 *
 * @author Danny Hannah <danny@message.co.uk>
 */
class File implements ResizableInterface
{
	public $id;
	public $url;
	public $name;
	public $authorship;
	public $extension;
	public $fileSize;
	public $typeID;
	public $checksum;
	public $previewUrl;
	public $dimensionX;
	public $dimensionY;
	public $altText;
	public $duration;
	public $tags;

	public $file;

	protected $_fileRef;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->authorship = new Authorship;
	}

	/**
	 * Magic sleep method. Called before this object is serialized.
	 *
	 * Here, we drop the instance of `Filesystem\File` in Cog because it extends
	 * `SplFileInfo` so it cannot be serialized. We save the file path so we can
	 * re-instantiate it on `__wakeup()`.
	 *
	 * @return array Property names to include in serialization (all except for
	 *               `file`)
	 */
	public function __sleep()
	{
		// Save the file path
		$this->_fileRef = ($this->file->getReference()) ?: $this->file->getRealPath();

		// Get all properties for this object
		$vars = get_object_vars($this);

		// Remove the `file` property
		unset($vars['file']);

		return array_keys($vars);
	}

	/**
	 * Magic wake up method. Called after this object is unserialized.
	 *
	 * Here, we rebuild the instance of `Filesystem\File` in Cog from the saved
	 * file path.
	 */
	public function __wakeup()
	{
		$this->file = new FileSystemFile($this->_fileRef);
	}

	public function getUrl()
	{
		return $this->file->getPublicUrl();
	}

	public function getAltText()
	{
		return $this->altText;
	}

	/**
	 * Check if this file is "image" type.
	 *
	 * @return boolean True if this file is an image
	 */
	public function isTypeImage()
	{
		return Type::IMAGE === $this->typeID;
	}

	/**
	 * Check if this file is "document" type.
	 *
	 * @return boolean True if this file is a document
	 */
	public function isTypeDocument()
	{
		return Type::DOCUMENT === $this->typeID;
	}

	/**
	 * Check if this file is "video" type.
	 *
	 * @return boolean True if this file is a video
	 */
	public function isTypeVideo()
	{
		return Type::VIDEO === $this->typeID;
	}

	/**
	 * Check if this file is "other" type.
	 *
	 * @return boolean True if this file is another type of file
	 */
	public function isTypeOther()
	{
		return Type::OTHER === $this->typeID;
	}
}