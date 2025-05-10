PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
lint:
	phpcs -- --standard=PSR12 public/ src/ templates/ && phpcbf -- --standard=PSR12 public/ src/ templates/