<?php
/**
 * Created by PhpStorm.
 * User: p17204754
 * Date: 06/11/2019
 * Time: 14:49
 */

ini_set('display_errors', 'On');
ini_set('html_errors', 'On');
ini_set('xdebug.trace_output_name', 'dev_project.%t');
ini_set('xdebug.trace_format', '1');

define('DIRSEP', DIRECTORY_SEPARATOR);

$app_url = dirname($_SERVER['SCRIPT_NAME']);
$css_path = $app_url . '/css/stylesheet.css';

$log_path = '/home/p17204754/phpappfolder/';
$log_name = 'dev.log';
$log_file = $log_path . $log_name;

define('CSS_PATH', $css_path);
define('LOG_FILE', $log_file);
define('APP_NAME', 'dev_project');
define('LANDING_PAGE', $_SERVER['SCRIPT_NAME']);
define('ADMIN', 'adminInterface' );
define('DOWNLOAD', 'retrieve' );
define('BOOK', 'book' );


define ('BCRYPT_ALGO', PASSWORD_DEFAULT);
define ('BCRYPT_COST', 12);


$option_types = ['on', 'off', 'forward', 'reverse'];
$user_types = ['user', 'admin'];
define('OPTION_TYPES', $option_types);
define('USER_TYPES', $user_types);


$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'development',
        'debug' => true,
        'class_path' => __DIR__ . '/src/',
        'view' => [
            'template_path' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
            ]],
    ],
    'doctrine_settings' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'dev_db',
        'port' => '3306',
        'user' => 'dev_user',
        'password' => 'dev_pass',
        'charset' => 'utf8mb4'
    ],
];

return $settings;