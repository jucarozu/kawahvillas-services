<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\BaseController;
use \Stripe\Stripe;

class InvoiceEventsController extends BaseController {

  public function post(Request $request, Response $response, $args) {
    
    try {

      // Set Stripe secret key
      Stripe::setApiKey('sk_test_51IoL75HR21ubejJfMmkGCQL7CZ7bokI0o929oMcqGLsCfZaMmGMf31qPeNtizW93ECyq1pEPy0ntKgld91qaXaIh00tIRIn4V2');

      $event = $request->getParsedBody();
      $type = $event['type'];
      $object = $event['data']['object'];

      switch ($type) {

        case 'invoice.payment_succeeded':

          if ($object['billing_reason'] == 'subscription_create') {
            
            $subscription_id = $object['subscription'];
            $payment_intent_id = $object['payment_intent'];
          
            // Retrieve the payment intent used to pay the subscription
            $payment_intent = \Stripe\PaymentIntent::retrieve(
              $payment_intent_id,
              []
            );

            \Stripe\Subscription::update(
              $subscription_id,
              ['default_payment_method' => $payment_intent->payment_method],
            );

          }

          break;
        
        case 'invoice.paid':

          $customer_id = $object['customer'];
          $product_id = $object['lines']['data'][0]['price']['product'];
          $subscription_id = $object['subscription'];

          $subscription = \Stripe\Subscription::retrieve(
            $subscription_id,
            []
          );

          $subscription_status = $subscription['status'];

          $customerRow = [
            'stripe_customer_id' => $customer_id,
            'stripe_product_id' => $product_id,
            'stripe_subscription_id' => $subscription_id,
            'stripe_subscription_status' => $subscription_status
          ];

          $sql = "
            UPDATE as_customers 
            SET stripe_product_id = :stripe_product_id,
                stripe_subscription_id = :stripe_subscription_id,
                stripe_subscription_status = :stripe_subscription_status
            WHERE stripe_customer_id = :stripe_customer_id;
          ";

          $pdo = $this->container->get('db');
          $result = $pdo->prepare($sql)->execute($customerRow);

          break;
      }

      $response->getBody()->write('Successful');
      $response->withStatus(200);

      return $response;

    } catch (\Exception $e) {

      $response->getBody()->write('Error: ' . $e->getMessage());
      $response->withStatus(500);
      
      return $response;

    }

  }
}