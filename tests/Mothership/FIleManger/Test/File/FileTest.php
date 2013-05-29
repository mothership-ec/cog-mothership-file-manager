<?php

namespace Message\Mothership\FileManager\Test\File;

use Message\Mothership\FileManager\File\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    protected $_page;

    public function setUp()
    {
        $this->_file = new File;
    }

}