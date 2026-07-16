# Panduan Mengonlinekan `accounting-app` Laragon Menggunakan Cloudflare Tunnel

Dokumen ini berisi panduan lengkap untuk membuat website **accounting-app** yang berjalan di Laragon lokal (`D:\laragon`) dapat diakses secara online menggunakan **Cloudflare Tunnel**.

Ada dua metode yang bisa Anda gunakan:
1. **Metode 1: Quick Tunnel (Tanpa Akun & Domain)** – Sangat mudah, instan, cocok untuk demo/testing cepat. Menghasilkan URL acak gratis seperti `https://xxx.trycloudflare.com`.
2. **Metode 2: Cloudflare Tunnel Permanen (Menggunakan Domain Sendiri)** – Cocok untuk kebutuhan jangka panjang (produksi). Gratis menggunakan domain Anda yang sudah dihubungkan ke Cloudflare.

---

## 🛠️ Persiapan Awal

Sebelum memulai, pastikan:
* Laragon Anda sudah aktif di `D:\laragon` dan Apache/Nginx dalam status **Started**.
* Ketahui alamat lokal web Anda. Biasanya jika folder project bernama `accounting-app` di `D:\laragon\www\accounting-app`, maka alamat lokalnya adalah `http://accounting-app.test` atau `http://localhost/accounting-app`.

---

## ⚡ Metode 1: Quick Tunnel (Paling Cepat & Instan)

Metode ini tidak memerlukan pendaftaran akun Cloudflare atau kepemilikan domain. Cukup unduh aplikasinya dan jalankan perintah.

### Langkah 1: Unduh `cloudflared` untuk Windows
1. Unduh file executable `cloudflared` versi Windows langsung dari link resmi:  
   🔗 [Download cloudflared-windows-amd64.msi](https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.msi) atau versi zip [cloudflared-windows-amd64.zip](https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.zip).
2. Jika menggunakan `.msi`, instal seperti biasa. Jika menggunakan `.zip`, ekstrak file `cloudflared.exe` ke folder yang mudah diakses, misalnya di `C:\cloudflared\cloudflared.exe`.

### Langkah 2: Jalankan Tunnel via PowerShell / CMD
1. Buka **Command Prompt (CMD)** atau **PowerShell**.
2. Jalankan perintah berikut (sesuaikan dengan alamat lokal `accounting-app` Anda):

   * **Jika web Anda diakses via localhost:**
     ```bash
     cloudflared tunnel --url http://localhost/accounting-app
     ```
   * **Jika web Anda menggunakan virtual host Laragon (misal: `accounting-app.test`):**
     ```bash
     cloudflared tunnel --url http://accounting-app.test
     ```

3. Perhatikan output di CMD/PowerShell Anda. Cari baris yang mirip seperti ini:
   ```text
   +------------------------------------------------------------+
   |  Your quick tunnel has been created!                       |
   |  URL: https://some-random-subdomain.trycloudflare.com      |
   +------------------------------------------------------------+
   ```
4. Buka URL `https://some-random-subdomain.trycloudflare.com` di browser HP atau perangkat lain di luar jaringan Anda. Web Laragon Anda sekarang sudah online!

> [!IMPORTANT]
> **Kekurangan Quick Tunnel:** URL akan selalu berubah setiap kali Anda mematikan dan menjalankan ulang perintah di CMD. Jangan menutup CMD tersebut selama Anda ingin web tetap online.

---

## 🌐 Metode 2: Cloudflare Tunnel dengan Domain Sendiri

Gunakan metode ini jika Anda ingin alamat tetap seperti `accounting.domainanda.com`. Metode ini tidak menggunakan `trycloudflare.com` dan tidak memerlukan Cloudflare Access/Zero Trust agar website dapat dibuka publik.

> [!IMPORTANT]
> Laragon dan komputer harus tetap menyala serta terhubung ke internet. Cloudflare Tunnel menyediakan koneksi publik yang stabil, tetapi tidak otomatis menjadikan komputer lokal sebagai server produksi yang tahan gangguan.

### Langkah 1: Hubungkan Domain ke Cloudflare

1. Masuk atau buat akun di [Cloudflare Dashboard](https://dash.cloudflare.com/).
2. Pilih **Add a domain**, lalu masukkan domain yang baru dibeli.
3. Cloudflare akan memberikan dua nameserver. Ganti nameserver domain di dashboard registrar tempat domain dibeli dengan nameserver dari Cloudflare.
4. Tunggu sampai status domain di Cloudflare menjadi **Active** sebelum melanjutkan.

### Langkah 2: Buat Tunnel dari Dashboard Utama

1. Masuk ke **Cloudflare Dashboard**.
2. Buka **Networking** -> **Tunnels**.
3. Klik **Create a tunnel**.
4. Beri nama tunnel, misalnya `laragon-accounting-tunnel`, lalu klik **Create Tunnel**.

Untuk website publik, Anda tidak perlu membuat **Access Application**, login provider, atau kebijakan Zero Trust.

### Langkah 3: Instal `cloudflared` sebagai Windows Service

1. Pada halaman tunnel, pilih sistem operasi **Windows**.
2. Buka **Command Prompt** atau **PowerShell** menggunakan **Run as administrator**.
3. Salin dan jalankan perintah instalasi yang ditampilkan Cloudflare. Bentuknya kurang lebih seperti berikut:

   ```cmd
   cloudflared.exe service install YOUR_TUNNEL_TOKEN
   ```

4. Jangan membagikan `YOUR_TUNNEL_TOKEN` karena token tersebut dapat digunakan untuk menjalankan konektor tunnel Anda.
5. Tunggu sampai konektor terhubung dan status tunnel berubah menjadi **Healthy**, kemudian klik **Continue**.

Windows Service akan menjalankan `cloudflared` di latar belakang dan memulainya kembali saat Windows menyala. Status service dapat diperiksa dari PowerShell Administrator:

```powershell
Get-Service cloudflared
```

### Langkah 4: Publikasikan Aplikasi pada Domain

1. Buka tunnel yang sudah dibuat, lalu pilih tab **Routes**.
2. Klik **Add route** -> **Published application**.
3. Isi konfigurasi route:
   * **Subdomain:** misalnya `accounting`.
   * **Domain:** pilih domain Anda, misalnya `domainanda.com`.
   * **Path:** biarkan kosong agar seluruh aplikasi dapat diakses.
   * **Service URL:** gunakan alamat aplikasi Laragon, misalnya `http://accounting-app.test`.
4. Jika virtual host Laragon tidak dikenali, buka pengaturan tambahan HTTP dan isi **HTTP Host Header** dengan `accounting-app.test`.
5. Klik **Save**.

Cloudflare akan membuat route DNS untuk hostname tersebut. Website kemudian dapat dibuka melalui:

```text
https://accounting.domainanda.com
```

Hostname ini bersifat publik. Pengunjung tidak perlu akun Cloudflare dan tidak perlu memasang aplikasi tambahan.

### Langkah 5: Sesuaikan Konfigurasi Laravel

Ubah nilai `APP_URL` pada file `.env` agar sesuai dengan domain publik:

```dotenv
APP_URL=https://accounting.domainanda.com
APP_ENV=production
APP_DEBUG=false
```

Setelah mengubah `.env`, jalankan dari folder project:

```bash
php artisan optimize:clear
```

Pastikan aplikasi memiliki halaman login dan tidak mengizinkan pendaftaran pengguna umum jika memang tidak diperlukan.

### Langkah 6: Uji dari Jaringan Luar

1. Matikan Wi-Fi pada ponsel dan gunakan jaringan seluler.
2. Buka `https://accounting.domainanda.com`.
3. Pastikan halaman login tampil, HTTPS aktif, dan aset CSS/JavaScript termuat dengan benar.

Jika Cloudflare meminta login sebelum halaman aplikasi tampil, periksa menu **Zero Trust** -> **Access controls** -> **Applications** dan pastikan tidak ada Access Application yang melindungi hostname tersebut. Periksa juga pengaturan **Block traffic to all domains in this account**; nonaktifkan untuk hostname ini atau tambahkan pengecualian jika website memang harus publik.

---

## 💡 Troubleshooting & Tips Tambahan

### 1. Masalah SSL/TLS loop (Too Many Redirects)
Menggunakan `HTTP` pada **Service URL** tetap normal karena koneksi publik pengunjung ke Cloudflare menggunakan HTTPS. Jika terjadi redirect berulang:

1. Pastikan `APP_URL` menggunakan `https://`.
2. Bersihkan cache Laravel dengan `php artisan optimize:clear`.
3. Pastikan tidak ada aturan redirect yang saling bertentangan di Laravel, Apache, atau **Cloudflare Rules**.
4. Jangan mengubah mode SSL/TLS menjadi **Flexible** hanya untuk memperbaiki tunnel. Gunakan **Full (strict)** untuk konfigurasi domain Cloudflare jika origin yang diakses langsung memiliki sertifikat valid.

### 2. Konfigurasi `Host Header` untuk Virtual Host Laragon
Jika Anda menggunakan virtual host `.test` (seperti `accounting-app.test`) dan mendapati error **404 Not Found** dari Apache:
1. Masuk ke **Cloudflare Dashboard** -> **Networking** -> **Tunnels**, lalu buka tunnel Anda.
2. Pada tab **Routes**, edit **Published application** yang bermasalah.
3. Klik **Additional application settings** -> **HTTP Settings**.
4. Isi bagian **HTTP Host Header** dengan domain lokal Laragon Anda: `accounting-app.test`.
5. Klik **Save**.

### 3. Mengaktifkan HTTPS Lokal di Laragon (Opsional)
Jika Anda ingin tunnel terhubung secara aman secara end-to-end:
1. Klik kanan pada menu Laragon -> **Apache** -> **SSL** -> **Enabled**.
2. Jika HTTPS lokal menyala, gunakan `https://accounting-app.test` pada kolom URL di setting tunnel, dan aktifkan **No TLS Verify** di bagian *Additional Application Settings -> TLS*.
