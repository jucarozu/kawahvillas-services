<?php

use Slim\Factory\AppFactory;

// Autoload dependencies
require __DIR__ . '/../../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Instantiate container
$instance = new \DI\Container();
AppFactory::setContainer($instance);

// Instantiate app
$app = AppFactory::create();
$container = $app->getContainer();

// Import app files
require __DIR__ . '/settings.php';
require __DIR__ . '/dependencies.php';
require __DIR__ . '/routes.php';

// Set base path
$app->setBasePath('/api/v1');

// Add middleware
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

// Run application
$app->run();