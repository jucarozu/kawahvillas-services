<?php

use Slim\Routing\RouteCollectorProxy;

$app->group('/customers', function(RouteCollectorProxy $group) {
  
  $group->get('/user', 'App\Controllers\CustomersController:getByUserId');
  $group->post('/user', 'App\Controllers\CustomersController:post');

});

$app->group('/stripe', function(RouteCollectorProxy $group) {

  $group->post('/invoiceEvents', 'App\Controllers\InvoiceEventsController:post');

});