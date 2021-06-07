#!/bin/bash

DOCKER_CONTAINER_PHP='toolbox-php'
DOCKER_CONTAINER_NODE='toolbox-node'

echo -e "\n\e[104m Qualitify \e[0m\n"

echo -e "\n\e[94m Checking dependencies... \e[0m\n"

array=( friendsofphp/php-cs-fixer squizlabs/php_codesniffer phpmd/phpmd phpstan/phpstan symfony/phpunit-bridge friendsoftwig/twigcs)
for i in "${array[@]}"
do
	composer show | grep "${i}"
	if [[ $? -eq 0 ]]
    then
        echo -e "\n\e[30;48;5;82m ${i} ready \e[0m\n"
    else
        composer require --dev "${i}"
    fi
done

echo -e "\n\e[94m PHP tools \e[0m\n"

echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP CS Fixer \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix src/ --rules=@PSR12 --diff --dry-run --using-cache=no

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP Code Sniffer \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP vendor/bin/phpcs src/

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP Mess Detector \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP vendor/bin/phpmd src/ text codesize,controversial,phpmd.xml

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP Stan \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP vendor/bin/phpstan analyse -c phpstan.neon

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHPUnit \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP bin/phpunit

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m Twig Code Sniffer \e[0m"
docker exec -it $DOCKER_CONTAINER_PHP vendor/bin/twigcs templates

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\n\e[93m JavaScript tools \e[0m\n"

echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[93m ESLint \e[0m"
docker run -it -v "$APP_PWD":/home/app $DOCKER_CONTAINER_NODE yarn run eslint assets/ --ext .js,.jsx,.ts,.tsx

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
