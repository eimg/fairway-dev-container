<?php
declare(strict_types=1);

$cfg['blowfish_secret'] = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';
$i = 1;
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

// Auto-login configuration for development convenience
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'root';

// Set absolute URI to current request URL for development
$cfg['PmaAbsoluteUri'] = 'http://' . $_SERVER['HTTP_HOST'] . '/phpmyadmin/';

// Disable warnings and checks for development
$cfg['PmaNoRelation_DisableWarning'] = true;
$cfg['CheckConfigurationPermissions'] = false; 
