<?php

namespace Message\Mothership\FileManager\Controller;

/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Detail extends \Message\Cog\Controller\Controller
{
	protected $_file;

	public function index($fileID)
	{
		$file = $this->_services['filesystem.file.loader']->getByID($fileID);

		$data = array(
			'file' => $file,
		);
		return $this->render('::detail', $data);
	}

	public function edit($fileID)
	{
		// Load the changed data from the request
		$edits = $this->_services['request']->get('file');
		// Load the file object
		$file = $this->_services['filesystem.file.loader']->getByID($fileID);
		// Set the alt text
		$file->altText = $edits['alt_text'];
		// Turn the tags into an array and trim the values
		$file->tags = array_filter(array_map('trim', explode(',',$edits['tags'])));
		// Save the file
		$file = $this->_services['filesystem.file.edit']->save($file);
		// Redriect the page to where is was
		return $this->redirect($this->generateUrl('filemanager.detail',array('fileID' => $file->fileID)));
	}
}
