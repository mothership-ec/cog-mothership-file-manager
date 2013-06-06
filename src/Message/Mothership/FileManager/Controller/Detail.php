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
		$author = $this->_services['user.loader']->getByID($file->authorship->createdBy());
		$data = array(
			'file' => $file,
			'author' => $author,
		);
		return $this->render('::detail', $data);
	}

	public function edit($fileID)
	{
		// Load the file object
		$file = $this->_services['filesystem.file.loader']->getByID($fileID);
		// Load the changed data from the request
		if ($edits = $this->_services['request']->get('file')) {
			// Set the alt text
			$file->altText = $edits['alt_text'];
			// Turn the tags into an array and trim the values
			$file->tags = array_filter(array_map('trim', explode(',',$edits['tags'])));
			// Save the file
			$file = $this->_services['filesystem.file.edit']->save($file);
		}
		// Redriect the page to where is was
		return $this->redirect($this->generateUrl('filemanager.detail',array('fileID' => $file->fileID)));
	}

	public function delete($fileID)
	{
		// Load the file object
		$file = $this->_services['filesystem.file.loader']->getByID($fileID);
		// Check that the delete request has been sent
		if ($delete = $this->_services['request']->get('delete')) {
			$file = $this->_services['filesystem.file.delete']->delete($file);
		}
		return $this->redirect($this->generateUrl('filemanager.listing'));
	}
}
