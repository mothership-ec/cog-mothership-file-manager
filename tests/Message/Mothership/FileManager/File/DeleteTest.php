<?php 

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\File;
use Message\Cog\DB\Adapter\Faux\Connection as FauxConnection;
use Message\Cog\Test\Event\FauxConnection;

class Delete extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var string
	 * @access protected
	 */
	protected $_delete;

	/**
	 * Setup.
	 *
	 * Mock a use case. User a deletes file b
	 * Delete expects `user` (int)
	 */
	public function setUp()
	{
		$this->_eventDispatcher = new FauxDispatcher;
		$this->_delete = $this->getMock('Message\Cog\DB\Query', array('query'),array(
			new FauxDispatcher(array('deleteId' => 4))
		));

		$this->_nestedSetHelper = $this->getMock('Message\Cog\DB\NestedSetHelper', array('getById'));

	}

	/**
	 * Tests delete returns an object
	 */
	public function testDeleteReturnsObject()
	{

	}

	/**
	 * Test delete returns a username
	 */
	public function testDeleteReturnsUser()
	{
		// Test delete returns a delete by user
	}

	/**
	 * Test delete returns a date
	 */
	public function testDeleteReturnsDeletionDate()
	{
		// Test delete returns a deletion date
	}
}

