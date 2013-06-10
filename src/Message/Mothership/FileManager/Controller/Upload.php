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
 * @author James Moss <james@message.co.uk>
 */
class Upload extends \Message\Cog\Controller\Controller
{
	public function index()
	{
		$files = $this->get('request')->files;

		if (!$files->has('upload')) {
			return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
		}

		$create = $this->get('filesystem.file.create');

		foreach ($files->get('upload') as $upload) {
			try {
				if(is_null($upload)) {
					continue;
				}

				$file    = $create->move($upload);
				$fileObj = $create->save($file);
				$this->addFlash('success', sprintf('%s was successfully uploaded.', $file->getBasename()));
			} catch(BannedType $e) {
				$create->cleanup($file);
				$this->addFlash('error', sprintf('%s was not uploaded because it is a banned file type.', $file->getBasename()));
			} catch(FileExists $e) {
				$create->cleanup($file);
				$this->addFlash('notice', sprintf(
					'%s already exists. <a href="%s">View this file</a>',
					$this->generateUrl('ms.cp.file_manager.detail', array('fileID' => $e->getFileId()))
				));
			}
		}

		return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
	}
}
