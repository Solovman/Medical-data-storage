<?php

return [
	'APP_NAME' => 'BitCar',
	'MENU' => [],
	'DB_OPTIONS' => [],
	'NUM_OF_ITEMS_PER_PAGE' => 6,

	'MIGRATION_PATH' => "/migrations",
	'MIGRATION_TABLE' => "N_ONE_MIGRATIONS",

	'ICONS_PATH' => '/images/icons/',
	'IMAGES_PATH' => '/images/products/',

	'VIEWS_PATH' => ROOT . '/src/View/',
	'COMPONENTS_PATH' => ROOT . '/src/View/components/',
	'PAGES_PATH' => ROOT . '/src/View/pages/',
	'LAYOUTS_PATH' => ROOT . '/src/View/layouts/',

	'MIGRATION_NEEDED' => true,

	'SERVICES_PATH' => ROOT . '/services.xml',

	'HOST_NAME' => $_SERVER['HTTP_HOST'],

	'ORDER_HASH_PREFIX' => 'BITCAR_ORD',
	'ORDER_HASH_ALGO' => 'crc32',
	'SCRIPTS_PATH' => ROOT . '/js/',

	'FINAL_ERROR_PAGE' => 'pages/finalErrorPage',
	'PUBLIC_ERROR_PAGE' => 'pages/publicErrorPage',
	'ADMIN_ERROR_PAGE' => 'pages/adminErrorPage',

	'ROOT_LOG_DIR' => ROOT . '/var/log/',
];