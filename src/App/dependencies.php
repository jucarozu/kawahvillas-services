<?php

use Psr\Container\ContainerInterface;

$container->set('db', function(ContainerInterface $instance) {

  $db_settings = $instance->get('db_settings');
  
  $driver = $db_settings->DB_DRIVER;
  $host = $db_settings->DB_HOST;
  $user = $db_settings->DB_USER;
  $password = $db_settings->DB_PASSWORD;
  $name = $db_settings->DB_NAME;
  $charset = $db_settings->DB_CHARSET;

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
  ];

  $dsn = $driver . ':host=' . $host . ';dbname=' . $name . ';charset=' . $charset;

  return new PDO($dsn, $user, $password, $options);
  
});