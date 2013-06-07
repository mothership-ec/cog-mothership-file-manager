<?php

namespace Message\Mothership\FileManager\Controller;

use Message\Mothership\FileManager\File\Create;
use Message\Mothership\FileManager\File\Loader;

use Message\Mothership\FileManager\File\Exception\BannedType;
use Message\Mothership\FileManager\File\Exception\FileExists;

use Message\Cog\Filesystem\File as FilesystemFile;

/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Upload extends \Message\Cog\Controller\Controller
{
	public function index()
	{
		$files = $this->get('request')->files;

		if(!$files->has('upload')) {
			return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
		}

		// create a new file
		$create = $this->_services['filesystem.file.create'];
		$messages = array();

		foreach($files->get('upload') as $upload) {
			try {
				if(is_null($upload)) {
					continue;
				}

				$file    = $create->move($upload);
				$fileObj = $create->save($file);
				$messages[] = array('error', $file->getBasename().' was successfully uploaded.');
			} catch(BannedType $e) {
				$create->cleanup($file);
				$messages[] = array('error', $file->getBasename().' is a banned file type.');
			} catch(FileExists $e) {
				$create->cleanup($file);
				$url = $this->generateUrl('ms.cp.file_manager.detail', array('fileID' => $e->getFileId()));
				$messages[] = array('error', $file->getBasename().' already exists. <a href="'.$url.'">edit</a>');
			}
		}

		foreach($messages as $message) {
			$this->get('http.session')->getFlashBag()->add($message[0], $message[1]);
		}

		return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
	}
}
