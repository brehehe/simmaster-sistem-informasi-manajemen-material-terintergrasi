#!/usr/bin/env bash
set -e

echo "=========================================================="
echo "  Setup Staging Armaster (staging.armaster.net)"
echo "=========================================================="

if [ "$EUID" -ne 0 ]; then
  echo "Error: Script ini membutuhkan privilege root."
  echo "Silakan jalankan dengan: sudo bash $0"
  exit 1
fi

SOURCE_DIR="/var/www/html/armaster"
STAGING_DIR="/var/www/html/armaster-staging"
NGINX_CONF="/etc/nginx/sites-available/staging.armaster.net"
NGINX_LINK="/etc/nginx/sites-enabled/staging.armaster.net"

# 1. Salin file aplikasi ke staging jika belum ada
if [ ! -d "$STAGING_DIR" ]; then
    echo "[1/6] Menyalin $SOURCE_DIR ke $STAGING_DIR..."
    cp -a "$SOURCE_DIR" "$STAGING_DIR"
else
    echo "[1/6] Direktori $STAGING_DIR sudah ada, melewati copy file..."
fi

# 2. Permissions dan Ownership
echo "[2/6] Mengatur permissions dan ownership..."
chown -R fasmat:www-data "$STAGING_DIR"
chmod -R 775 "$STAGING_DIR/storage" "$STAGING_DIR/bootstrap/cache"

# 3. Konfigurasi .env Staging
echo "[3/6] Menyesuaikan konfigurasi .env staging..."
ENV_FILE="$STAGING_DIR/.env"
if [ -f "$ENV_FILE" ]; then
    sed -i 's/^APP_NAME=.*/APP_NAME="Armaster Staging"/' "$ENV_FILE"
    sed -i 's/^APP_ENV=.*/APP_ENV=staging/' "$ENV_FILE"
    sed -i 's|^APP_URL=.*|APP_URL=https://staging.armaster.net|' "$ENV_FILE"
    sed -i 's/^DB_DATABASE=.*/DB_DATABASE=fazmat_staging/' "$ENV_FILE"
fi

# 4. Storage symlink & Cache clear
echo "[4/6] Menghubungkan storage symlink & clear cache Laravel..."
rm -f "$STAGING_DIR/public/storage"
sudo -u fasmat php "$STAGING_DIR/artisan" storage:link || true
sudo -u fasmat php "$STAGING_DIR/artisan" optimize:clear || true
sudo -u fasmat php "$STAGING_DIR/artisan" config:clear || true
sudo -u fasmat php "$STAGING_DIR/artisan" cache:clear || true

# 5. Konfigurasi Nginx
echo "[5/6] Mengonfigurasi Nginx virtual host..."
cat << 'EOF' > "$NGINX_CONF"
server {
    server_name staging.armaster.net;
    root /var/www/html/armaster-staging/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    listen 80;
}
EOF

if [ ! -L "$NGINX_LINK" ]; then
    ln -s "$NGINX_CONF" "$NGINX_LINK"
fi

nginx -t
systemctl reload nginx

# 6. SSL Certbot
echo "[6/6] Memasang sertifikat SSL Let's Encrypt via Certbot..."
certbot --nginx -d staging.armaster.net --non-interactive --agree-tos --register-unsafely-without-email --redirect || certbot --nginx -d staging.armaster.net

echo "=========================================================="
echo "  SUKSES! Staging Armaster aktif di: https://staging.armaster.net"
echo "  Database: fazmat_staging"
echo "  Path: /var/www/html/armaster-staging"
echo "=========================================================="
