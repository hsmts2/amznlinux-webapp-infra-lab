#!/bin/bash
set -eux

# Amazon Linux basic settings
timedatectl set-timezone Asia/Tokyo
localectl set-locale ja_JP.UTF-8
hostnamectl set-hostname testSV1

# Packages
dnf -y update || true
dnf -y install httpd php php8.3-mysqlnd cronie mariadb105

# Services
systemctl enable --now httpd
systemctl enable --now php-fpm
systemctl enable --now crond

# DocumentRoot sample files
cat > /var/www/html/index.html <<'HTML'
<h1>test page: Amazon Linux 2023</h1>
HTML

mkdir -p /var/www/html/sampledir
cat > /var/www/html/sampledir/test.html <<'HTML'
<h1>test page: sampledir</h1>
HTML

systemctl restart httpd
