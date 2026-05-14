<!DOCTYPE html>
<html>
<head>
    <title>NLKSOFT Ödeme Sayfası</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">Finansbank 3D Secure Ödeme</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('payment.process') }}" method="POST">
                        @csrf <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adınız</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Soyadınız</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tutar (TL)</label>
                                <input type="number" name="amount" class="form-control" value="10.00" step="0.01" required>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label class="form-label">Kart Numarası</label>
                            <input type="text" name="card_no" class="form-control" placeholder="16 Haneli Kart Numarası" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ay (MM)</label>
                                <input type="text" name="expiry_month" class="form-control" placeholder="12" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Yıl (YY)</label>
                                <input type="text" name="expiry_year" class="form-control" placeholder="28" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="text" name="cvv" class="form-control" placeholder="123" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-2 py-2">Güvenli Ödeme Yap</button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>