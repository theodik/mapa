<?php

$router->map('GET', '/', 'home#index', 'root');

$router->map('GET', '/admin/', 'admin#index', 'admin_index');
$router->map('GET', '/admin/login', 'admin#login', 'admin_login');
$router->map('POST', '/admin/login', 'admin#do_login');

