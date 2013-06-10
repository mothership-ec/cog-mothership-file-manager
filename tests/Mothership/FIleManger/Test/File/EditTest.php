<?php

namespace Message\Mothership\FileManager\Test\File;

use Message\Mothership\FileManager\File\Edit;
use Message\Mothership\FileManager\File\File;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Test\Event\FauxDispatcher;
use Message\Cog\DB\Adapter\Faux\Connection AS FauxConnection;
use Message\Cog\DB\Query;
use Message\Cog\ValueObject\Authorship;

class EditTest extends \PHPUnit_Framework_TestCase
{
	const FILE_ID = 4;

	protected $_file;

	protected $_eventDispatcher;
	protected $_query;

	protected $_loader;
	protected $_edit;

	public function setUp()
	{
		$this->_eventDispatcher = new FauxDispatcher;
		$this->_query           = $this->getMock('Message\Cog\DB\Query', array('query'), array(
			new FauxConnection(array('affectedRows' => 1))
		));
		$this->_loader = $this->getMock('Message\Mothership\FileManager\File\Loader', array('getByID'), array(), '', false);
		$this->_edit = new Edit(
			$this->_query,
			$this->_eventDispatcher
		);

		$this->_file = new File;
		$this->_file->id = self::FILE_ID;
		$this->_file->authorship = new Authorship;

		$this->_loader
			->expects($this->any())
			->method('getByID')
			->with(self::FILE_ID)
			->will($this->returnValue($this->_file));


		// $this->_edit
		// 	->expects($this->any())
		// 	->method('save')
		// 	->with($this->_file)
		// 	->will($this->returnValue($this->_updatedFile));
	}

	public function testSave()
	{
		$updatedFile = $this->_edit->save($this->_loader->getByID(self::FILE_ID));
		$dateTime = new \DateTime;
		$this->assertEquals($updatedFile->authorship->updatedAt()->getTimestamp(), $dateTime->getTimestamp(), 2);
		$this->assertTrue(!is_null($updatedFile->authorship->updatedBy()));
		$this->assertTrue($updatedFile->id == self::FILE_ID);
    }

}