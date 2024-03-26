<?php

namespace N_ONE\App\Model\Service;

use Exception;
use N_ONE\Core\Exceptions\ValidateException;

class ValidationService
{
	/**
	 * @throws ValidateException
	 */
	public static function validatePhoneNumber(string $phone): string
	{
		$phone = preg_replace('/\D/', '', $phone);

		if (strlen($phone) !== 11)
		{
			throw new ValidateException("Phone entered incorrectly");
		}
		if ($phone[0] === '7')
		{
			$phone[0] = '8';
		}

		return $phone;
	}

	/**
	 * @throws ValidateException
	 */
	public static function validateEmailAddress(string $email): string
	{
		$email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
		if (!$email)
		{
			throw new ValidateException("Email entered incorrectly");
		}

		return $email;
	}

	/**
	 * @throws ValidateException
	 */
	public static function validateEntryField(array|string|null $field): array|string
	{
		if (is_array($field))
		{
			$result = [];
			foreach ($field as $key => $value)
			{
				$validatedField = trim($value);
				if ($validatedField !== "")
				{
					$result[$key] = $validatedField;
				}
			}

			return $result;
		}
		$validatedField = trim($field);
		if ($validatedField === "")
		{
			throw new ValidateException("No field should be empty");
		}

		return $validatedField;
	}

	public static function validateFulltextField(?string $fulltextField): ?string
	{
		if ($fulltextField === null)
		{
			return null;
		}

		$fulltextField = trim($fulltextField);
		if (!$fulltextField)
		{
			return null;
		}
		$fulltextInArray = preg_split('/\s+/', $fulltextField, -1, PREG_SPLIT_NO_EMPTY);
		$preparedFulltextInArray = array_map(static function($word) {
			$preparedWord = preg_replace('/[^A-z0-9А-я]/u', '', $word);
			if ($preparedWord)
			{
				return "+" . $preparedWord . "*";
			}

			return null;
		}, $fulltextInArray);

		return implode(' ', $preparedFulltextInArray);
	}

	public static function safe(?string $value): string
	{
		return htmlspecialchars($value, ENT_QUOTES);
	}

	/**
	 * @throws ValidateException
	 * @throws Exception
	 */
	public static function validateImage($image, int $i = 0): bool
	{
		$allowed_formats = ["jpg", "png", "jpeg", "svg"];
		$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'];// Разрешенные форматы файлов
		$imageFileType = strtolower(pathinfo(basename($image["image"]["name"][$i]), PATHINFO_EXTENSION));
		$fileMimeType = mime_content_type($image["image"]["tmp_name"][$i]);
		$fileInfo = @getimagesize($image["image"]["tmp_name"][$i]);

		// Проверка наличия файла
		if (!isset($image["image"]))
		{
			throw new ValidateException("incorrect image");
		}

		if (!in_array($fileMimeType, $allowedMimeTypes, true))
		{
			throw new ValidateException("incorrect image");
		}

		// Проверка размера файла
		if ($image["image"]["size"][$i] > 500000)
		{
			throw new ValidateException("incorrect image");
		}

		if (!in_array($imageFileType, $allowed_formats))
		{
			throw new ValidateException("incorrect image");
		}

		if ($fileInfo === false && $imageFileType !== "svg")
		{
			// Файл не является изображением
			throw new ValidateException("incorrect image");
		}

		return true;
	}

	public static function validateMetaTag($html, $tagName): ?string
	{
		$pattern = '/<meta\s+name="' . preg_quote($tagName, '/') . '"\s+content="([^"]*)"\s*\/?>/i';
		if (preg_match($pattern, $html, $matches))
		{
			return $matches[1];
		}

		return null;
	}
}