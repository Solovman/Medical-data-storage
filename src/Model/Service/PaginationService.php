<?php

namespace N_ONE\App\Model\Service;

use N_ONE\Core\Configurator\Configurator;

class PaginationService
{
	public static function getNextPageUri(int $numCars, ?int $pageNumber, string $currentUrl): ?string
	{
		if ($numCars === Configurator::option('NUM_OF_ITEMS_PER_PAGE') + 1)
		{
			$newPageNumber = ($pageNumber + 1);

			if (mb_strpos($currentUrl, "page"))
			{
				return preg_replace('/([?|&])page=\d+/', '$1page=' . $newPageNumber, $currentUrl, 1);
			}

			if (mb_strpos($currentUrl, "?"))
			{
				return $currentUrl . "&page=$newPageNumber";
			}

			return $currentUrl . "?page=$newPageNumber";
		}

		return null;
	}

	public static function getPreviousPageUri(?int $pageNumber, string $currentUrl): ?string
	{
		if ($pageNumber === null || $pageNumber <= 0)
		{
			return null;
		}

		$newPageNumber = ($pageNumber - 1);

		if (mb_strpos($currentUrl, "page"))
		{
			return preg_replace('/([?|&])page=\d+/', '$1page=' . $newPageNumber, $currentUrl, 1);
		}

		if (mb_strpos($currentUrl, "?"))
		{
			return $currentUrl . "&page=$newPageNumber";
		}

		return $currentUrl . "?page=$newPageNumber";
	}
}