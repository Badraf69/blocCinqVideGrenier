#!/bin/bash
# Script pour lancer les tests PHPUnit sous Linux/Mac
cd "$(dirname "$0")/.."
if [ -f vendor/bin/phpunit ]; then
    vendor/bin/phpunit --testdox tests
else
    echo "PHPUnit n'est pas installé. Installation..."
    composer require --dev phpunit/phpunit
    vendor/bin/phpunit --testdox tests
fi
