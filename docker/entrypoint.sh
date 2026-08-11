#!/bin/sh
set -e

cd /var/www/html

php -r '
$host = getenv("DB_HOST");
$port = getenv("DB_PORT") ?: "3306";
$db   = getenv("DB_DATABASE");
$user = getenv("DB_USERNAME");
$pass = getenv("DB_PASSWORD");

if (!$host || !$db || !$user) {
    fwrite(STDERR, "DB_HOST, DB_DATABASE or DB_USERNAME not set, skipping database bootstrap\n");
    exit(0);
}

$attempts = 0;
while ($attempts < 30) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [PDO::ATTR_TIMEOUT => 5]);

        if (getenv("DB_RESET_SCHEMA")) {
            $pdo->exec("DROP DATABASE IF EXISTS `{$db}`");
            echo "Database `{$db}` dropped for reset\n";
        }

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Database `{$db}` ready\n";

        $grantUser = getenv("DB_GRANT_USER");
        if ($grantUser) {
            $pdo->exec("GRANT ALL PRIVILEGES ON `{$db}`.* TO `{$grantUser}`@`%`");
            $pdo->exec("FLUSH PRIVILEGES");
            echo "Granted `{$db}` to `{$grantUser}`\n";
        }

        exit(0);
    } catch (PDOException $e) {
        $attempts++;
        fwrite(STDERR, "Waiting for database... ({$attempts}/30) " . $e->getMessage() . "\n");
        sleep(2);
    }
}

fwrite(STDERR, "Could not reach database after 30 attempts\n");
exit(1);
'

php artisan config:clear
php artisan migrate --force

if [ "$SEED_DATABASE" = "true" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

exec "$@"
