# Toolbox

App with some tools and experiments.

Stack:
- Symfony 5.2
- PHP 7.4 + mariaDB + nginx
- MailCatcher (for development)
- GitHub Actions for CI/CD purposes

In this project is used:
- Symfony UX (Stimulus) + Webpack Encore in the frontend

## Requirements

Please make sure you have the following software installed. If not, please, install them:

* [Docker](https://docs.docker.com/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Docker

Create .env file for Docker and modify it in case you need it:
```bash
cd infra/docker/local
cp dist.env .env
```

Build and start containers:
```bash
docker-compose build
docker-compose up -d
```

Build node container:
```bash
docker build -t toolbox-node node/ --no-cache
```

## Database

Let's create the database for this project:
```bash
docker exec -t toolbox-db mysql -e "CREATE DATABASE IF NOT EXISTS toolbox"
docker exec -t toolbox-db mysql -e "GRANT ALL ON toolbox.* TO 'toolbox'@'%' IDENTIFIED BY 'toolbox'"
```

## App

Create Symfony .env file for your local environment:
```bash
cd </project/root>
cp .env .env.local
```

Install vendors via composer
```bash
docker exec -it toolbox-php composer install
```

Install node dependencies
```bash
cd </project/root>
docker run -it -v $(pwd):/home/app toolbox-node yarn install
```

To access node container
```bash
docker run -it -v $(pwd):/home/app toolbox-node bash
```

Create database schema
```bash
docker exec -it toolbox-php bin/console doctrine:schema:create
```

Update your /etc/host file adding the following entry
```bash
127.0.0.1       toolbox.loc
```

## Assets 

Build assets
```bash
docker run -it -v $(pwd):/home/app toolbox-node yarn encore dev
```

Watch mode
```bash
docker run -it -v $(pwd):/home/app toolbox-node yarn encore dev --watch
```

## Code quality
```bash
docker exec -it toolbox-php bin/qualitify.sh
```


#### That's all!
 
Open your browser and go to:
* http://toolbox.loc
