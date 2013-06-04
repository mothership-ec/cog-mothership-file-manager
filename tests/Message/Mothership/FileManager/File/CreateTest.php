<?php 

namespace Message\Mothership\FileManager\File;

use Message\Mothership\FileManager\File\File;

use Message\Cog\DB\Adapter\Faux\Connection as FauxConnection;
use Message\Cog\Test\Event\FauxDispatcher;

	/**
	 * @author Ewan Valentine <ewan@message.co.uk>
	 * @copyright Message 2013
	 */

class Create extends \PHPUnit_Framework_TestCase
{

	/**
	 * @access 	protected
	 */
	protected $_create;

	public function setUp()
	{
		$this->_create = new Create();
	}

	/**
	 * Checks no database exceptions are thrown
	 */
	public function testSavesSuccess()
	{
		// Returns values from database, compares them with original values
	}

	/**
	 * Checks database exception is thrown on save failure
	 * 
	 */
	public function testSaveFail()
	{
		// Test exception is thrown, once Create class is complete
	}
}