<?php
namespace App\DTOs;

class PaymentRequestDTO {
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public float $amount,
        public string $cardNumber,
        public string $expiryMonth,
        public string $expiryYear,
        public string $cvv
    ) {}
}