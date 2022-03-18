.SILENT:

PHP_CONTAINER=docker exec -it jirhub_php
PHP=$(PHP_CONTAINER) php
SF_CONSOLE=$(PHP) bin/console

test:
	vendor/bin/codecept run

test-unit:
	vendor/bin/codecept run unit

fix-cs:
	docker exec jirhub_php php vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix

cc:
	docker exec jirhub_php php bin/console cache:clear

cc-hard:
	docker exec -it jirhub_php bash -c "rm -rf ./var/cache/dev/*"

bash:
	docker exec -it jirhub_php sh

restart: stop start

start:
	docker-compose up -d

stop:
	docker-compose down

build:
	docker-compose build
	$(MAKE) start
	$(MAKE) install
	$(MAKE) stop

install:
	cmd="install --optimize-autoloader" $(MAKE) composer

update:
	cmd="update --optimize-autoloader" $(MAKE) composer

require:
	cmd="require $(pck)" $(MAKE) composer

composer:
	docker exec -it jirhub_php composer $(cmd)

console:
	$(SF_CONSOLE) $(cmd)

lint:
	docker exec jirhub_php vendor/bin/php-cs-fixer fix --using-cache=no --config=.php-cs-fixer.dist.php `git diff --name-status --diff-filter=AM ':!*.png' ':!*.pdf' ':!*.csv', ':!*.jpeg' | sed '/^D/d' | sed 's/^R.*\t\(.*\)\t.*/\1/g' | sed 's/^[M|A]\t\(.*\)/\1/g'`