<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    public function test_payment_form_page_is_accessible()
    {
        $response = $this->get(route('payment.form'));

        $response->assertStatus(200);
        $response->assertSee('Finansbank 3D Secure Ödeme');
    }

    public function test_payment_process_builds_bank_redirect_form()
    {
        $this->withoutMiddleware();

        config([
            'payment.finansbank' => [
                'merchant_id' => '085300000009704',
                'merchant_pass' => '12345678',
                'store_key' => '12345678',
                'user_code' => 'QNB_ISYERI_KULLANICI_3DPAY',
                'user_pass' => 'a1234',
                'mbr_id' => '5',
                'secure_type' => 'Payfor3DPay',
                'api_url' => 'https://vpostest.qnb.com.tr/Gateway/Default.aspx',
            ],
        ]);

        $response = $this->post(route('payment.process'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '5555555555',
            'amount' => '10.00',
            'card_no' => '4282405990002166',
            'expiry_month' => '12',
            'expiry_year' => '28',
            'cvv' => '656',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('payment.redirect');
        $response->assertSee('name="MbrId"', false);
        $response->assertSee('name="Hash"', false);
        $response->assertSee('name="Pan"', false);
    }
}
