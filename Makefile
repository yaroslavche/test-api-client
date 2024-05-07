build:
	docker compose build
install:
	docker compose exec api_client_php composer install
up:
	docker compose up -d
down:
	docker compose down
rr:
	make down && make build && make up
phpcs:
	docker compose exec api_client_php vendor/bin/php-cs-fixer fix src --dry-run -v
phpstan:
	docker compose exec api_client_php vendor/bin/phpstan
