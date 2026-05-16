# QNB Finansbank VPOS 3D Secure Laravel Entegrasyonu 💳

Bu proje, **Laravel 11** ve **Docker (Laravel Sail)** kullanılarak geliştirilmiş, profesyonel bir sanal POS ödeme entegrasyonu örneğidir. QNB Finansbank'ın 3D Secure (Payfor) altyapısını kullanarak ödeme alma, doğrulama ve hata yönetimi süreçlerini içerir.

## 🛠 Teknik Özellikler

- **PHP 8.3 & Laravel 11**
- **Docker & Laravel Sail** (Sektör standardı izolasyon)
- **SQLite** (Hızlı kurulum için veritabanı dosyası)
- **3D Secure Payfor Altyapısı**
- **Logging System** (Banka yanıtlarının detaylı takibi)

## 🚀 Hızlı Kurulum

Bilgisayarınızda **Docker Desktop** yüklü olması yeterlidir. Başka hiçbir şeye (PHP, MySQL vb.) ihtiyacınız yoktur.
1. Adım: Docker Desktop Kurulumu (Zorunlu)
Docker komutlarının çalışması için bilgisayarında bir "motor" olması lazım.

Docker Desktop adresine git ve Windows sürümünü indir.

Kurulumu yap (Kurulum sırasında "Use WSL 2 instead of Hyper-V" seçeneğinin işaretli olduğundan emin ol).

Bilgisayarını yeniden başlatmanı isteyecektir, başlat.

2. Adım: Docker'ı Başlat
Bilgisayarın açıldığında Docker Desktop uygulamasını çalıştır. Sağ altta küçük bir balina ikonu göreceksin. O ikon yeşil olana kadar (Engine Running yazana kadar) bekle.

### 1. Projeyi Klonlayın
```bash
git clone https://github.com/Mustafa0291polat/Banka-Odeme-Entegrasyon-Sistemi.git
cd Banka-Odeme-Entegrasyon-Sistemi
(Not: ZIP olarak indirdiyseniz klasör isminin sonuna -main ekleyerek girin).

### 2. Bağımlılıkları Yükleyin (Docker ile)
# Linux ve macOS için:
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs

# Windows (PowerShell) için:
docker run --rm -v ${PWD}:/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs

### 3. Çevresel Değişkenleri Hazırlayın
 cp .env.example .env
`.env` dosyasını açın ve `APP_URL` kısmını (bir sonraki adımda alacağınız) tünel URL'i ile güncelleyin.

### 4. Projeyi Ayağa Kaldırın
# Linux ve macOS için:

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# Windows İçin (Git Bash veya WSL Terminali kullanarak):

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

*Not: Windows PowerShell kullanıyorsanız komutların başına 'wsl' ekleyin (Örn: wsl vendor/bin/sail up -d).*
 wsl ./vendor/bin/sail artisan key:generate
 wsl ./vendor/bin/sail artisan migrate

## 🌍 Dış Dünyaya Açılma (Webhook Handling)

Bankanın ödeme sonucunu `callback` adresinize gönderebilmesi için localhost'unuzu internete açmanız gerekir.

**Tavsiye Edilen:**

.\ngrok http 8000     

Ekranda verilen URL'i kopyalayın ve `.env` içindeki `FINANSBANK_SUCCESS_URL` ile `FINANSBANK_FAIL_URL` değişkenlerine yapıştırın. 

## 🧪 Test Senaryoları ve Kartlar

Proje şu an **Test (Sandbox)** ortamına ayarlıdır. `FinansbankPaymentService.php` içindeki API bilgileri QNB test terminaline aittir.


## 🔍 Log Takibi

Banka ile yapılan tüm iletişim `storage/logs/laravel.log` dosyasına kaydedilir. Hata durumunda logları şu komutla canlı izleyebilirsiniz:
wsl ./vendor/bin/sail artisan logs
