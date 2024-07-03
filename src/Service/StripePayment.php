<?php

namespace App\Service;

class StripePayment {

    public $redirectUrl;

    public function __construct(){
        Stripe::setApiKey($_SERVER('STRIPE_SECRET'));
    }

}