<?php

namespace Message\Mothership\FileManager\Test\File;

use Message\Mothership\FileManager\File\Create;

use Message\Cog\DB\Adapter\Faux\Connection as FauxConnection;
use Message\Cog\Test\Event\FauxDispatcher;

class CreateTest extends \PHPUnit_Framework_TestCase
{
	const NEW_FILE_ID = 4;

	protected $_newFile;

	protected $_eventDispatcher;
	protected $_query;

	protected $_loader;
	protected $_create;

	public function setUp()
	{
		$this->_eventDispatcher = new FauxDispatcher;
		$this->_query           = $this->getMock('Message\Cog\DB\Query', array('query'), array(
			new FauxConnection(array('insertId' => 4))
		));
		$this->_loader = $this->getMock('Message\Mothership\FileManager\File\Loader', array('getByID'), array(), '', false);
		$this->_create = new Create(
			$this->_loader,
			$this->_query,
			$this->_eventDispatcher
		);

		$this->_newFile = $this->getMock('Message\Mothership\FileManager\File\File');
		$this->_newFile->id = self::NEW_FILE_ID;

		$this->_loader
			->expects($this->any())
			->method('getByID')
			->with(self::NEW_FILE_ID)
			->will($this->returnValue($this->_newFile));
	}

	public function testSave()
	{
		$array = array(
			'url' => 'image-url',
			'name' => 'testfile',
			'extension' => '.jpg',
			'file_size' => '12203409',
			'type_id' => '1',
			'checksum' => '12345',
			'preview_url' => 'http://testimage.com/test1',
			'dimension_x' => '12234',
			'dimension_y' => '1234',
			'alt_text' => 'Image of a tiger',
			'duration' => '100000',
		);
		$newFile = $this->_create->save($array);
		$this->assertEquals($newFile,$this->_newFile);

	}

	public function testEventDispatched()
	{
		// set up expectation on the mock
	}

	/**
	 * @depends testEventDispatched
	 */
	public function testCreateReturnsPageFromEvent()
	{

	}
}