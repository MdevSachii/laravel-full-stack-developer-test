#!/usr/bin/make

SHELL = /bin/sh

UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

shell:
	docker exec -it test-app-container bash -c "sudo -u app-user /bin/bash"

up:
	UID=${UID} GID=${GID} docker-compose -f docker-compose.yml --env-file ./docker-configs/.env --profile main up --build -d --remove-orphans

down:
	docker-compose -f docker-compose.yml --profile main down --remove-orphans
