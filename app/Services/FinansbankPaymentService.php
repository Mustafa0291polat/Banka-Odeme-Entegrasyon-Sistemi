<?php

namespace App\Services;

use App\DTOs\PaymentRequestDTO;
use App\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\Log;

class FinansbankPaymentService
{
    public function initiatePayment(PaymentRequestDTO $requestDTO): array
    {
        try {
            $merchantId = config('payment.finansbank.merchant_id'); 
            $merchantPass = config('payment.finansbank.merchant_pass');
            $storeKey = config('payment.finansbank.store_key');
            $userCode = config('payment.finansbank.user_code');
            $userPass = config('payment.finansbank.user_pass');
            $mbrId = config('payment.finansbank.mbr_id');
            
            $oid = 'TEST' . date('YmdHis'); 
            $amount = number_format($requestDTO->amount, 2, '.', '');
            
            $okUrl = config('payment.finansbank.success_url') ?: route('payment.callback');
            $failUrl = config('payment.finansbank.fail_url') ?: route('payment.callback');
            
            $rnd = (string) time(); 
            $txnType = "Auth3D";
            $installmentCount = ""; // Tek çekim için boş kalmalı
            $secureType = config('payment.finansbank.secure_type', 'Payfor3DPay');

            // Hash hesaplaması: MbrId|OrderId|Amount|OkUrl|FailUrl|TxnType|InstallmentCount|Rnd|StoreKey
            $hashStr = $mbrId . $oid . $amount . $okUrl . $failUrl . $txnType . $installmentCount . $rnd . $storeKey;
            $hash = base64_encode(pack('H*', sha1($hashStr)));

            $expiry = str_pad($requestDTO->expiryMonth, 2, '0', STR_PAD_LEFT) . substr($requestDTO->expiryYear, -2);

            $postData = [
                'MbrId'            => $mbrId,
                'MerchantID'       => $merchantId,
                'UserCode'         => $userCode,
                'UserPass'         => $userPass,
                'OrderId'          => $oid,
                'PurchAmount'      => $amount,
                'OkUrl'            => $okUrl,
                'FailUrl'          => $failUrl,
                'TxnType'          => $txnType,
                'SecureType'       => $secureType,
                'InstallmentCount' => $installmentCount,
                'rnd'              => $rnd,
                'Hash'             => $hash,
                'Currency'         => '949',
                'Lang'             => 'TR',
                'Pan'              => $requestDTO->cardNumber,
                'Expiry'           => $expiry,
                'Cvv2'             => $requestDTO->cvv,
                'CardHolderName'   => $requestDTO->firstName . ' ' . $requestDTO->lastName,
            ];

            // Log minimal non-sensitive payment info for debugging (mask PAN)
            Log::info('3D Secure Ödeme Başlatıldı', [
                'OrderId'   => $oid,
                'Amount'    => $amount,
                'ApiUrl'    => config('payment.finansbank.api_url'),
                'PanLast4'  => substr($requestDTO->cardNumber, -4),
                'Expiry'    => $expiry,
            ]);

            return [
                'status'   => true,
                'apiUrl'   => config('payment.finansbank.api_url'),
                'postData' => $postData
            ];
        } catch (\Exception $e) {
            Log::error('3D Secure Başlatma Hatası', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyPayment(array $callbackData): PaymentResponseDTO
    {
        try {
            Log::info('Callback Verileri', $callbackData);

            // Finansbank'tan dönen parametreler
            $threeDStatus = $callbackData['3DStatus'] ?? $callbackData['mdStatus'] ?? null;
            $procReturnCode = $callbackData['ProcReturnCode'] ?? null;
            $errorMessage = $callbackData['ErrMsg'] ?? $callbackData['mdErrorMsg'] ?? 'Ödeme başarısız';
            $transactionId = $callbackData['TransId'] ?? $callbackData['ReferenceCode'] ?? uniqid();

            Log::info('Callback Doğrulama', [
                '3DStatus'      => $threeDStatus,
                'ProcReturnCode' => $procReturnCode,
                'TransactionId' => $transactionId,
            ]);

            // 3DStatus: 1=Başarılı, 2=Başarılı (3D Secure olmayan), 3=Kimlik doğrulamada başarısız, 4=Kimlik doğrulamadan geçiş
            // ProcReturnCode: 00=Başarılı
            if (in_array((string) $threeDStatus, ['1', '2', '4'], true) && $procReturnCode === '00') {
                Log::info('Ödeme Başarılı', ['TransactionId' => $transactionId]);
                return new PaymentResponseDTO(
                    true, 
                    'Ödeme başarıyla tamamlandı', 
                    $transactionId
                );
            }

            Log::error('Ödeme Başarısız', [
                'Status'        => $threeDStatus,
                'ReturnCode'    => $procReturnCode,
                'Message'       => $errorMessage
            ]);

            return new PaymentResponseDTO(
                false, 
                $errorMessage ?? 'Ödeme başarısız',
                $transactionId
            );
        } catch (\Exception $e) {
            Log::error('Callback İşleme Hatası', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return new PaymentResponseDTO(false, 'Ödeme doğrulama hatası: ' . $e->getMessage());
        }
    }
}