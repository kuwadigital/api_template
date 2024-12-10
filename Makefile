# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php-fpm

# Executables
PHP      = $(PHP_CONT) php
CONSOLE  = @$(PHP) bin/console
PHPUNIT  = @$(PHP) bin/phpunit
COMPOSER = $(PHP_CONT) composer

# Misc
.DEFAULT_GOAL = help

##
##—————————————————————————————— The Symfony Docker Makefile
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##—————————————————————————————— App
app-build: ## Install App
	@$(DOCKER_COMP) build --pull #--no-cache
	@$(DOCKER_COMP) up --detach
	@$(COMPOSER) install

app-shell: ## Bash
	@$(PHP_CONT) bash

app-composer: ## Run composer command (Example: make composer c='req symfony/uid')
	@$(COMPOSER) $(c)

app-test: ## Run all tests (Example specify: make test c=tests/Functional)
	@$(PHPUNIT) $(c)

app-console: ## Run console (Example: make console c=make:controller)
	@$(CONSOLE) $(c)

app-clean: ## Clear App cache (env=dev)
	@$(CONSOLE) cache:clear --env=dev

app-route: ## Lists all your application routes
	@$(CONSOLE) debug:route

##—————————————————————————————— Docker
docker-restart: stop start ## Restart the Docker containers (stop start)
docker-rebuild: down build up ## Rebuild the Docker containers (down build up)

docker-build: ## Build Docker images
	@$(DOCKER_COMP) build

docker-up: ## Start Docker containers in detached mode
	@$(DOCKER_COMP) up --detach

docker-down: ## Stop and remove Docker containers and orphaned volumes
	@$(DOCKER_COMP) down --remove-orphans

docker-start: ## Start Docker containers
	@${DOCKER_COMP} start

docker-stop: ## Stop Docker containers
	@${DOCKER_COMP} stop

##—————————————————————————————— Static code analysis of our system
sys-phpstan: ## Run the static analysis of code.
	@${PHP_CONT} vendor/bin/phpstan analyse -c phpstan.neon
	@${PHP_CONT} vendor/bin/phpstan clear-result-cache

sys-cs-diff: ## Show coding standards problems (without making changes)
	@${PHP_CONT} vendor/bin/php-cs-fixer fix --dry-run --diff

sys-cs-fix: ## Fix as much coding standards problems
	@${PHP_CONT} vendor/bin/php-cs-fixer fix

sys-composer-validate: ## Validate your composer.json file
	${PHP_CONT} composer validate
