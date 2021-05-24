<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\BaseController;

class CustomersController extends BaseController {
  
  public function get(Request $request, Response $response, $args) {
    
    try {

      $user_id = $request->getQueryParams()['user_id'];

      $pdo = $this->container->get('db');
      
      $sql = $pdo->prepare("
        SELECT as_customer_id, wp_user_id, wp_user_country, wp_user_phone, wp_user_status, stripe_customer_id, stripe_product_id, stripe_subscription_id, stripe_subscription_status 
        FROM as_customers 
        WHERE wp_user_id = $user_id
      ");
      
      $sql->execute();
      
      $result = $sql->fetch();
      $payload = json_encode($result);
      
      $response->getBody()->write($payload);
      $response->withHeader('Content-Type', 'application/json');
      $response->withStatus(200);
      
      return $response;

    } catch (\Exception $e) {

      $response->getBody()->write('Error: ' . $e->getMessage());
      $response->withStatus(500);
      
      return $response;

    }

  }

  public function post(Request $request, Response $response, $args) {
    
    try {

      $body = (array)$request->getParsedBody();

      $customerRow = [
        'wp_user_id' => $body['wp_user_id'],
        'wp_user_country' => $body['wp_user_country'],
        'wp_user_phone' => $body['wp_user_phone'],
        'stripe_customer_id' => $body['stripe_customer_id']
      ];

      $sql = "
        INSERT INTO as_customers 
        SET wp_user_id = :wp_user_id, 
            wp_user_country = :wp_user_country, 
            wp_user_phone = :wp_user_phone, 
            stripe_customer_id = :stripe_customer_id;
      ";

      $pdo = $this->container->get('db');
      $result = $pdo->prepare($sql)->execute($customerRow);

      $payload = json_encode($result);
      
      $response->getBody()->write($payload);
      $response->withHeader('Content-Type', 'application/json');
      $response->withStatus(200);

      return $response;

    } catch (\Exception $e) {

      $response->getBody()->write('Error: ' . $e->getMessage());
      $response->withStatus(500);
      
      return $response;

    }

  }
  
}