<?php

namespace Message\Mothership\FileManager\Task;

use Message\Cog\Console\Task\Task;
use Message\Cog\Filesystem\File;
use Message\Mothership\FileManager\File\Exception;
use Symfony\Component\Finder\Finder;

class SyncFiles extends Task
{
	const FILE_LOCATION = 'cog://public/files/';

	/**
	 * Array of instances of \Message\Cog\Filesystem\File
	 *
	 * @var array
	 */
	protected $_files = [];

	/**
	 * Array of file names not saved because they already exist
	 *
	 * @var array
	 */
	protected $_existingFiles = [];

	/**
	 * Array of file names not saved because they are a banned file type
	 *
	 * @var array
	 */
	protected $_bannedFiles = [];

	/**
	 * Array of errors thrown
	 *
	 * @var array
	 */
	protected $_errors = [];

	/**
	 * @var Finder
	 */
	protected $_finder;

	public function process()
	{
		$this->_setFinder()
			->_loadFiles()
			->_addToSystem()
			->_reportExistingFiles()
			->_reportBannedFiles()
			->_reportErrors()
		;
	}

	protected function _setFinder()
	{
		$this->writeln('Setting finder');
		$this->_finder = $this->get('filesystem.finder');
		$this->writeln('Finder set');

		return $this;
	}

	protected function _loadFiles()
	{
		if (!$this->_finder) {
			$this->_setFinder();
		}
		$this->writeln('Loading files from ' . self::FILE_LOCATION);

		foreach ($this->_finder->files()->in(self::FILE_LOCATION) as $file) {
			$this->_files[] = new File($file);
			$this->writeln('Found <info>' . $file->getRealPath() . '</info>');
		}

		$this->writeln(count($this->_files) . ' files found');

		return $this;
	}

	protected function _addToSystem()
	{
		foreach ($this->_files as $file) {
			try {
				$this->writeln('Saving <info>' . $file->getFilename() . '</info>');
				$this->get('file_manager.file.create')->save($file);
			}
			catch (Exception\FileExists $e) {
				$this->_existingFiles[$e->getFileId()] = $file->getFilename();
			}
			catch (Exception\BannedType $e){
				$this->_bannedFiles[] = $file->getFilename();
			}
			catch (\Exception $e) {
				$this->_errors[] = $e->getMessage();
			}
		}

		return $this;
	}

	protected function _reportExistingFiles()
	{
		foreach ($this->_existingFiles as $id => $name) {
			$this->writeln("<info>" . $name . "</info> already exists with id of " . $id);
		}

		return $this;
	}

	protected function _reportBannedFiles()
	{
		foreach ($this->_bannedFiles as $name) {
			$this->writeln("<error>" . $name .  " is a banned file type</error>");
		}

		return $this;
	}

	protected function _reportErrors()
	{
		$this->writeln("<error>The following exceptions were thrown!</error>");
		foreach ($this->_errors as $error) {
			$this->writeln("<error>- " . $error . "</error>");
		}
	}
}