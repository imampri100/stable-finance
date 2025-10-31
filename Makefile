dev-up:
	docker-compose -f development.docker-compose.yml up --build -d --force-recreate

dev-up-clean:
	docker-compose -f development.docker-compose.yml build --no-cache && docker-compose -f development.docker-compose.yml up -d --force-recreate

dev-down:
	docker-compose -f development.docker-compose.yml down

dev-down-clean:
	docker-compose -f development.docker-compose.yml down -v

deploy-up-clean:
	docker-compose -f deploy.docker-compose.yml build --no-cache && docker-compose -f deploy.docker-compose.yml up -d --force-recreate

deploy-down:
	docker-compose -f deploy.docker-compose.yml down

deploy-down-clean:
	docker-compose -f deploy.docker-compose.yml down -v