<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTOs\PaymentRequestDTO;
use App\Services\FinansbankPaymentService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private FinansbankPaymentService $paymentService;

    public function __construct(
        FinansbankPaymentService $paymentService
    ) {
        $this->paymentService = $paymentService;
    }

    public function showForm()
    {
        return view('payment.form');
    }

    public function processPayment(Request $request)
    {
     
        try {
            $request->merge([
                'card_no' => str_replace(
                    ' ',
                    '',
                    $request->card_no
                )
            ]);

            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'phone' => 'required|string|max:20',
                'amount' => 'required|numeric|min:1',
                'card_no' => 'required|digits:16',
                'expiry_month' => 'required|digits:2',
                'expiry_year' => 'required|digits:2',
                'cvv' => 'required|digits_between:3,4',
            ]);

            $dto = new PaymentRequestDTO(
                $validated['first_name'],
                $validated['last_name'],
                $validated['phone'],
                $validated['amount'],
                $validated['card_no'],
                $validated['expiry_month'],
                $validated['expiry_year'],
                $validated['cvv']
            );

            $response = $this->paymentService->initiatePayment($dto);

            if (!$response['status']) {
                return back()->withErrors([
                    'error' => $response['message']
                ]);
            }

            return view(
                'payment.redirect',
                [
                    'apiUrl' => $response['apiUrl'],
                    'postData' => $response['postData']
                ]
            );

        } catch (\Exception $e) {
            Log::error(
                'Controller Hatası',
                [
                    'message' => $e->getMessage()
                ]
            );

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function callback(Request $request)
    {
        Log::info(
            'BANKA CALLBACK GELDİ',
            $request->all()
        );

        $responseDTO = $this->paymentService->verifyPayment($request->all());

        if ($responseDTO->status) {
            return view(
                'payment.success',
                [
                    'message' => $responseDTO->message,
                    'transactionId' => $responseDTO->transactionId
                ]
            );
        }

        return view(
            'payment.failure',
            [
                'message' => $responseDTO->message
            ]
        );
    }
}