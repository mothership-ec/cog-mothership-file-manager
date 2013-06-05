<?php

namespace Message\Mothership\FileManager\Controller;

/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Detail extends \Message\Cog\Controller\Controller
{

	public function index($fileID)
	{

		$file = $this->_services['filesystem.file.loader']->getByID($fileID);
		$data = array(
			'file' => $file,
		);
		return $this->render('::detail', $data);
	}
}
