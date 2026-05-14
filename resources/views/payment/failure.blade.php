<!DOCTYPE html>
<html>
<head>
    <title>Ödeme Başarısız</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="alert alert-danger py-5 shadow">
            <h1 class="display-4">Ödeme Başarısız!</h1>
            <p class="lead">Maalesef ödeme işleminiz tamamlanamadı.</p>
            @if(isset($message))
                <p class="text-muted"><strong>Hata Mesajı:</strong> {{ $message }}</p>
            @endif
            <hr>
            <a href="{{ route('payment.form') }}" class="btn btn-outline-danger">Tekrar Dene</a>
        </div>
    </div>
</body>
</html>