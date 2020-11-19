<?php

declare(strict_types=1);

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

Tester\Environment::setup();

if (!defined('TEMP_PATH')) {
	define('TEMP_PATH', __DIR__ . '/temp');
}

if (!defined('CONFIG_DIR')) {
	define('CONFIG_DIR', __DIR__ . '/files');
}
