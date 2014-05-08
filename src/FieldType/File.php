<?php

namespace Message\Mothership\FileManager\FieldType;

use Message\Cog\Field\Field;

use Message\Mothership\FileManager\File\Type as FileType;
use Message\Mothership\FileManager\File\Loader;

use Message\ImageResize\ResizableInterface;

use Message\Cog\Filesystem;
use Message\Cog\Service\ContainerInterface;
use Message\Cog\Service\ContainerAwareInterface;
use Message\Cog\Localisation\Translator;
use Symfony\Component\Form\FormBuilder;

/**
 * A field for a file in the file manager database.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class File extends Field implements ResizableInterface
{
	protected $_allowedTypes;

	protected $_file;

	/**
	 * @var \Message\Mothership\FileManager\File\Loader
	 */
	protected $_loader;

	/**
	 * @var \Message\Cog\Localisation\Translator
	 */
	protected $_translator;

	public function __construct(Loader $loader, Translator $translator)
	{
		$this->_loader     = $loader;
		$this->_translator = $translator;

		parent::__construct();
	}

	/**
	 * Cast this field to a string.
	 *
	 * This outputs the public path to the file, or a blank string if the file
	 * could not be found.
	 *
	 * @return string Public path to the file
	 */
	public function __toString()
	{
		return $this->getUrl();
	}

	public function getFieldType()
	{
		return 'file';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl()
	{
		return $this->getFile() ? $this->getFile()->getUrl() : '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAltText()
	{
		return $this->getFile() ? $this->getFile()->getAltText() : '';
	}

	public function getFormField(FormBuilder $form)
	{
		$form->add($this->getName(), 'ms_file', $this->getFieldOptions());
	}

	public function getFormType()
	{
		return 'ms_file';
	}

	public function setAllowedTypes($types)
	{
		if (!is_array($types)) {
			$types = array($types);
		}

		$this->_allowedTypes = $types;

		return $this;
	}

	public function getFile()
	{
		if (!$this->_file && $this->_value) {
			$this->_file = $this->_loader->getByID((int) $this->_value);
		}

		return $this->_file;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function getFieldOptions()
	{
		$defaults = [
			'choices' => $this->_getChoices(),
			'allowed_types' => $this->_allowedTypes ? : false,
			'empty_value' => $this->_translator->trans('ms.file_manager.select.default'),
		];

		return array_merge($defaults, parent::getFieldOptions());
	}

	protected function _getChoices()
	{
		static $files;

		if (null === $files) {
			$files = $this->_loader->getAll();

			if (!$files) {
				$files = [];
				return $files;
			}

			$choices = [];

			foreach ($files as $file) {

				if ($this->_allowedTypes) {
					if (!in_array($file->typeID, $this->_allowedTypes)) {
						continue;
					}
				}

				$choices[$file->id] = $file->name;
			}

			$files = $choices;

			asort($files);
		}

		return $files;
	}

}