<?php

use N_ONE\App\Application;
use N_ONE\App\Model\Service\MiddleWare;
use N_ONE\Core\Routing\Route;
use N_ONE\Core\Routing\Router;

$router = Router::getInstance();

$router->get('/', static function() {
	$di = Application::getDI();

	return ($di->getComponent('catalogController'))->renderCatalog();
});

//роуты с защитой
$router->get('/admin', MiddleWare::adminMiddleware(static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderDashboard();
}));

$router->get('/admin/:entity', MiddleWare::adminMiddleware(static function(Route $route) {
	$di = Application::getDI();
	$entityToEdit = $route->getVariables()[0];
	$currentPageNumber = (int)($_GET['page'] ?? null);
	$isActive = (int)($_GET['isActive'] ?? 1);

	return ($di->getComponent('adminController'))->renderEntityPage($entityToEdit, $currentPageNumber, $isActive);
}));

$router->get('/admin/:string/edit/:id', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToEdit = $route->getVariables()[0];
	$itemId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderEditPage($entityToEdit, $itemId);
}));

$router->post('/admin/:string/edit/:id', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToEdit = $route->getVariables()[0];
	$itemId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->updateEntity($entityToEdit, $itemId);
}));

$router->get('/admin/:string/add', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToEdit = $route->getVariables()[0];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderAddPage($entityToEdit);
}));

$router->post('/admin/:string/add', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToAdd = $route->getVariables()[0];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->addEntity($entityToAdd);
}));

$router->get('/admin/edit/success', MiddleWare::adminMiddleware(static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderSuccessEditPage();
}));

$router->get('/admin/add/success', MiddleWare::adminMiddleware(static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderSuccessAddPage();
}));

$router->get('/admin/:entity/restore/:id', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToDelete = $route->getVariables()[0];
	$entityId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderConfirmPage($entityToDelete, $entityId, 'восстановить');
}));

$router->post('/admin/:entity/restore/:id', static function(Route $route) {
	$entityToDelete = $route->getVariables()[0];
	$entityId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->processChangeActive($entityToDelete, $entityId, 1);
});

$router->get('/admin/restore/success', MiddleWare::adminMiddleware(static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderSuccessPage(1);
}));

$router->get('/admin/:entity/delete/:id', MiddleWare::adminMiddleware(static function(Route $route) {
	$entityToDelete = $route->getVariables()[0];
	$entityId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderConfirmPage($entityToDelete, $entityId, 'удалить');
}));

$router->post('/admin/:entity/delete/:id', static function(Route $route) {
	$entityToDelete = $route->getVariables()[0];
	$entityId = (int)$route->getVariables()[1];
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->processChangeActive($entityToDelete, $entityId, 0);
});

$router->get('/admin/delete/success', MiddleWare::adminMiddleware(static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderSuccessPage(0);
}));

//роуты доступные всем
$router->get('/login', static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->renderLoginPage('login', []);
});

$router->post('/login', static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->login($_POST['email'], $_POST['password'], $_POST['rememberMe']);
});

$router->get('/logout', static function() {
	$di = Application::getDI();

	return ($di->getComponent('adminController'))->logout();
});

