<?php

namespace N_ONE\App\Model\Service;

use N_ONE\App\Model\Tag;

class TagService
{
	/**
	 * @param Tag[] $tags
	 */
	public static function reformatTags(array $tags): array
	{
		// $result = [];
		// foreach ($tags as $tag)
		// {
		// 	if ($tag->getParentId() === null)
		// 	{
		// 		$result["$tag->getTitle()"][] =
		// 	}
		// }
		// return $result;

		$groupedTags = [];

		foreach ($tags as $tag)
		{
			// Проверяем, есть ли уже массив с тегами для данного parentID
			if (!isset($groupedTags[$tag->getParentId()]))
			{
				$groupedTags[$tag->getParentId()] = [];
			}

			// Добавляем текущий тег в соответствующий массив
			$groupedTags[$tag->getParentId()][] = $tag;
		}

		return $groupedTags;
	}

	public static function reformatRangeTag(string $range): array
	{
		$parts = explode(":", $range);

		// Проверка корректности формата
		if (count($parts) === 2)
		{
			// Извлечение переменных
			$idVar = (int)$parts[0];
			$intVars = explode(",", $parts[1]);

			// Проверка корректности формата для целых чисел
			if (count($intVars) === 2)
			{
				// Извлечение целых чисел
				$intVar1 = (int)$intVars[0];
				$intVar2 = (int)$intVars[1];

				// Возвращение массива значений
				return [$idVar, $intVar1, $intVar2];
			}

			// Некорректный формат для целых чисел
			return [null, null, null];
		}

		// Некорректный формат для диапазона
		return [null, null, null];
	}
}