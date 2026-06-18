# Operations

## EC2確認

```bash
systemctl status httpd
systemctl status php-fpm
curl http://localhost
```

## RDS接続確認

```bash
mysql -u root -p -h <RDS_ENDPOINT>
```

## Apacheログ確認

```bash
tail -n 30 /var/log/httpd/access_log
tail -n 30 /var/log/httpd/error_log
```

## PHP-FPMログ確認

```bash
cat /var/log/php-fpm/www-error.log
```
