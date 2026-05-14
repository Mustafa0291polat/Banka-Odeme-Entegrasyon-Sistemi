<!DOCTYPE html>
<html>
<head>
    <title>Ödeme Başarılı</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="alert alert-success py-5 shadow">
            <h1 class="display-4">Tebrikler!</h1>
            <p class="lead">Ödeme işleminiz başarıyla gerçekleştirildi.</p>
            @if(isset($transactionId))
                <p><strong>İşlem (Transaction) ID:</strong> {{ $transactionId }}</p>
            @endif
            <hr>
            <a href="{{ route('payment.form') }}" class="btn btn-primary">Yeni Ödeme Yap</a>
        </div>
    </div>
</body>
</html>