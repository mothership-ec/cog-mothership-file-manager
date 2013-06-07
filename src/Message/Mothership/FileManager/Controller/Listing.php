<?php

namespace Message\Mothership\FileManager\Controller;

use Message\Mothership\FileManager\File\Create;
use Message\Mothership\FileManager\File\Loader;

use Message\Cog\Filesystem\File as FilesystemFile;

/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Listing extends \Message\Cog\Controller\Controller
{

	public function index()
	{
		if ($searchTerm = $this->get('request')->query->get('search')) {
			$files = $this->get('filesystem.file.loader')->getBySearchTerm($searchTerm);
			$search = $searchTerm;
		} else {
			$files = $this->get('filesystem.file.loader')->getAll();
		}

		return $this->render('::listing', array(
			'files'  => $files,
			'search' => isset($search) ? $search : '',
		));
	}
}
