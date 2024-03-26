<?php
/**
 * @var int $errorCode
 * @var string $errorMessage
 */

?>
<div class="error-message-container">
	<h1 class="error-message"><?= $errorCode ?> <?= $errorMessage ?></h1>
	<p>Вернуться на <a href="/">главную страницу</a></p>
</div>
<meta name="css" content="<?= '/styles/' . basename(__FILE__, '.php') . '.css' ?>">