<?php

namespace Message\Mothership\FileManager\Controller;

use Message\Cog\Controller\Controller;
use Message\Cog\Filesystem;
use Message\Mothership\FileManager\File\File;

/**
 * Controller for printing files.
 *
 * @author Laurence Roberts <laurence@message.co.uk>
 */
class Printer extends Controller
{
	/**
	 * Print a file at the given path.
	 *
	 * @return Response
	 */
	public function printPath()
	{
		$path = $this->get('request')->get('path');

		$contents = file_get_contents($path);

		$response = $this->render('::print', array(
			'contents' => $contents,
		));

		if ("pdf" === pathinfo($path, PATHINFO_EXTENSION)) {
			$response->headers->set('Content-type', 'application/pdf');
		}

		return $response;
	}

	/**
	 * Print a file from it's id.
	 *
	 * @param  int $fileID fileID to be printed
	 * @return Response
	 */
	public function printFile($fileID)
	{
		$file = $this->get('file_manager.file.loader')->getByID($fileID);

		$path = $file->getFile()->getRealPath();

		$contents = file_get_contents($path);

		$response = $this->render('::print', array(
			'contents' => $contents,
		));

		if ("pdf" === pathinfo($path, PATHINFO_EXTENSION)) {
			$response->headers->set('Content-type', 'application/pdf');
		}

		return $response;
	}
}
