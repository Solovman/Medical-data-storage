<?php

use N_ONE\App\Model\Service\ValidationService;
use N_ONE\Core\Configurator\Configurator;

$iconsPath = Configurator::option('ICONS_PATH');
$imagesPath = Configurator::option('IMAGES_PATH');
$cssFile = isset($content) ? ValidationService::validateMetaTag($content, 'css') : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/styles/reset.css">
	<link rel="stylesheet" href="/styles/publicLayout.css">
	<?php if (isset($cssFile)): ?>
		<link rel="stylesheet" href="<?= $cssFile ?>">
	<?php endif; ?>
	<title>eshop</title>
</head>
<body>

<main>
	<?= $content ?>
</main>


</body>
</html>