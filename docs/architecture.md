# Architecture

## 構成概要

```text
Internet
  ↓
Internet Gateway
  ↓
Public Subnet
  ↓
EC2 testSV1
  ├─ Apache httpd
  ├─ PHP / php-fpm
  ├─ Web Application
  └─ S3 image reference
       ↓
      Amazon S3

Private Subnet
  ↓
EC2 testSV2

Private Subnets
  ↓
Amazon RDS for MariaDB
```
