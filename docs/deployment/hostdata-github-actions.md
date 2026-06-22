# Invitely Deployment to Hostdata via GitHub Actions

Dokumen ini fokus ke **deploy production saja** untuk aplikasi Laravel `Invitely by NechCode`.

Workflow yang dipakai ada di:

- [deploy-hostdata.yml](/D:/Downloads/KBT/.github/workflows/deploy-hostdata.yml)
- [hostdata-cicd-known-good.md](/D:/Downloads/KBT/docs/deployment/hostdata-cicd-known-good.md)

Deploy modelnya:

- build dependency dan asset di GitHub Actions
- sync hasil aplikasi ke server Hostdata lewat SSH + `rsync`
- jalankan migrasi dan cache Laravel di server

## Asumsi deployment

Dokumen ini mengasumsikan:

- repo ini di-push ke GitHub
- hosting kamu mendukung SSH
- targetnya shared hosting / VPS dengan pola mirip Hostdata
- kamu akan menjalankan Laravel dari folder app, lalu document root domain diarahkan ke folder `public`

## 1. Tentukan path production di server

Contoh:

```text
domains/example.com/public_html/invitely
```

Maka document root domain atau subdomain harus diarahkan ke:

```text
domains/example.com/public_html/invitely/public
```

Kalau kamu deploy di subdomain seperti `invitely.nechcode.com`, biasanya hasil akhirnya tetap:

- app root: `domains/nechcode.com/public_html/invitely`
- web root: `domains/nechcode.com/public_html/invitely/public`

Kalau path di panel hosting berbeda, nanti isi variabel `HOSTDATA_APP_PATH` dengan path yang benar.

## 2. Buat SSH key khusus untuk GitHub Actions

Jangan pakai key laptop utama. Buat key baru khusus deploy.

Di PowerShell:

```powershell
ssh-keygen -t ed25519 -C "github-actions-invitely-production" -f "$env:USERPROFILE\.ssh\invitely_production_ed25519"
```

File yang dihasilkan:

- private key: `invitely_production_ed25519`
- public key: `invitely_production_ed25519.pub`

## 3. Pasang public key di server

Tambahkan isi file `.pub` ke:

```text
~/.ssh/authorized_keys
```

Kalau belum ada folder app, buat dulu:

```bash
mkdir -p domains/example.com/public_html/invitely
```

## 4. Siapkan `.env` production di server

Workflow ini **tidak mengirim `.env` dari GitHub**. Jadi `.env` production harus kamu buat manual di server.

Lokasi:

```text
domains/example.com/public_html/invitely/.env
```

Contoh minimal:

```dotenv
APP_NAME="Invitely by NechCode"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://invitely.example.com
APP_KEY=base64:ISI_APP_KEY_KAMU

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invitely_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=invitely.example.com
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="Invitely by NechCode"
```

### Penting untuk `APP_KEY`

Jangan generate `APP_KEY` setiap deploy.

Generate **sekali saja**, lalu simpan permanen di `.env` production.

Kalau perlu generate dari lokal:

```powershell
php artisan key:generate --show
```

Lalu copy hasilnya ke `APP_KEY=...` di `.env` server.

## 5. Siapkan permission folder Laravel

Pastikan folder berikut writable:

- `storage/`
- `bootstrap/cache/`

Workflow juga akan memastikan subfolder ini ada:

- `storage/app/public`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/framework/views`
- `storage/logs`

## 6. Tambahkan GitHub Secrets dan Variables

### Secrets

Tambahkan di repo GitHub:

- `HOSTDATA_HOST`
- `HOSTDATA_USER`
- `HOSTDATA_SSH_KEY`

Isi `HOSTDATA_SSH_KEY`:

- full private key dari file `invitely_production_ed25519`
- bukan `.pub`
- tanpa passphrase

Rekomendasi resmi untuk repo ini:

- **pakai base64 dari file private key**
- jangan mengandalkan raw multiline kecuali kamu benar-benar yakin paste-nya bersih

Format raw yang benar harus diawali:

```text
-----BEGIN OPENSSH PRIVATE KEY-----
```

dan diakhiri:

```text
-----END OPENSSH PRIVATE KEY-----
```

Kalau mau, kamu juga bisa simpan key dalam format base64. Workflow ini menerima dua format:

- raw multiline OpenSSH key
- base64 dari private key file

Untuk buat base64 di PowerShell:

```powershell
[Convert]::ToBase64String([IO.File]::ReadAllBytes("$env:USERPROFILE\.ssh\invitely_production_ed25519"))
```

Ini adalah format secret yang paling direkomendasikan karena paling tahan terhadap masalah line break saat copy-paste di GitHub.

### Variables

Tambahkan repository variables:

- `HOSTDATA_APP_PATH`
- `HOSTDATA_PORT`

Contoh:

```text
HOSTDATA_APP_PATH=domains/example.com/public_html/invitely
HOSTDATA_PORT=22
```

Penting:

- `HOSTDATA_APP_PATH` harus satu baris plain text
- jangan pakai leading slash
- jangan ada tab atau enter tersembunyi

## 7. Push ke branch `main`

Workflow deploy hanya jalan dari branch:

```text
main
```

Jadi pastikan repo production kamu memang deploy dari `main`.

## 8. Run deploy pertama

Kamu punya dua opsi:

1. push ke `main`
2. atau jalankan manual dari tab **Actions** → workflow **Deploy Invitely to Hostdata**

Untuk deploy pertama, saya sarankan jalankan manual dulu supaya lebih mudah baca log.

## 9. Queue production untuk Fonnte broadcast

Ini bagian penting.

Aplikasi ini memakai job queue untuk broadcast Fonnte:

- `SendFonnteBroadcastJob`

Artinya deploy saja belum cukup. Kamu juga harus menjalankan worker queue di production.

Kalau hosting kamu tidak menyediakan supervisor permanen, set cron untuk worker database queue.

Contoh cron per menit:

```bash
* * * * * cd /home/USERNAME/domains/example.com/public_html/invitely && /usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=120 >> /dev/null 2>&1
```

Kalau path PHP di server bukan `/usr/bin/php`, sesuaikan dengan binary yang benar. Workflow deploy sudah mencoba mendeteksi CLI PHP, tapi cron harus kamu isi manual sesuai server.

## 10. Checklist deploy production

Checklist ringkas:

1. Repo sudah ada di GitHub.
2. Branch production adalah `main`.
3. SSH key deploy sudah dibuat.
4. Public key sudah masuk ke `authorized_keys`.
5. Folder app di server sudah ada.
6. Document root domain mengarah ke `/public`.
7. `.env` production sudah dibuat manual.
8. Database MySQL production sudah siap.
9. Secrets GitHub sudah diisi.
10. Variables GitHub sudah diisi.
11. Cron queue worker sudah dibuat.
12. Jalankan workflow deploy.

## 11. Kalau deploy gagal

### Jika gagal di SSH key / `libcrypto`

Cek:

- `HOSTDATA_SSH_KEY` berisi private key, bukan public key
- key tidak punya passphrase
- format key benar
- public key yang cocok ada di `authorized_keys`
- `HOSTDATA_HOST` adalah host SSH / IP server, bukan URL aplikasi

Catatan penting dari implementasi yang sudah lolos:

- kalau raw multiline terasa benar tapi tetap gagal, pindah ke **base64 private key**
- itu adalah jalur yang paling stabil untuk `HOSTDATA_SSH_KEY`

### Jika deploy sukses tapi web error

Cek:

- `.env` ada di server
- `APP_KEY` valid
- kredensial MySQL benar
- document root benar-benar diarahkan ke `/public`
- `storage/` dan `bootstrap/cache/` writable
- migrasi berhasil
- queue worker aktif

## 12. Hal yang sengaja tidak saya masukkan

Sesuai permintaan kamu, versi ini fokus **deployment production only**:

- tidak ada workflow test
- tidak ada workflow preview
- tidak ada environment staging
- tidak ada deploy dev

Kalau kamu mau, langkah berikutnya saya bisa bantu 2 hal:

1. isi file ini dengan **nilai host/path yang benar** sesuai hosting kamu
2. review `.env` production final yang akan kamu pasang di server
