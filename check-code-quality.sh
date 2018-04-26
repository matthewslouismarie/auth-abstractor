vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes
vendor/bin/phpunit
vendor/bin/infection --min-covered-msi=70 --threads=4