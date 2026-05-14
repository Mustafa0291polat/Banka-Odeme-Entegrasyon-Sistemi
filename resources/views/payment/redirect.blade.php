<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>3D Secure Yönlendirme</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 20%; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <h3>Güvenli Ödeme Sayfasına Yönlendiriliyorsunuz...</h3>
    <p>Lütfen bekleyin, sayfayı kapatmayın.</p>
    <div class="loader"></div>

    <!-- Bankaya gidecek gizli form -->
    <form id="threeDForm" action="{{ $apiUrl }}" method="POST">
        @foreach($postData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <!-- Sayfa açılır açılmaz formu otomatik submit eden JavaScript -->
    <script>
        window.onload = function() {
            document.getElementById('threeDForm').submit();
        };
    </script>
</body>
</html>