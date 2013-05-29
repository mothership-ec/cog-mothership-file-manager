<?php 

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\File;
use Message\Cog\DB\Query;

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
		$this->_delete = new Delete();
	}

	/**
	 * Tests delete returns an object
	 */
	public function testDeleteReturnsObject()
	{
		// Test delete returns something
	}

	/**
	 * Test delete returns a username
	 */
	public function testDeleteReturnsUser()
	{
		// Test delete returns a delete by user

		$this->_delete->delete(1);
	}

	/**
	 * Test delete returns a date
	 */
	public function testDeleteReturnsDeletionDate()
	{
		// Test delete returns a deletion date
	}
}