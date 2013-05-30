<?php

namespace Message\Mothership\FileManager\Test\File;

use Message\Mothership\FileManager\File\Edit;
use Message\Mothership\FileManager\File\File;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Test\Event\FauxDispatcher;
use Message\Cog\DB\Adapter\Faux\Connection;
use Message\Cog\DB\Query;

class EditTest extends \PHPUnit_Framework_TestCase
{
    protected $_file;


    public function testEdit()
    {
    	$despatcher = new FauxDispatcher;
		$connection = new Connection(array('affectedRows' => 1));

		$connection->setPattern('/file\.file_id = 1/m', array(
			array(
				'fileID' => 1,
				'url' => "hello",
				'name' => "Test",
				'extension' => ".jpg",
				'fileSize' => "1000",
				'createdAt' => 1369743806,
				'createdBy' => 1,
				'updatedAt' => null,
				'updatedBy' => null,
				'deletedAt' => null,
				'deletedBy' => null,
				'typeID' => 1,
				'checksum' => 1234556,
				'previewUrl' => null,
				'dimensionX' => 1200,
				'dimensionY' => 1300,
				'altText' => null,
				'duration' => null
			),
			array(
				'affectedRows' => 1,
			),
			array(
				'fileID' => 1,
				'url' => 'http://www.testurl.com/image1.jpg',
				'name' => 'Test image',
				'extension' => 'jpg',
				'file_size' => 1233445,
				'updated_at' => null,
				'updated_by' => null,
				'type_id' => 1,
				'checksum' => 123456,
				'preview_url' => 'http://preview.testurk.com/image1.jpg',
				'dimension_x' => 12345,
				'dimension_y' => 12345,
				'alt_text' => 'This is a test',
				'duration' => null,
			),
			// array(
			// 	'url' => 'http://www.testurl.com/image1.jpg',
			// 	'name' => 'Test image',
			// 	'extension' => 'jpg',
			// 	'file_size' => 1233445,
			// 	'updated_at' => null,
			// 	'updated_by' => null,
			// 	'type_id' => 1,
			// 	'checksum' => 123456,
			// 	'preview_url' => 'http://preview.testurk.com/image1.jpg',
			// 	'dimension_x' => 12345,
			// 	'dimension_y' => 12345,
			// 	'alt_text' => 'This is a test',
			// 	'duration' => null,
			// ),
		), 1);
		$this->assertTrue()
		// $db = new Query($connection);
		// $loader = new Loader('gb', $db);
		// $file = $loader->getByID(1);
		// $newFile = clone $file;
		// $newfile->altText = 'Hello Kitty';
		// $this->assertTrue($file instanceof File);

		// $edit = new Edit($db, $despatcher);
		// var_dump($edit->save($file, $newFile)); exit;
    }

}