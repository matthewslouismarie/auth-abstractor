vendor/bin/phpunit --coverage-clover=clover.xml --coverage-xml=coverage/coverage-xml --log-junit=coverage/phpunit.junit.xml
vendor/bin/infection --coverage=coverage --min-covered-msi=70 --threads=4
vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes