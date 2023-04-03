up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

php:
	docker compose exec -it php bash

lint:
	docker compose exec php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src --rules=@Symfony

node:
	docker run --rm -v $(PWD):/var/www/html -w /var/www/html -p 9000:9000 -u node -e NPM_CONFIG_UPDATE_NOTIFIER=false -it node:lts-slim bash