<?php

require_once 'vendor/autoload.php';

use App\DTOs\PaymentRequestDTO;
use App\Services\FinansbankPaymentService;

$dto = new PaymentRequestDTO('Test', 'User', '5555555555', 10.00, '4543590000000006', '12', '28', '123');
$service = new FinansbankPaymentService();
$result = $service->initiatePayment($dto);

var_dump($result);