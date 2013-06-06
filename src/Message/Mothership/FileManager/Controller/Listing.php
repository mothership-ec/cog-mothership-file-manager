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
			$files = $this->_services['filesystem.file.loader']->getBySearchTerm($searchTerm);
			$search = $searchTerm;
		} else {
			$files = $this->_services['filesystem.file.loader']->getAll();
		}

		$data = array(
			'files' => $files,
			'search' => isset($search) ? $search : '',
		);
		return $this->render('::listing', $data);
	}

	public function upload()
	{

		$files = $this->get('request')->files;

		if(!$files->has('upload')) {
			return $this->redirect($this->generateUrl('filemanager.listing'));
		}

		// create a new file
		$create = $this->_services['filesystem.file.create'];

		foreach($files->get('upload') as $upload) {
			try {
				$file = $create->move($upload);
				$file = $create->save($file);
			} catch(\Exception $e) {
				$create->cleanup($file);
			}
		}

		return $this->redirect($this->generateUrl('filemanager.listing'));
	}
}
