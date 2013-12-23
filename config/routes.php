<?php

$router->map('GET', '/', 'home#index', 'root');
$router->map('GET', '/index', 'home#index');
$router->map('POST', '/login', 'home#login', 'login');
