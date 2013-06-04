<?php

namespace Message\Mothership\FileManager\Controller;

/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Listing extends \Message\Cog\Controller\Controller
{

	public function index()
	{
		$allFiles = $this->_services['filesystem.file.loader']->getAll();

		return $this->render('::listing', array('files' => $allFiles, 'bob' => 'hello'));
	}
}
