<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\BaseController;

class CustomersController extends BaseController {
  
  public function getByUserId(Request $request, Response $response, $args) {
    
    try {

      $queryParams = $request->getQueryParams();

      if (array_key_exists('wp_user_id', $queryParams)) {

        $wp_user_id = $queryParams['wp_user_id'];

      } else {

        $response->getBody()->write("You must specify the user ID");
        $response->withStatus(401);
        
        return $response;

      }

      $pdo = $this->container->get('db');
      
      $sql = $pdo->prepare("
        SELECT as_customer_id, wp_user_id, wp_user_country, wp_user_phone, wp_user_status, stripe_customer_id, stripe_product_id, stripe_subscription_id, stripe_subscription_status 
        FROM as_customers 
        WHERE 1 = 1 
        AND wp_user_id = $wp_user_id 
      ");
      
      $sql->execute();
      
      $result = $sql->fetchAll()[0];
      $payload = json_encode($result, JSON_NUMERIC_CHECK);
      
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