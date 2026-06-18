#!/bin/bash
set -eux

timedatectl set-timezone Asia/Tokyo
localectl set-locale ja_JP.UTF-8
hostnamectl set-hostname testSV2

dnf -y update || true
dnf -y install mariadb105
