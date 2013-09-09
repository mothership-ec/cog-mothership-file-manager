<?php

namespace Message\Mothership\FileManager\Controller;

use Message\Mothership\FileManager\File\Create;
use Message\Mothership\FileManager\File\Loader;
use Message\Mothership\FileManager\File\Type;

use Message\Cog\Filesystem\File as FilesystemFile;

/**
 * Controller listing files from file manager
 *
 * @author Danny Hannah <danny@message.co.uk>
 * @author Laurence Roberts <laurence@message.co.uk>
 */
class Listing extends \Message\Cog\Controller\Controller
{
	public function index()
	{
		$filterSortForm = $this->_getFilterSortForm();
		$filterSortData = $filterSortForm->getFilteredData();

		$files = null;

		// If a filters has been set, attempt to load files with the filter.
		if ($filterSortData['filter']) {

			// Check if this is a file type filter.
			if (in_array($filterSortData['filter'], $this->_getFileTypes())) {
				$files = $this->get('file_manager.file.loader')->getByType($filterSortData['filter']);
			}
		}

		// If no files have yet been selected, load them all.
		if ($files === null) {
			$files = $this->get('file_manager.file.loader')->getAll();
		}

		// If a sort method has been set, re-order the files.
		if ($filterSortData['sort']) {
			$this->_sortFiles($files, $filterSortData['sort']);
		}

		return $this->render('::listing', array(
			'files'       => $files,
			'searchTerm'  => null,
			'form'        => $this->_getUploadForm(),
			'search_form' => $this->_getSearchForm(),
			'filter_sort_form' => $filterSortForm,
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
			'files'       => $this->get('file_manager.file.loader')->getBySearchTerm($term),
			'searchTerm'  => $term,
			'form'        => $this->_getUploadForm(),
			'search_form' => $this->_getSearchForm(),
		));
	}

	protected function _getUploadForm()
	{
		$form = $this->get('form')
			->setName('upload')
			->setMethod('POST')
			->setAction($this->generateUrl('ms.cp.file_manager.upload'));
		$form->add('new_upload', 'file', 'upload an image', array('attr' => array('multiple' => 'multiple')));

		return $form;
	}

	protected function _getSearchForm()
	{
		$form = $this->get('form')
			->setName('file_search')
			->setMethod('POST')
			->setAction($this->generateUrl('ms.cp.file_manager.search.forward'));
		$form->add('term', 'search', 'Enter search term...');

		return $form;
	}

	protected function _getFilterSortForm()
	{
		$form = $this->get('form');

		$form->add('sort', 'choice', 'Sort By', array(
			'choices' => array(
				'name' => 'Name',
				'date-uploaded' => 'Date uploaded',
			),
			'empty_value' => 'Sort by...',
		));

		$form->add('filter', 'choice', 'Filter By', array(
			'choices' => array(
				'File Types' => $this->_getFileTypes()
			),
			'empty_value' => 'Filter by...',
		));

		return $form;
	}

	/**
	 * Get the list of file types.
	 * 
	 * @return array
	 */
	protected function _getFileTypes()
	{
		return array(
			Type::IMAGE    => 'Image',
			Type::DOCUMENT => 'Document',
			Type::VIDEO    => 'Video',
			Type::OTHER    => 'Other',
		);
	}

	/**
	 * Sort the files against the chosen method.
	 * 
	 * @param  array  $files  Files to sort
	 * @param  string $sortBy Sorting method
	 * 
	 * @return array          Sorted files
	 */
	protected function _sortFiles($files, $sortBy)
	{
		switch ($sortBy) {
			case 'name':
				$sortFn = function($a, $b) {
					return strcmp($a->name, $b->name);
				};
				break;

			case 'date-uploaded':
				$sortFn = function($a, $b) {
					if ($a->authorship->createdAt() == $b->authorship->createdAt()) return 0;
					return ($a->authorship->createdAt() < $b->authorship->createdAt()) ? -1 : 1;
				};
				break;

			default:
				// If the sort method is invalid, just return and do not sort the files.
				return;
		}

		usort($files, $sortFn);
	}
}
