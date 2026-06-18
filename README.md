# amznlinux-webapp-infra-lab

『実践 Amazon Linux』の学習内容をもとに、Amazon Linux 2023 上で構築した Web アプリケーション環境を AWS CloudFormation で再現するためのリポジトリです。

本リポジトリは、書籍で学習した Amazon Linux 2023、Apache httpd、PHP、MariaDB、Amazon RDS、Amazon S3 を組み合わせた構成を、自分の理解に基づいて Infrastructure as Code として整理したものです。書籍内容を転載するものではなく、学習した構成を CloudFormation テンプレート、UserData、サンプルアプリケーション、SQL として再現・管理することを目的としています。

---

<br>

## 概要

本リポジトリでは、Amazon Linux 2023 を利用した Web アプリケーション環境を CloudFormation テンプレートとして管理します。

主な構成は、VPC、Public / Private Subnet、EC2 Instance Connect Endpoint、S3 Gateway Endpoint、EC2、EBS、Amazon RDS for MariaDB、Amazon S3、Apache httpd、PHP / php-fpm、MariaDB 接続用 Web アプリケーションです。

---

<br>

## 対象書籍と学習範囲

| 項目 | 内容 |
| --- | --- |
| 対象書籍 | 実践 Amazon Linux |
| 学習テーマ | Amazon Linux 2023 上での Web アプリケーション環境構築 |
| 主な学習対象 | Amazon Linux 2023 / Apache httpd / PHP / php-fpm / MariaDB / Amazon RDS / Amazon S3 |
| 本リポジトリで作成する範囲 | VPC / EC2 / EBS / RDS / S3 / Security Group / IAM Role / UserData |
| 作成するAWS構成 | EC2 Web サーバー + RDS MariaDB + S3 の Web アプリケーション構成 |
| IaC | AWS CloudFormation |
| テンプレート形式 | YAML |
| リージョン | `ap-northeast-1` |

---

<br>

## リポジトリの目的

このリポジトリの目的は、単に CloudFormation テンプレートを保存することではなく、以下の観点を整理することです。

* Amazon Linux 2023 上の Web アプリケーション環境をコードとして再現する
* Apache httpd、PHP、php-fpm、MariaDB クライアントの初期設定を UserData として整理する
* EC2、RDS、S3 を組み合わせた基本的な Web アプリケーション構成を理解する
* CloudFormation のスタック分割と Cross Stack Export / ImportValue の関係を整理する
* GitHub 上で学習成果をポートフォリオとして管理する

---

<br>

## アーキテクチャ

以下は、本リポジトリの CloudFormation テンプレートで作成する AWS 構成の概要です。

```text
Internet
  |
  v
Internet Gateway
  |
  v
Public Subnet
  |
  v
EC2 testSV1
  ├── Apache httpd
  ├── PHP / php-fpm
  ├── Web Application
  └── S3 image reference
       |
       v
      Amazon S3

Private Subnet
  |
  v
EC2 testSV2

Private Subnets
  |
  v
Amazon RDS for MariaDB
```

---

<br>

## 作成される構成

CloudFormation スタックは、依存関係に合わせて以下の3つに分けています。

```text
1. network
2. compute
3. data-storage
```

| スタック | テンプレート | 内容 |
| --- | --- | --- |
| network | [templates/01-network.yml](templates/01-network.yml) | VPC、Subnet、Internet Gateway、Route Table、EC2 Instance Connect Endpoint、S3 Gateway Endpoint |
| compute | [templates/02-compute.yml](templates/02-compute.yml) | Security Group、EC2 testSV1 / testSV2、Elastic IP、EBS、IAM Role、UserData |
| data-storage | [templates/03-data-storage.yml](templates/03-data-storage.yml) | Amazon RDS for MariaDB、DB Subnet Group、S3 Bucket、Bucket Policy |

---

<br>

## 作成される主な AWS リソース

| 分類 | 作成するリソース |
| --- | --- |
| Network | VPC、Public Subnet、Private Subnet、Internet Gateway、Route Table |
| Endpoint | EC2 Instance Connect Endpoint、S3 Gateway Endpoint |
| Security | Security Group（Web / DB / Endpoint） |
| Compute | EC2 testSV1、EC2 testSV2、Elastic IP、EBS Volume |
| IAM | EC2 用 IAM Role / Instance Profile |
| Database | Amazon RDS for MariaDB |
| Storage | Amazon S3 Bucket、Bucket Policy |
| Application | Apache httpd、PHP / php-fpm、HTML / PHP サンプルアプリケーション |

---

<br>

## 本テンプレートで対象外にしたもの

| 対象外 | 理由 |
| --- | --- |
| HTTPS / ACM | ドメイン取得や証明書検証が環境依存になるため |
| Route 53 | 独自ドメイン運用を前提にしない学習構成のため |
| ALB | まずは単一 Web サーバー構成と OS / ミドルウェア設定の理解に絞るため |
| Auto Scaling | Amazon Linux 2023 上の基本構築を優先するため |

---

<br>

## ディレクトリ構成

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
└── .gitignore
```

---

<br>

## CloudFormation テンプレート

CloudFormation テンプレートは以下に格納しています。

| テンプレート | 内容 |
| --- | --- |
| [templates/01-network.yml](templates/01-network.yml) | VPC、Subnet、Internet Gateway、Route Table、Endpoint |
| [templates/02-compute.yml](templates/02-compute.yml) | Security Group、EC2、EBS、IAM Role、UserData |
| [templates/03-data-storage.yml](templates/03-data-storage.yml) | RDS、S3、Bucket Policy |

---

<br>

## パラメータファイル

パラメータファイルのサンプルは以下に格納しています。

| ファイル | 用途 |
| --- | --- |
| [params/dev-network.json](params/dev-network.json) | network スタック用パラメータ |
| [params/dev-compute.json](params/dev-compute.json) | compute スタック用パラメータ |
| [params/dev-data-storage.json](params/dev-data-storage.json) | data-storage スタック用パラメータ |

`params/dev-compute.json` の `AmiId`、`KeyName`、`AllowedSshCidr`、`ImageBucketName` と、`params/dev-data-storage.json` の `ImageBucketName`、`DBMasterPassword` は自分の環境に合わせて変更してください。

---

<br>

## 前提条件

以下の環境が必要です。

* AWS CLI v2
* AWS CLI の認証設定済み環境
* デプロイ先 AWS アカウント
* デプロイ先リージョン: `ap-northeast-1`
* 既存の EC2 キーペア
* Amazon Linux 2023 の AMI ID
* CloudFormation、EC2、VPC、IAM、RDS、S3 関連リソースを作成・更新・削除できる IAM 権限

---

<br>

## デプロイ・運用方法

### パラメータ準備

各 `params/*.json` の値を、自分の環境に合わせて変更します。

主に変更が必要な値は以下です。

```text
<YOUR_AMI_ID>
<YOUR_KEY_NAME>
<YOUR_GLOBAL_IP>/32
<YOUR_UNIQUE_IMAGE_BUCKET_NAME>
<YOUR_DB_MASTER_PASSWORD>
```

### テンプレート検証

```bash
aws cloudformation validate-template \
  --template-body file://templates/01-network.yml

aws cloudformation validate-template \
  --template-body file://templates/02-compute.yml

aws cloudformation validate-template \
  --template-body file://templates/03-data-storage.yml
```

### スタック作成

依存関係があるため、`network`、`compute`、`data-storage` の順番で作成します。

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-network \
  --template-body file://templates/01-network.yml \
  --parameters file://params/dev-network.json
```

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-compute \
  --template-body file://templates/02-compute.yml \
  --parameters file://params/dev-compute.json \
  --capabilities CAPABILITY_NAMED_IAM
```

```bash
aws cloudformation create-stack \
  --stack-name amznlinux-webapp-infra-lab-data-storage \
  --template-body file://templates/03-data-storage.yml \
  --parameters file://params/dev-data-storage.json
```

### スタック状態確認

```bash
aws cloudformation describe-stacks \
  --stack-name amznlinux-webapp-infra-lab-network

aws cloudformation describe-stacks \
  --stack-name amznlinux-webapp-infra-lab-compute

aws cloudformation describe-stacks \
  --stack-name amznlinux-webapp-infra-lab-data-storage
```

### 動作確認

EC2 上で Apache httpd、PHP-FPM、RDS 接続、ログを確認します。

```bash
systemctl status httpd
systemctl status php-fpm
curl http://localhost
```

```bash
mysql -u root -p -h <RDS_ENDPOINT>
```

```bash
tail -n 30 /var/log/httpd/access_log
tail -n 30 /var/log/httpd/error_log
cat /var/log/php-fpm/www-error.log
```

### スタック更新

変更対象のテンプレートに応じて、該当スタックを更新します。

```bash
aws cloudformation update-stack \
  --stack-name amznlinux-webapp-infra-lab-compute \
  --template-body file://templates/02-compute.yml \
  --parameters file://params/dev-compute.json \
  --capabilities CAPABILITY_NAMED_IAM
```

### スタック削除

スタック削除は、作成順とは逆に行います。

```bash
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-data-storage
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-compute
aws cloudformation delete-stack --stack-name amznlinux-webapp-infra-lab-network
```

---

<br>

## 設計上の補足

`app/` 配下には、学習で作成した HTML / PHP ファイルを配置しています。

| ファイル | 内容 |
| --- | --- |
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

[sql/init-users-db.sql](sql/init-users-db.sql) には、学習で実行したデータベース、テーブル、ユーザー、権限作成SQLを配置しています。アプリケーション用DBパスワードは `<YOUR_APP_DB_PASSWORD>` に置き換えて使用します。

以下は GitHub にコミットしないでください。

* 秘密鍵、`.pem`
* DBパスワード
* RDSマスターパスワード
* AWSアクセスキー
* 個人のグローバルIPアドレス
* 実在環境のエンドポイント
* `app/db.php`

---

<br>

## 書籍との差分

書籍で手動構築した内容をもとに、CloudFormation で同等の学習環境を再現できるようにしています。ネットワーク、コンピュート、データストレージを3つのスタックに分け、Cross Stack Export / ImportValue で連携する構成に整理しています。
