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
            // DÖKÜMANTASYONDAKİ C# KODUNUN BİREBİR PHP KARŞILIĞIDIR
            $MbrId            = "5";
            $MerchantID       = env('FINANSBANK_MERCHANT_ID', '085300000009704');
            $MerchantPass     = env('FINANSBANK_MERCHANT_PASS', '12345678'); 
            $UserCode         = env('FINANSBANK_USER_CODE', 'QNB_API_KULLANICI_3DPAY');
            $UserPass         = env('FINANSBANK_USER_PASS', 'UcBN0');
            $SecureType       = "3DPay";
            $TxnType          = "Auth";
            $InstallmentCount = "0";
            $Currency         = "949";
            
            $OkUrl            = env('FINANSBANK_SUCCESS_URL');
            $FailUrl          = env('FINANSBANK_FAIL_URL');
            $OrderId          = 'TEST' . date('YmdHis');
            $PurchAmount      = number_format($requestDTO->amount, 2, '.', '');
            $Lang             = "TR";

            // C# Kodundaki: String rnd = DateTime.Now.Ticks.ToString();
            $rnd = (string) time();

            // C# Kodundaki: String str = MbrId + OrderId + PurchAmount + OkUrl + FailUrl + TxnType + InstallmentCount + rnd + MerchantPass;
            $hashStr = $MbrId . $OrderId . $PurchAmount . $OkUrl . $FailUrl . $TxnType . $InstallmentCount . $rnd . $MerchantPass;

            // C# Kodundaki SHA1 şifreleme algoritmasının tam PHP karşılığı
            $hash = base64_encode(pack('H*', sha1($hashStr)));

            // Kart Bilgileri
            $Pan    = str_replace(' ', '', $requestDTO->cardNumber);
            $Cvv2   = $requestDTO->cvv;
            $Expiry = str_pad($requestDTO->expiryMonth, 2, '0', STR_PAD_LEFT) . substr($requestDTO->expiryYear, -2);

            // C# Kodundaki formun büyük/küçük harflerine (%100) sadık kalınmış HTML Formu
            $htmlForm = '
            <!DOCTYPE html>
            <html lang="tr">
            <head><title>QNB - 3D Pay Yönlendirme</title></head>
            <body onload="document.forms[0].submit();">
                <div style="text-align:center; margin-top:50px; font-family:sans-serif;">
                    <h2>Lütfen bekleyin, bankanın 3D Secure sayfasına yönlendiriliyorsunuz...</h2>
                </div>
                <form method="post" action="https://vpostest.qnb.com.tr/Gateway/Default.aspx">
                    <input type="hidden" name="MbrId" value="'.$MbrId.'">
                    <input type="hidden" name="MerchantID" value="'.$MerchantID.'">
                    <input type="hidden" name="UserCode" value="'.$UserCode.'">
                    <input type="hidden" name="UserPass" value="'.$UserPass.'">
                    <input type="hidden" name="SecureType" value="'.$SecureType.'">
                    <input type="hidden" name="TxnType" value="'.$TxnType.'">
                    <input type="hidden" name="InstallmentCount" value="'.$InstallmentCount.'">
                    <input type="hidden" name="Currency" value="'.$Currency.'">
                    <input type="hidden" name="OkUrl" value="'.$OkUrl.'">
                    <input type="hidden" name="FailUrl" value="'.$FailUrl.'">
                    <input type="hidden" name="OrderId" value="'.$OrderId.'">
                    <input type="hidden" name="PurchAmount" value="'.$PurchAmount.'">
                    <input type="hidden" name="Lang" value="'.$Lang.'">
                    <input type="hidden" name="Rnd" value="'.$rnd.'">
                    <input type="hidden" name="Hash" value="'.$hash.'">
                    <input type="hidden" name="Pan" value="'.$Pan.'">
                    <input type="hidden" name="Cvv2" value="'.$Cvv2.'">
                    <input type="hidden" name="Expiry" value="'.$Expiry.'">
                </form>
            </body>
            </html>';

            return [
                'status' => true,
                'html_content' => $htmlForm
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('3D Hata:', ['msg' => $e->getMessage()]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyPayment(array $callbackData): PaymentResponseDTO
    {
        try {
            Log::info('Callback Verileri Çözümleniyor', $callbackData);

            // Finansbank'tan dönen parametreler
            $threeDStatus = $callbackData['3DStatus'] ?? $callbackData['mdStatus'] ?? null;
            $procReturnCode = $callbackData['ProcReturnCode'] ?? null;
            $errorMessage = $callbackData['ErrMsg'] ?? $callbackData['mdErrorMsg'] ?? 'Ödeme başarısız';
            $transactionId = $callbackData['TransId'] ?? $callbackData['ReferenceCode'] ?? uniqid();

            Log::info('Callback Doğrulama Parametreleri', [
                '3DStatus'      => $threeDStatus,
                'ProcReturnCode' => $procReturnCode,
                'TransactionId' => $transactionId,
            ]);

            // 3DStatus: 1=Başarılı, 2=Başarılı (3D Secure olmayan), 3=Kimlik doğrulamada başarısız, 4=Kimlik doğrulamadan geçiş
            // ProcReturnCode: 00=Başarılı
            if (in_array((string) $threeDStatus, ['1', '2', '4'], true) && $procReturnCode === '00') {
                Log::info('Ödeme Başarılı - Doğrulama Geçildi', ['TransactionId' => $transactionId]);
                return new PaymentResponseDTO(
                    true, 
                    'Ödeme başarıyla tamamlandı', 
                    $transactionId
                );
            }

            Log::error('Ödeme Başarısız - Banka Reddi', [
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
            Log::error('Callback İşleme Hatası (Sistem Hatası)', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return new PaymentResponseDTO(false, 'Ödeme doğrulama hatası: ' . $e->getMessage());
        }
    }
}