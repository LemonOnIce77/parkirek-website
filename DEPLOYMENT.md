# 🚀 Deployment Parkirek ke Azure VM dengan Docker

Panduan resmi untuk menjalankan aplikasi **Parkirek** pada Azure Virtual Machine
( Ubuntu 22.04 / 24.04 LTS ) menggunakan **Docker + Docker Compose**, sehingga
aplikasi dapat diakses melalui `http://<IP_PUBLIC_VM>`.

---

## 📋 Arsitektur Deployment

```
                    Internet
                       │
            ┌──────────┴───────────┐
            │   Azure VM (Public IP)│
            │   Ubuntu + Docker     │
            │                       │
            │  ┌─────────────────┐  │
            │  │  Service: app   │  │  Nginx + PHP-FPM (Laravel 12)
            │  │  port 80 → host │  │
            │  └────┬───────┬────┘  │
            │       │       │       │
            │  network edge  │  network backend (INTERNAL)
            │       │       │       │
            │       │  ┌────┴─────┐ │
            │       │  │ Service: │ │  MySQL 8.4
            │       │  │   db     │ │  ❌ tanpa port publish
            │       │  └────┬─────┘ │  ✅ volume: parkirek_db_data
            │       │       │       │
            └───────┴───────┴───────┘
```

| Service | Image | Port publik | Jaringan | Volume |
|---------|-------|-------------|----------|--------|
| `app` | `parkirek-app:latest` (build lokal) | `80` | `edge` + `backend` | `parkirek_app_storage` |
| `db`  | `mysql:8.4` | **tidak ada** | `backend` (internal) | `parkirek_db_data` |

> 🔒 Database **tidak memiliki section `ports`**, sehingga tidak bisa diakses dari
> luar VM. Aplikasi hanya bisa menjangkau MySQL lewat jaringan internal `backend`.

---

## 1️⃣ Persiapan Azure VM

### 1.1 Membuat VM
1. Azure Portal → **Create a resource** → **Virtual machine**.
2. Ubuntu Server 22.04/24.04 LTS, minimal **2 vCPU / 4 GB RAM** (build Laravel butuh RAM).
3. Buat **Public IP** dan simpan IP-nya (contoh: `20.51.100.25`).
4. Port yang harus dibuka pada **Network Security Group (NSG)**:

   | Port | Protokol | Sumber | Keterangan |
   |------|----------|--------|------------|
   | 22   | TCP      | IP Anda | SSH |
   | 80   | TCP      | `0.0.0.0/0` | HTTP aplikasi |

### 1.2 Install Docker Engine + Compose Plugin (Ubuntu)

```bash
# Update paket
sudo apt update && sudo apt upgrade -y

# Install Docker Engine resmi Ubuntu
sudo apt install -y ca-certificates curl gnupg
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
  sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $VERSION_CODENAME) stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Izinkan user tanpa sudo memakai Docker (opsional)
sudo usermod -aG docker $USER && newgrp docker

# Verifikasi
docker --version
docker compose version
```

### 1.3 Buka port 80 pada firewall VM
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw enable
sudo ufw status
```

---

## 2️⃣ Menyalin Proyek ke VM

### Cara A — clone dari GitHub (disarankan)
```bash
cd ~
git clone https://github.com/Zhacksdev/parkirrek-website.git
cd parkirrek-website
```

### Cara B — kirim dari komputer lokal (scp)
```bash
# jalankan di komputer lokal
scp -r .\parkirrek-website user@<IP_PUBLIC_VM>:/home/user/
```

---

## 3️⃣ Konfigurasi `.env` (Rahasia)

```bash
cp .env.example .env
nano .env
```

Isi minimal:

```dotenv
APP_NAME=Parkirek
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<IP_PUBLIC_VM>          # GANTI dengan IP publik VM
APP_KEY=base64:<HASIL_GENERATE>
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=db                            # nama service database (jaringan internal)
DB_PORT=3306
DB_DATABASE=parkirek
DB_USERNAME=parkirek
DB_PASSWORD=<password_acak_kuat>
MYSQL_ROOT_PASSWORD=<password_acak_kuat_lain>

APP_PORT=80
SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Generate `APP_KEY` (tanpa PHP di host):

```bash
docker run --rm laravelsail/php8.3-composer:latest \
  php artisan key:generate --show
```

Atau dengan OpenSSL:

```bash
echo "APP_KEY=base64:$(openssl rand -base64 32)"
```

> 🔐 **`.env` tidak boleh di-commit.** File ini sudah tercakup di `.gitignore`.
> Yang masuk repository hanya `.env.example`.

---

## 4️⃣ Menjalankan Aplikasi

```bash
# Build image aplikasi + jalankan seluruh service
docker compose up -d --build

# Lihat status
docker compose ps

# Isi data demo (opsional)
docker compose exec app php artisan db:seed --force
```

Output yang diharapkan:

```
NAME           IMAGE                     SERVICE   STATUS
parkirek-app   parkirek-app:latest       app       Up (healthy)
parkirek-db    mysql:8.4                 db        Up (healthy)
```

> `app` hanya start **setelah** `db` berstatus `healthy`
> (`depends_on: condition: service_healthy`).

---

## 5️⃣ Pengujian / Bukti Pengujian

```bash
# 1. Endpoint sehat
curl -i http://<IP_PUBLIC_VM>/up
curl -i http://<IP_PUBLIC_VM>/

# 2. Database TIDAK bisa diakses dari luar (harus gagal / timeout)
telnet <IP_PUBLIC_VM> 3306        # harus gagal
nc -zv <IP_PUBLIC_VM> 3306        # harus "connection refused"

# 3. Pastikan tidak ada port MySQL yang ter-publish
docker compose ps
docker ps --format "table {{.Names}}\t{{.Ports}}"
# db  -> 3306/tcp   (tanpa 0.0.0.0:3306->3306)

# 4. Data tetap ada setelah container dihapus & dijalankan ulang
docker compose down
docker compose up -d
docker compose exec app php artisan tinker \
  --execute="echo App\Models\User::count().' users';"   # hasil tetap sama

# 5. Log aplikasi
docker compose logs -f app
docker compose logs -f db
```

Screenshot yang disarankan untuk laporan:
1. `docker compose build` sukses.
2. `docker compose ps` → kedua service **healthy**.
3. Browser: `http://<IP_PUBLIC_VM>` (landing page Parkirek).
4. Login mahasiswa/satpam berhasil.
5. `docker ps` → MySQL **tanpa** port publik.
6. `docker volume ls` → `parkirek_db_data` & `parkirek_app_storage`.
7. Hasil `curl http://<IP_PUBLIC_VM>/up` → HTTP 200.

---

## 6️⃣ Operasional

### Update aplikasi setelah push ke GitHub
```bash
cd ~/parkirrek-website
git pull
docker compose up -d --build          # rebuild image lalu recreate container
docker compose exec app php artisan migrate --force
```

### Melihat log
```bash
docker compose logs -f --tail=100 app
docker compose exec app tail -f storage/logs/laravel.log
```

### Restart service tertentu
```bash
docker compose restart app
docker compose restart db
```

### Backup & restore database
```bash
# Backup
docker compose exec db mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" parkirek > backup.sql

# Restore
docker compose exec -T db mysql -uroot -p"$MYSQL_ROOT_PASSWORD" parkirek < backup.sql
```

### Hapus container (data tetap aman)
```bash
docker compose down            # container & network dihapus, volume TETAP ada
docker compose down -v         # ⚠️ volume juga dihapus (data hilang!)
```

---

## 7️⃣ Troubleshooting

| Gejala | Penyebab & Solusi |
|--------|-------------------|
| `curl <IP>:<port>` refused | Port 80 belum diizinkan di **NSG** Azure dan/atau `ufw` pada VM |
| `app` restart berulang / `service "db" is unhealthy` | Kredensial MySQL tidak sinkron. Pastikan `MYSQL_ROOT_PASSWORD` & `DB_PASSWORD` di `.env` sama dengan yang dipakai saat volume pertama kali dibuat (ubah password pada volume lama butuh `ALTER USER`) |
| Halaman 500, log `Class ... does not exist` | Autoload composer belum optimal → `docker compose exec app composer dump-autoload -o` |
| `permission denied` pada `storage/` | Jalankan `docker compose exec app chown -R www-data:www-data storage bootstrap/cache` |
| `APP_KEY` kosong | Isi `APP_KEY` di `.env`, lalu `docker compose up -d --force-recreate app` |
| Port 80 sudah dipakai di VM | Ubah `APP_PORT=8080` di `.env` → akses `http://<IP>:8080`, dan buka port 8080 di NSG |

---

## 8️⃣ Perintah Singkat (Cheat Sheet)

```bash
docker compose up -d --build      # build + jalankan
docker compose ps                 # status & health
docker compose logs -f app        # log aplikasi
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan tinker
docker compose down               # stop & hapus container (volume aman)
docker compose restart app
```

---

## 📚 Referensi

- Docker Compose Specification: https://docs.docker.com/compose/
- Install Docker Engine on Ubuntu: https://docs.docker.com/engine/install/ubuntu/
- Laravel 12 Deployment: https://laravel.com/docs/12.x/deployment
