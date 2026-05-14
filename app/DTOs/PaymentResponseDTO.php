<?php
namespace App\DTOs;

class PaymentResponseDTO {
    public function __construct(
        public bool $status,
        public string $message,
        public ?string $transactionId = null,
        public ?string $redirectUrl = null
    ) {}
}