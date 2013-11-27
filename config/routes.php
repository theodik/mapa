<?php

$router->map('GET', '/', 'home#index', 'root');
$router->map('POST', '/login', 'home#login', 'login');
