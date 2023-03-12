cp .env.example .env &&
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs &&
./vendor/bin/sail up -d &&
echo 'Waiting for mysql...' &&
sleep 30 &&
./vendor/bin/sail artisan migrate:fresh --seed &&
./vendor/bin/sail artisan test
