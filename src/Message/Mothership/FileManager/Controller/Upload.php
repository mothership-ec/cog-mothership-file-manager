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

		$create = $this->get('file_manager.file.create');
		$uploads = $files->get('upload');

		foreach ($uploads['new_upload'] as $upload) {
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

				$file = $this->get('file_manager.file.loader')
							->includeDeleted(true)
							->getByID($e->getFileID());
				if ($file->authorship->deletedAt()) {
					$this->addFlash('notice', $file->file->getBasename().' already exists, but was deleted. Do you want to <a href="'.$this->generateUrl('ms.cp.file_manager.restore',array('fileID' => $file->id)).'">Restore it?</a>');
				} else {
					$this->addFlash('notice', sprintf(
						'%s already exists. <a href="%s">View this file</a>',
						$file->name,
						$this->generateUrl('ms.cp.file_manager.detail', array('fileID' => $e->getFileId()))
					));
				}
			}
		}

		// If only a single file was uploaded, redirect to it's detail page.
		if (count($uploads['new_upload']) == 1 and isset($fileObj) and $fileObj->id) {
			return $this->redirect($this->generateUrl('ms.cp.file_manager.detail', array(
				'fileID' => $fileObj->id
			)));
		}

		// Otherwise redirect back to the main listing page.
		return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
	}
}
