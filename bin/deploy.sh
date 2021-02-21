#!/bin/bash

echo -e "\n\e[94m DEPLOY! \e[0m\n"

git checkout master
git pull origin master

cd infra/docker/prod
cp dist.env .env
docker-compose down
docker-compose up -d --build
cd ../../../

cp infra/env/.env.prod .env.local

docker exec toolbox-php composer dump-env prod
docker exec toolbox-php composer install --no-dev --optimize-autoloader
docker exec toolbox-php bin/console doctrine:schema:update --force
docker run -v $(pwd):/home/app toolbox-node yarn install
docker run -v $(pwd):/home/app toolbox-node yarn encore production

docker exec toolbox-php bin/console cache:clear
docker exec toolbox-php bin/console cache:warmup
