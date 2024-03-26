<?php

// use N_ONE\Core\Configurator\Configurator;
// use N_ONE\Core\DependencyInjection\DependencyInjection;

require_once __DIR__ . '/../boot.php';

// $di = new DependencyInjection(Configurator::option('SERVICES_PATH'));
//
// $tagRepo = $di->getComponent('tagRepository');
//
// var_dump(\N_ONE\App\Model\Service\TagService::reformatTags($tagRepo->getList()));

N_ONE\App\Application::run();