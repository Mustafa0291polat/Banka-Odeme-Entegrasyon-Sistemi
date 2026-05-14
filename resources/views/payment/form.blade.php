<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Secure Güvenli Ödeme</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-container {
            background-color: #ffffff;
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .payment-header {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .payment-header h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.5px;
        }
        .payment-form {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            background-color: #fff;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        .btn-submit {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #059669;
        }
        .error-box {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 13px;
        }
        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .card-icons {
            text-align: center;
            margin-top: 20px;
            opacity: 0.6;
        }
    </style>
</head>
<body>

    <div class="payment-container">
        <div class="payment-header">
            <h2>3D Secure Güvenli Ödeme</h2>
        </div>

        <div class="payment-form">
            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('payment.process') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Adınız</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Ahmet" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Soyadınız</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Yılmaz" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="05XXXXXXXXX" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Tutar (TL)</label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" placeholder="100.00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="card_no">Kart Numarası</label>
                    <input type="text" id="card_no" name="card_no" value="{{ old('card_no') }}" placeholder="4355 0812 3456 7892" maxlength="19" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_month">Son Kul. Ay</label>
                        <select id="expiry_month" name="expiry_month" required>
                            <option value="" disabled selected>Ay</option>
                            @for ($m = 1; $m <= 12; $m++)
                                @php $val = sprintf('%02d', $m); @endphp
                                <option value="{{ $val }}" {{ old('expiry_month') == $val ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expiry_year">Son Kul. Yıl</label>
                        <select id="expiry_year" name="expiry_year" required>
                            <option value="" disabled selected>Yıl</option>
                            @php $currentYear = (int)date('y'); @endphp
                            @for ($i = $currentYear; $i <= $currentYear + 15; $i++)
                                @php $val = sprintf('%02d', $i); @endphp
                                <option value="{{ $val }}" {{ old('expiry_year') == $val ? 'selected' : '' }}>
                                    20{{ $val }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" value="{{ old('cvv') }}" placeholder="123" maxlength="4" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Güvenli Ödemeyi Tamamla</button>

                <div class="card-icons">
                    <small>Mastercard - Visa - Troy</small>
                </div>
            </form>
        </div>
    </div>

</body>
</html>