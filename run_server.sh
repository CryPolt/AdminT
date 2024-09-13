#!/bin/bash

# Убедитесь, что PHP установлен
if ! command -v php &> /dev/null
then
    echo "PHP не установлен. Пожалуйста, установите PHP и попробуйте снова."
    exit 1
fi

# Запустите сервер PHP
php server.php
