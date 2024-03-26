<?php

namespace N_ONE\App\Controller;

use N_ONE\Core\TemplateEngine\TemplateEngine;

class CatalogController extends BaseController
{
	public function renderCatalog(): string
	{

		$content = TemplateEngine::render('pages/main');

		return $this->renderPublicView($content);
	}
}