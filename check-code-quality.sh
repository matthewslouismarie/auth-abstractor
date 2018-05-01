vendor/bin/phpunit --coverage-clover=clover.xml --coverage-xml=coverage/coverage-xml --log-junit=coverage/phpunit.junit.xml

if [ $? -ne 0 ]; then
    exit 1
fi

vendor/bin/infection --coverage=coverage --min-msi=70 --threads=6

if [ $? -ne 0 ]; then
    exit 1
fi

vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes

if [ $? -ne 0 ]; then
    exit 1
fi