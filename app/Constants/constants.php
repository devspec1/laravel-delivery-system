<?php

$payment_methods = array(
	["key" => "cash", "value" => 'Cash', 'icon' => asset("images/icon/cash.png")],
	["key" => "paypal", "value" => 'PayPal', 'icon' => asset("images/icon/paypal.png")],
	["key" => "braintree", "value" => 'Card Payment', 'icon' => asset("images/icon/card.png")],
	["key" => "stripe", "value" => 'Card Payment', 'icon' => asset("images/icon/card.png")],
);

if(!defined('PAYMENT_METHODS')) {
	define('PAYMENT_METHODS', $payment_methods);	
}

$payout_methods = array(
	["key" => "bank_transfer", "value" => 'Bank Transfer'],
	["key" => "paypal", "value" => 'PayPal'],
	["key" => "stripe", "value" => 'Stripe'],
);

if(!defined('PAYOUT_METHODS')) {
	define('PAYOUT_METHODS', $payout_methods);	
}

if(!defined('CACHE_HOURS')) {
	define('CACHE_HOURS', 24);	
}