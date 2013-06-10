<?php

namespace Message\Mothership\FileManager\Controller;
/**
 * Controller listing files from file manager
 *
 * @author Daniel Hannah <danny@message.co.uk>
 */
class Detail extends \Message\Cog\Controller\Controller
{

	/**
	 * Display the details of the given file
	 *
	 * @param  int $fileID fileID to be loaded and displayed
	 */
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

	/**
	 * Edit the given file attributes
	 *
	 * @param  int $fileID fileID to be updated
	 */
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
			if ($file = $this->_services['filesystem.file.edit']->save($file)) {
				$this->addFlash('success', $file->file->getBasename().' was updated successfully');
			} else {
				$this->addFlash('error', $file->file->getBasename().' could not be updated.');
			}
		}
		// Redirect the page to where is was
		return $this->redirect($this->generateUrl('ms.cp.file_manager.detail',array('fileID' => $file->id)));
	}

	/**
	 * Delete a fileID
	 *
	 * @param  int 	$fileID id of the file to be marked as deleted
	 */
	public function delete($fileID)
	{
		// Check that the delete request has been sent
		if ($delete = $this->_services['request']->get('delete')) {
			// Load the file object
			$file = $this->_services['filesystem.file.loader']->getByID($fileID);

			if ($file = $this->_services['filesystem.file.delete']->delete($file)) {
				$hash = $this->_generateHash($fileID);
				$this->addFlash('success', $file->file->getBasename().' was deleted. <a href="'.$this->generateUrl('ms.cp.file_manager.restore',array('fileID' => $file->id,'hash' => $hash)).'">Undo</a>');
			} else {
				$this->addFlash('error', $file->file->getBasename().' could not be deleted.');
			}

		}

		return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
	}

	/**
	 * Restore an image that has been deleted
	 * @param  int $fileID 		fileID of the file to be restored
	 * @param  [type] $hash   	hash of the fileID and userID to stop CSRF attacks
	 */
	public function restore($fileID, $hash)
	{
		// Load the file
		$file = $this->_services['filesystem.file.loader']->includeDeleted(true)->getByID($fileID);

		// If it doesn't match then redirect to the listing
		if (!$this->_compareHash($fileID, $hash)) {
			$this->addFlash('error', $file->file->getBasename().' could not be restored.');
			$this->redirect($this->generateUrl('ms.cp.file_manager.listing'));
		}

		if ($this->_services['filesystem.file.delete']->restore($file)) {
			$this->addFlash('success', $file->file->getBasename().' was restored successfully');
		} else {
			$this->addFlash('error', $file->file->getBasename().' could not be restored.');
		}
		return $this->redirect($this->generateUrl('ms.cp.file_manager.listing'));

	}

	/**
	 * Generate a hash of the userID and the fileID
	 *
	 * @param  int 		$fileID fileID to be hashed
	 * @return string 			the encyrptyed hash
	 */
	protected function _generateHash($fileID)
	{
		$hash = new \Message\Cog\Security\Hash\SHA1($this->_services['security.salt']);
		return $hash->encrypt(
			implode('-', array(
				$this->_services['user.current']->id,
				$fileID,
			))
		);

	}

	/**
	 * Compare the given hash and check it against what we expect
	 *
	 * @param  int 		$fileID The file to be restorted
	 * @param  string 	$hash   The hash to comapre against
	 * @return bool        		Resuklt of the hash comparison
	 */
	protected function _compareHash($fileID, $hash)
	{
		$check = implode('-', array(
			$this->_services['user.current']->id,
			$fileID,
		));
		// Compare the given hash with the one we expect
		$hashObj = new \Message\Cog\Security\Hash\SHA1($this->_services['security.salt']);
		// If it doesn't match then redirect to the listing
		return $hashObj->check($check, $hash);

	}
}
