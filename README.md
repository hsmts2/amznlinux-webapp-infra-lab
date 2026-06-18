# amznlinux-webapp-infra-lab

『実践 Amazon Linux』の学習内容をもとに、Amazon Linux 2023 上で構築した Web アプリケーション環境を AWS CloudFormation で再現するためのリポジトリです。

本リポジトリは、書籍で学習した Amazon Linux 2023、Apache httpd、PHP、MariaDB、Amazon RDS、Amazon S3 を組み合わせた構成を、自分の理解に基づいて Infrastructure as Code として整理したものです。

書籍内容を転載するものではなく、学習した構成を CloudFormation テンプレートとして再現・管理することを目的としています。

---

<br>

## 概要

本リポジトリでは、Amazon Linux 2023 を利用した Web アプリケーション環境を CloudFormation テンプレートとして管理します。

主な構成は以下です。

- VPC
- Public Subnet
- Private Subnet
- Internet Gateway
- Route Table
- EC2 Instance Connect Endpoint
- S3 Gateway Endpoint
- Security Group
- EC2 Instance
- Elastic IP
- EBS Volume
- Amazon RDS for MariaDB
- Amazon S3 Bucket
- Apache httpd
- PHP / php-fpm
- MariaDB 接続用 Web アプリケーション

---

<br>

## テンプレート構成

```text
amznlinux-webapp-infra-lab/
├── README.md
├── templates/
│   ├── 01-network.yml
│   ├── 02-compute.yml
│   └── 03-data-storage.yml
├── params/
│   ├── dev-network.json
│   ├── dev-compute.json
│   └── dev-data-storage.json
├── userdata/
│   ├── testsv1-userdata.sh
│   └── testsv2-userdata.sh
├── app/
│   ├── index.html
│   ├── sampledir/
│   │   └── test.html
│   ├── s3test.html
│   ├── s3test2.html
│   ├── db.php.example
│   ├── register.php
│   ├── login.php
│   ├── welcome.php
│   └── logout.php
├── sql/
│   └── init-users-db.sql
├── docs/
│   ├── architecture.md
│   ├── deployment.md
│   ├── operations.md
│   └── cleanup.md
└── .gitignore
```

---

<br>

## スタック構成

| スタック | テンプレート | 内容 |
|---|---|---|
| network | [templates/01-network.yml](templates/01-network.yml) | VPC、Subnet、IGW、Route Table、EC2 Instance Connect Endpoint、S3 Gateway Endpoint |
| compute | [templates/02-compute.yml](templates/02-compute.yml) | Security Group、EC2 testSV1/testSV2、Elastic IP、EBS、IAM Role、UserData |
| data-storage | [templates/03-data-storage.yml](templates/03-data-storage.yml) | Amazon RDS for MariaDB、DB Subnet Group、S3 Bucket、Bucket Policy |

---

<br>

## デプロイ順序

依存関係があるため、以下の順番でデプロイします。

```text
1. network
2. compute
3. data-storage
```

### 1. network

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-network \
  --template-body file://templates/01-network.yml \
  --parameters file://params/dev-network.json
```

### 2. compute

[params/dev-compute.json](params/dev-compute.json) の `AmiId`、`KeyName`、`AllowedSshCidr`、`ImageBucketName` は自分の環境に合わせて変更してください。

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-compute \
  --template-body file://templates/02-compute.yml \
  --parameters file://params/dev-compute.json \
  --capabilities CAPABILITY_NAMED_IAM
```

### 3. data-storage

[params/dev-data-storage.json](params/dev-data-storage.json) の `ImageBucketName`、`DBMasterPassword` は自分の環境に合わせて変更してください。

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-data-storage \
  --template-body file://templates/03-data-storage.yml \
  --parameters file://params/dev-data-storage.json
```

---

<br>

## アプリケーション配置

`app/` 配下には、学習で作成した HTML / PHP ファイルを配置しています。

| ファイル | 内容 |
|---|---|
| [app/index.html](app/index.html) | Apache DocumentRoot確認用ページ |
| [app/sampledir/test.html](app/sampledir/test.html) | サブディレクトリ配信確認用ページ |
| [app/s3test.html](app/s3test.html) | S3オブジェクトURLを直接参照するHTML |
| [app/s3test2.html](app/s3test2.html) | Apache Redirect経由でS3画像を参照するHTML |
| [app/db.php.example](app/db.php.example) | DB接続情報のサンプル |
| [app/register.php](app/register.php) | ユーザー登録画面 |
| [app/login.php](app/login.php) | ログイン画面 |
| [app/welcome.php](app/welcome.php) | ログイン成功後の画面 |
| [app/logout.php](app/logout.php) | ログアウト処理 |

`app/db.php` は接続情報を含むため、Git管理対象外にしています。

---

<br>

## SQL

[sql/init-users-db.sql](sql/init-users-db.sql) には、学習で実行したデータベース、テーブル、ユーザー、権限作成SQLを配置しています。

```sql
CREATE DATABASE users;

USE users;

CREATE TABLE tbl_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

CREATE USER 'testuser'@'172.31.%' IDENTIFIED BY '<YOUR_APP_DB_PASSWORD>';

GRANT ALL PRIVILEGES ON users.* TO 'testuser'@'172.31.%';
```

---

<br>

## 注意事項

本リポジトリは学習用途です。

以下はGitHubにコミットしないでください。

- 秘密鍵、`.pem`
- DBパスワード
- RDSマスターパスワード
- AWSアクセスキー
- 個人のグローバルIPアドレス
- 実在環境のエンドポイント
- `app/db.php`

---

<br>

## 削除順序

スタック削除は、作成順とは逆に行います。

```text
1. data-storage
2. compute
3. network
```

```bash
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-data-storage
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-compute
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-network
```
