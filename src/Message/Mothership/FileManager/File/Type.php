<?php

namespace Message\Mothership\FileManager\File;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
* Guesses the type (img/doc/vid/other) of a file.
*/
class Type
{
	const IMAGE    = 1;
	const DOCUMENT = 2;
	const VIDEO    = 3;
	const OTHER    = 4;

	public function guess(\SplFileInfo $file)
	{
		// Get the mimetype
		$guesser = MimeTypeGuesser::getInstance();
		$mimetype = $guesser->guess($file->getPathname());

		if($this->_isBannedMimetype($mimetype)) {
			throw new Exception\BannedType(sprintf('`%s` is not an allowed file type', $file->getBasename()), $file);
		}

		$mappings = $this->_getMappings();

		foreach($mappings as $typeID => $mimetypes) {
			if(in_array($mimetype, $mimetypes)) {
				return $typeID;
			}
		}

		return self::OTHER;
	}

	protected function _getMappings()
	{
		return array(
			self::IMAGE => array(
				'image/jpeg',
				'image/pjpeg',
				'image/gif',
				'image/png',
			),
			self::DOCUMENT => array(
				// text documents
				'application/msword',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'application/x-iwork-pages-sffpages',
				'application/pdf',
				'text/plain',
				'application/rtf',
				'application/x-rtf',
				'text/richtext',

				// spreadsheets
				'application/excel',
				'application/vnd.ms-excel',
				'application/x-excel',
				'application/x-msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'text/csv',
				'application/x-iwork-numbers-sffnumbers',

				// presentations
				'application/vnd.ms-powerpoint',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'application/vnd.openxmlformats-officedocument.presentationml.template',
				'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'application/x-iwork-keynote-sffkey',
			),
			self::VIDEO => array(
				'application/annodex',
				'application/mp4',
				'application/ogg',
				'application/vnd.rn-realmedia',
				'application/x-matroska',
				'video/3gpp',
				'video/3gpp2',
				'video/annodex',
				'video/divx',
				'video/flv',
				'video/h264',
				'video/mp4',
				'video/mp4v-es',
				'video/mpeg',
				'video/mpeg-2',
				'video/mpeg4',
				'video/ogg',
				'video/ogm',
				'video/quicktime',
				'video/ty',
				'video/vdo',
				'video/vivo',
				'video/vnd.rn-realvideo',
				'video/vnd.vivo',
				'video/webm',
				'video/x-bin',
				'video/x-cdg',
				'video/x-divx',
				'video/x-dv',
				'video/x-flv',
				'video/x-la-asf',
				'video/x-m4v',
				'video/x-matroska',
				'video/x-motion-jpeg',
				'video/x-ms-asf',
				'video/x-ms-dvr',
				'video/x-ms-wm',
				'video/x-ms-wmv',
				'video/x-msvideo',
				'video/x-sgi-movie',
				'video/x-tivo',
				'video/avi',
				'video/x-ms-asx',
				'video/x-ms-wvx',
				'video/x-ms-wmx',
				'video/webm',
			),
		);
	}

	public function _isBannedMimetype($mimetype)
	{
		$banned = array(
			'application/x-sh',
			'application/octet-stream',
			'application/x-msdownload',
			'application/exe',
			'application/x-exe',
			'application/dos-exe',
			'vms/exe',
			'application/x-winexe',
			'application/msdos-windows',
			'application/x-msdos-program',
			'image/bmp',
			'image/x-bmp',
			'image/x-bitmap',
			'image/x-xbitmap',
			'image/x-win-bitmap',
			'image/x-windows-bmp',
			'image/ms-bmp',
			'image/x-ms-bmp',
			'application/bmp',
			'application/x-bmp',
			'application/x-win-bitmap'
		);

		return in_array($mimetype, $banned);
	}
}