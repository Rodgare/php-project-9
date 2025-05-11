PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
lint:
	composer exec ./vendor/bin/phpcs -- --standard=PSR12 src/ public/
fix:
	composer exec ./vendor/bin/phpcbf -- --standard=PSR12 src/ public/
stan:
	vendor/bin/phpstan --memory-limit=256M --ansi analyse src/ public/