dev-up:
	docker-compose -f docker-compose.development.yml up --build -d --force-recreate

dev-up-clean:
	docker-compose -f docker-compose.development.yml build --no-cache && docker-compose -f docker-compose.development.yml up -d --force-recreate
