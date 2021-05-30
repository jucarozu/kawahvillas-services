<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\BaseController;

class BookingRequestsController extends BaseController {
  
  public function get(Request $request, Response $response, $args) {
    
    try {

      $pdo = $this->container->get('db');
      
      $sql = $pdo->prepare("
        SELECT as_request_id, wp_user_id, st_rental_post_id, as_request_start_date, as_request_end_date, as_request_status 
        FROM as_booking_requests 
        WHERE 1 = 1
      ");
      
      $sql->execute();
      
      $result = $sql->fetchAll();
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

      $bookingRequestRow = [
        'wp_user_id' => $body['wp_user_id'],
        'st_rental_post_id' => $body['st_rental_post_id'],
        'as_request_start_date' => $body['as_request_start_date'],
        'as_request_end_date' => $body['as_request_end_date']
      ];

      $sql = "
        INSERT INTO as_booking_requests 
        SET wp_user_id = :wp_user_id, 
            st_rental_post_id = :st_rental_post_id, 
            as_request_start_date = :as_request_start_date, 
            as_request_end_date = :as_request_end_date;
      ";

      $pdo = $this->container->get('db');
      $result = $pdo->prepare($sql)->execute($bookingRequestRow);

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