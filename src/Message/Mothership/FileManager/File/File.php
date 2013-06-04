<?php

namespace Message\Mothership\FileManager\File;

/**
 * Represents the properties of a single File.
 *
 * @author Danny Hannah <danny@message.co.uk>
 * */
class File
{
	public $fileID;
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
}