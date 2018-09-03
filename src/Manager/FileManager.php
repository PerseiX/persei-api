<?php

namespace ApiBundle\Manager;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileManager
 * @package ApiBundle\Manager
 */
class FileManager
{
	/**
	 * @var string
	 */
	private $kernelDir;

	/**
	 * FileManager constructor.
	 *
	 * @param string $kernelDir
	 */
	public function __construct(string $kernelDir)
	{
		$this->kernelDir = $kernelDir;
	}

	/**
	 * @param string       $uploadDir
	 * @param UploadedFile $file
	 *
	 * @return string
	 */
	public function upload(string $uploadDir, UploadedFile $file)
	{
		$fileName = md5(uniqid()) . '.' . $file->guessExtension();

		try {
			$file->move(sprintf('%s/public/uploads/%s', $this->kernelDir, $uploadDir), $fileName);
		} catch (Exception $e) {
		}

		return $fileName;
	}
}