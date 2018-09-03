<?php

namespace ApiBundle\Form\Handler;

use ApiBundle\Form\Type\PerseiFileType;
use ApiBundle\Manager\FileManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class PerseiFIleHandler
 * @package ApiBundle\Form\Handler
 */
class PerseiFileHandler
{
	const BLOCK_PREFIX = 'persei_file';

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PerseiFileHandler constructor.
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct(FileManager $fileManager)
	{
		$this->fileManager = $fileManager;
	}

	/**
	 * @param Form $form
	 */
	public function handle(Form $form)
	{
		/**
		 * @var Form $item
		 */
		foreach ($form->all() as $key => $item) {
			if (self::BLOCK_PREFIX === $item->getConfig()->getType()->getBlockPrefix()) {
				if (true === $item->getData() instanceof UploadedFile) {
					$file       = $item->getData();
					$property   = $item->getConfig()->getOption('property');
					$uploadPath = $item->getConfig()->getOption('uploadPath');
					$fileName   = $this->fileManager->upload($uploadPath, $file);
					$accessor   = PropertyAccess::createPropertyAccessor();
					$object = $form->getData();
					$accessor->setValue($object, $property, $fileName);
				}
			}
		}
	}
}