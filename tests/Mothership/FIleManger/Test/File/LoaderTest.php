<?php

namespace Message\Mothership\FileManager\Test\File;

use Message\Mothership\FileManager\File\Edit;
use Message\Mothership\FileManager\File\File;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Test\Event\FauxDispatcher;
use Message\Cog\DB\Adapter\Faux\Connection AS FauxConnection;
use Message\Cog\DB\Query;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
	const FILE_ID = 4;

	protected $_file;

	protected $_eventDispatcher;
	protected $_query;

	protected $_loader;
	protected $_edit;

	public function setUp()
	{
		$connection = new FauxConnection;
		// For testDuplicateFieldNameException
		$connection->setPattern('/file_id([\s]+?)= [0-9]/us', array(
			array(
				'fileID'		=> 1,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+2 day'),
				'createdBy'     => 1,
				'updatedAt'     => strtotime('+1 day'),
				'updatedBy'     => 1,
				'deletedAt'		=> time(),
				'deletedBy'		=> 1,
			),
			array(
				'fileID'		=> 1,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
			array(
				'fileID'		=> 2,
				'name'     		=> 'pest',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
			array(
				'fileID'		=> 3,
				'name'     		=> 'rest',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
			array(
				'fileID'		=> 4,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+2 day'),
				'createdBy'     => 1,
				'updatedAt'     => strtotime('+1 day'),
				'updatedBy'     => 1,
				'deletedAt'		=> time(),
				'deletedBy'		=> 1,
			),
		));

		$this->_loader = new Loader('gb', new Query($connection));
	}

	public function testGetByID()
	{
		$file = $this->_loader->includeDeleted(true)->getByID(1);
		$this->assertTrue($file instanceof File);
		$this->assertTrue($file->fileID == 1);

		$ids = array(1,2,3,4);
		$files = $this->_loader->includeDeleted(true)->getByID($ids);
		$this->assertTrue(is_array($files));
		foreach ($files as $file) {
			$this->assertTrue($file instanceof File);
		}

		$connection = new FauxConnection;
		$connection->setPattern('/file_id([\s]+?)= [0-9]/us', array(
			array(
				'fileID'		=> 1,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+2 day'),
				'createdBy'     => 1,
				'updatedAt'     => strtotime('+1 day'),
				'updatedBy'     => 1,
				'deletedAt'		=> time(),
				'deletedBy'		=> 1,
			),
		));

		$loader = new Loader('gb', new Query($connection));

		$page = $loader->getByID(1);
		$this->assertFalse($page);

		$connection = new FauxConnection;
		$connection->setPattern('/file_id([\s]+?)= [0-9]/us', array(
		));
		$loader = new Loader('gb', new Query($connection));
		$page = $loader->getByID(1);
		$this->assertFalse($page);



    }

    public function testGetAll()
    {
		$connection = new FauxConnection;
		// For testDuplicateFieldNameException
		$connection->setPattern('/SELECT
				file_id
			FROM
				file/us', array(
			array(
				'file_id' => 1,
			),
			array(
				'file_id' => 2,
			),
		));

		$connection->setPattern('/file_id([\s]+?)= [0-9]/us', array(
			array(
				'fileID'		=> 1,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
			array(
				'fileID'		=> 2,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
		));

    	$loader = new Loader('gb', new Query($connection));

    	$all = $loader->getAll();
    	$this->assertTrue(is_array($all));
    	$this->assertEquals(count($all), 2);

    	foreach ($all as $file) {
    		$this->assertTrue($file instanceof File);
    	}
    }

    public function testGetByType()
    {
		$connection = new FauxConnection;
		$connection->setPattern('/type_id([\s]+?)= [0-9]/us', array(
			array(
				'file_id' => 1,
			),
			array(
				'file_id' => 2,
			),
		));

		$connection->setPattern('/file_id([\s]+?)= [0-9]/us', array(
			array(
				'fileID'		=> 1,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
			array(
				'fileID'		=> 2,
				'name'     		=> 'test',
				'extension'     => 'jpg',
				'fileSize'   	=> 12344,
				'createdAt'     => strtotime('+1 day'),
				'createdBy'     => 1,
				'updatedAt'     => null,
				'updatedBy'     => null,
				'deletedAt'		=> null,
				'deletedBy'		=> null,
			),
		));

    	$loader = new Loader('gb', new Query($connection));

    	$getByType = $loader->getByType(1);
    	$this->assertTrue(is_array($getByType));
    	$this->assertEquals(count($getByType), 2);

    	foreach ($getByType as $file) {
    		$this->assertTrue($file instanceof File);
    	}
    }

}