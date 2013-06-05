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
		$allFiles = $this->_services['filesystem.file.loader']->getAll();

		$data = array(
			'files' => $allFiles,
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
		$create = new Create(
			new Loader('en_GB', $this->get('db')),
			$this->get('db'),
			$this->get('event.dispatcher')
		);

		foreach($files->get('upload') as $upload) {

			try {
				// Move the file to the public dir and save it to the DB
				$filePath = 'cog://public/files/';
				$fileName = $upload->getClientOriginalName();
				
				// Check that the file doesnt exist in the destination
				if(file_exists($filePath.$fileName)) {
					// make a new (probably) unique filename
					$parts = pathinfo($fileName);
					$fileName = $parts['filename'].'-'.substr(uniqid(), 0, 8).'.'.$parts['extension'];
				}

				// Move her into position
				$upload->move($filePath, $fileName);

				$file = new FileSystemFile($filePath.$fileName);
				$create->save($file);
			} catch(\Exception $e) {
				// Clean up any files we couldnt save to the database
				if(file_exists($filePath.$fileName)) {
					unlink($filePath.$fileName);
				}
			}
			
		}
		
		return $this->redirect($this->generateUrl('filemanager.listing'));
	}
}
