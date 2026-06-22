# Hostdata CI/CD Known-Good Setup

Ini adalah catatan **kanonik** untuk deploy production Laravel ke Hostdata via GitHub Actions di repo ini.

Kalau nanti lupa detail yang benar, pakai file ini dulu sebelum coba-coba setting lain.

## Status

Setting di bawah ini adalah pola yang **sudah terbukti lolos** untuk konfigurasi GitHub Actions + Hostdata.

## 1. Secret SSH yang benar

Gunakan:

- **private key tanpa passphrase**
- simpan ke GitHub secret `HOSTDATA_SSH_KEY`
- format yang **paling stabil**: **base64 dari file private key**

### Jangan pakai

- public key `.pub`
- private key yang masih punya passphrase
- `.ppk`
- copy-paste multiline yang meragukan

## 2. Cara generate deploy key yang benar

Di PowerShell:

```powershell
ssh-keygen -t ed25519 -C "github-actions-invitely-production" -f "$env:USERPROFILE\.ssh\invitely_production_ed25519"
```

Saat diminta passphrase:

- tekan `Enter`
- tekan `Enter` lagi

Artinya key dibuat **tanpa passphrase**.

## 3. Cara verifikasi key lokal

Sebelum upload ke GitHub, pastikan key memang bisa dibaca:

```powershell
ssh-keygen -y -f "$env:USERPROFILE\.ssh\invitely_production_ed25519"
```

Kalau command itu langsung mengeluarkan public key tanpa prompt apapun, key sudah benar untuk GitHub Actions.

## 4. Cara isi `HOSTDATA_SSH_KEY` yang paling aman

Jangan paste raw multiline kalau tidak perlu.

Pakai **base64** dari private key:

```powershell
[Convert]::ToBase64String([IO.File]::ReadAllBytes("$env:USERPROFILE\.ssh\invitely_production_ed25519"))
```

Lalu:

1. copy output satu baris
2. buka GitHub repo
3. masuk `Settings -> Secrets and variables -> Actions`
4. buat/update secret `HOSTDATA_SSH_KEY`
5. paste hasil base64 tadi

Itu format yang paling aman terhadap:

- line break rusak
- paste multiline GitHub
- karakter carriage return dari Windows

## 5. Public key di server

Isi file ini:

- `invitely_production_ed25519.pub`

harus masuk ke:

```text
~/.ssh/authorized_keys
```

Di server, ini hanya untuk otorisasi login SSH. Ini **bukan** yang diisi ke GitHub secret.

## 6. Variable path yang benar

`HOSTDATA_APP_PATH` harus:

- **tanpa leading slash**
- **tanpa tab**
- **tanpa enter**
- **satu baris plain text**

Benar:

```text
domains/hafmul.site/public_html/invitely
```

Salah:

```text
/domains/hafmul.site/public_html/invitely
```

atau:

```text
    domains/hafmul.site/public_html/invitely
```

## 7. Minimum values yang harus ada di GitHub

### Secrets

- `HOSTDATA_HOST`
- `HOSTDATA_USER`
- `HOSTDATA_SSH_KEY`

### Variables

- `HOSTDATA_APP_PATH`
- `HOSTDATA_PORT`

Contoh:

```text
HOSTDATA_PORT=22
HOSTDATA_APP_PATH=domains/hafmul.site/public_html/invitely
```

## 8. Kalau error `HOSTDATA_SSH_KEY is not a valid unencrypted OpenSSH private key`

Artinya hampir pasti salah satu dari ini:

1. secret berisi key yang salah
2. key masih ber-passphrase
3. secret kepaste rusak
4. secret diisi raw multiline yang tidak identik dengan file asli

Solusi tercepat:

1. generate key baru tanpa passphrase
2. konversi private key itu ke base64
3. replace `HOSTDATA_SSH_KEY` dengan hasil base64
4. rerun workflow

## 9. Urutan deploy yang direkomendasikan

1. Generate deploy key baru tanpa passphrase
2. Test lokal dengan `ssh-keygen -y -f ...`
3. Convert private key ke base64
4. Simpan base64 ke `HOSTDATA_SSH_KEY`
5. Tambahkan public key ke `authorized_keys`
6. Set `HOSTDATA_APP_PATH` tanpa slash depan
7. Jalankan workflow manual sekali

## 10. File yang jadi referensi utama

- [deploy-hostdata.yml](/D:/Downloads/KBT/.github/workflows/deploy-hostdata.yml)
- [hostdata-github-actions.md](/D:/Downloads/KBT/docs/deployment/hostdata-github-actions.md)
- [hostdata-cicd-known-good.md](/D:/Downloads/KBT/docs/deployment/hostdata-cicd-known-good.md)

Kalau ada kebingungan nanti, anggap file ini sebagai ringkasan final yang paling praktis.
