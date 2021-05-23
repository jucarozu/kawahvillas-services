<?php

namespace App\Controllers;

use \Stripe\Stripe;
use App\Controllers\BaseController;

class StripeController extends BaseController {

  public function __construct() {

    // Set Stripe secret key
    Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

  }
  
}