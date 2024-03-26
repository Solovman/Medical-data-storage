<?php

namespace N_ONE\App\Model\Service;

use N_ONE\App\Model\Image;
use N_ONE\App\Model\Repository\ImageRepository;
use N_ONE\Core\Configurator\Configurator;
use N_ONE\Core\Exceptions\DatabaseException;
use N_ONE\Core\Exceptions\ValidateException;

class ImageService
{
	public function __construct(
		private readonly ImageRepository $imageRepository,
	)
	{
	}

	/**
	 * @throws DatabaseException
	 */
	public function deleteImages(array $imagesIds): bool
	{
		$imagesIds = array_map('intval', $imagesIds);
		$images = $this->imageRepository->getList($imagesIds, true);
		$path = ROOT . '/public' . Configurator::option('IMAGES_PATH');

		$this->imageRepository->permanentDeleteByIds($imagesIds);

		foreach ($imagesIds as $id)
		{
			unlink($path . $images[$id][0]->getPath());
		}

		return true;
	}

	/**
	 * @throws ValidateException
	 * @throws DatabaseException
	 */
	public function addBaseImages($files, $itemId): bool
	{
		$fileCount = count($files['image']['name']);

		for ($i = 0; $i < $fileCount; $i++)
		{
			ValidationService::validateImage($files, $i);

			$targetDir = ROOT
				. '/public'
				. Configurator::option('IMAGES_PATH')
				. "$itemId/"; // директория для сохранения загруженных файлов
			$targetFile = $targetDir . basename($files["image"]["name"][$i]);
			$file_extension = pathinfo($files['image']['name'][$i], PATHINFO_EXTENSION);

			self::createDirIfNotExist($targetDir);

			$fullSizeImageId = $this->imageRepository->add(
				new Image(null, $itemId, false, 1, 1200, 900, $file_extension)
			);
			$previewImageId = $this->imageRepository->add(
				new Image(null, $itemId, false, 2, 640, 480, $file_extension)
			);

			$finalFullSizePath = $targetDir . $fullSizeImageId . "_1200_900_fullsize_base" . ".$file_extension";
			$finalPreviewPath = $targetDir . $previewImageId . '_640_480_preview_base' . ".$file_extension";
			// Попытка загрузки файла на сервер
			if (move_uploaded_file($files["image"]["tmp_name"][$i], $targetFile))
			{
				self::resizeImage($targetFile, $finalFullSizePath, 1200, 900);
				self::resizeImage($targetFile, $finalPreviewPath, 640, 480);
				unlink($targetFile);
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @throws ValidateException
	 */
	public function addTagLogo($files, int $itemId): bool
	{
		ValidationService::validateImage($files);

		$targetDir =
			ROOT .
			'/public' .
			Configurator::option('ICONS_PATH'); // директория для сохранения загруженных файлов
		$fileExtension = pathinfo($files['image']['name'][0], PATHINFO_EXTENSION);
		$finalPath = $targetDir . $itemId . ".$fileExtension";

		$images = glob($targetDir . $itemId . '.*');

		foreach ($images as $image)
		{
			unlink($image);
		}


		if (move_uploaded_file($files["image"]["tmp_name"][0], $finalPath))// Сохраняем файл по указанному пути
		{
			return true;// Файл успешно сохранен
		}

		return false;// Произошла ошибка при сохранении файла
	}

	public static function resizeImage($source, $destination, $width, $height): bool
	{
		// Получаем размеры и тип изображения
		[$source_width, $source_height, $source_type] = getimagesize($source);

		// Создаем изображение на основе исходного файла
		switch ($source_type)
		{
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($source);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($source);
				break;
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($source);
				break;
			default:
				return false; // Неподдерживаемый формат файла
		}

		// Создаем пустое изображение с новыми размерами
		$new_image = imagecreatetruecolor($width, $height);

		// Масштабируем и копируем изображение с измененными размерами
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $source_width, $source_height);

		// Сохраняем измененное изображение
		switch ($source_type)
		{
			case IMAGETYPE_JPEG:
				imagejpeg($new_image, $destination);
				break;
			case IMAGETYPE_PNG:
				imagepng($new_image, $destination);
				break;
			case IMAGETYPE_GIF:
				imagegif($new_image, $destination);
				break;
		}

		// Освобождаем память
		imagedestroy($image);
		imagedestroy($new_image);

		return true;
	}

	public static function createDirIfNotExist($folderPath): void
	{    // Проверяем существует ли папка
		if (!file_exists($folderPath))
		{
			// Создаем папку если она не существует
			mkdir($folderPath, 0777, true);
		}
	}

	public static function getTagIcon(string $path): string
	{
		// Проверяем, существует ли файл с заданным путем без расширения
		if (file_exists(ROOT . '/public' . $path))
		{
			return $path; // Если файл существует, возвращаем его путь без изменений
		}

		// Получаем список файлов в директории
		$files = glob(ROOT . '/public' . $path . '.*');

		// Если найден хотя бы один файл с расширением, возвращаем путь к первому найденному файлу
		if (count($files) > 0)
		{
			$extension = pathinfo($files[0], PATHINFO_EXTENSION);
			return $path . '.' . $extension;
		}

		return false; // Если файлы не найдены, возвращаем false
	}
}