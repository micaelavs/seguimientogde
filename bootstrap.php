<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . "/constantes.php";
require_once BASE_PATH . '/vendor/autoload.php';

define('APP_VERSION','1.0.0');

$config	= FMT\Configuracion::instancia();
$config->cargar(BASE_PATH . '/config');

if(!defined('PHP_INTERPRETE')){
    define('PHP_INTERPRETE', \FMT\Helper\Arr::get($config['app'], 'php_interprete', 'php74'));
}

// \FMT\Mailer::init($config['email']['app_mailer'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);
// \PHPMailer\PHPMailer\PHPMailer::init($config['email']['app_mailer'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);
//\FMT\Logger::init(empty($_SESSION['iu']) ? '1' : $_SESSION['iu'], $config['logs']['modulo'], $config['logs']['end_point_event'], $config['logs']['end_point_debug'], $config['logs']['debug']);
\FMT\Usuarios::init($config['app']['modulo'], $config['app']['endpoint_panel'].'/api.php', ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);
\App\Modelo\OrganigramaApi::init($config['app']['endpoint_organigrama']);
\App\Modelo\OrganigramaApi::setToken($config['app']['modulo'], $config['app']['organigrama_access_token']);
\FMT\Informacion_fecha::init($config['app']['endpoint_informacion_fecha'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);

// \FMT\Ubicaciones::init($config['app']['endpoint_ubicaciones'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);
// \FMT\Informacion_fecha::init($config['app']['endpoint_informacion_fecha'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);