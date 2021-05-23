<?php

use Slim\Routing\RouteCollectorProxy;

$app->group('/customers', function(RouteCollectorProxy $group) {
  
  $group->get('', 'App\Controllers\CustomersController:get');
  $group->post('', 'App\Controllers\CustomersController:post');

});

$app->group('/stripe', function(RouteCollectorProxy $group) {

  $group->post('/invoiceEvents', 'App\Controllers\InvoiceEventsController:post');

});