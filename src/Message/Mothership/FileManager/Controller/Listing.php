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
		return $this->render('::listing', array(
			'files'      => $this->get('filesystem.file.loader')->getAll(),
			'searchTerm' => null,
		));
	}

	public function searchRedirect()
	{
		if ($search = $this->get('request')->request->get('file_search')) {
			return $this->redirect($this->generateURL('ms.cp.file_manager.search', array(
				'term' => $search['term'],
			)));
		}

		return $this->redirect($this->generateURL('ms.cp.file_manager.listing'));
	}

	public function search($term)
	{
		return $this->render('::listing', array(
			'files'      => $this->get('filesystem.file.loader')->getBySearchTerm($term),
			'searchTerm' => $term,
		));
	}
}
