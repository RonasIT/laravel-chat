<?php return array (
  'alymosul/laravel-exponent-push-notifications' => 
  array (
    'providers' => 
    array (
      0 => 'NotificationChannels\\ExpoPushNotifications\\ExpoPushNotificationsServiceProvider',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'php-open-source-saver/jwt-auth' => 
  array (
    'aliases' => 
    array (
      'JWTAuth' => 'PHPOpenSourceSaver\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'PHPOpenSourceSaver\\JWTAuth\\Facades\\JWTFactory',
    ),
    'providers' => 
    array (
      0 => 'PHPOpenSourceSaver\\JWTAuth\\Providers\\LaravelServiceProvider',
    ),
  ),
  'ronasit/laravel-helpers' => 
  array (
    'providers' => 
    array (
      0 => 'RonasIT\\Support\\HelpersServiceProvider',
    ),
  ),
  'ronasit/laravel-swagger' => 
  array (
    'providers' => 
    array (
      0 => 'RonasIT\\Support\\AutoDoc\\AutoDocServiceProvider',
    ),
  ),
);