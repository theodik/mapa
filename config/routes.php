<?php

$router->map('GET', '/', 'home#index', 'root');
$router->map('GET', '/[i:id]', 'home#index', 'show_server');

// Admin část

$router->map('GET', '/admin/', 'serveradmin#index', 'admin_index');

$router->map('GET', '/admin/login', 'sessionsadmin#new_session', 'admin_new_sessions');
$router->map('POST', '/admin/login', 'sessionsadmin#create_session');
$router->map('GET', '/admin/logout', 'sessionsadmin#delete_session', 'admin_delete_sessions');

// Administrace serverů
$router->map('GET', '/admin/servers/new', 'serveradmin#new_server', 'admin_new_server');
$router->map('POST', '/admin/servers', 'serveradmin#create_server', 'admin_create_server');

$router->map('GET', '/admin/servers/[i:id]/edit', 'serveradmin#edit_server', 'admin_edit_server');
$router->map('POST', '/admin/servers/[i:id]', 'serveradmin#update_server', 'admin_update_server');
$router->map('GET', '/admin/servers/[i:id]/delete', 'serveradmin#delete_server', 'admin_delete_server');

// Administrace uživatelů
$router->map('GET', '/admin/users', 'usersadmin#index', 'admin_index_users');
$router->map('GET', '/admin/users/new', 'usersadmin#new_user', 'admin_new_users');
$router->map('POST', '/admin/users', 'usersadmin#create_user', 'admin_create_users');

$router->map('GET', '/admin/users/[i:id]/edit', 'usersadmin#edit_user', 'admin_edit_users');
$router->map('POST', '/admin/users/[i:id]', 'usersadmin#update_user', 'admin_update_users');
$router->map('GET', '/admin/users/[i:id]/delete', 'usersadmin#delete_user', 'admin_delete_users');

