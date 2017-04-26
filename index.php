<?php

/**
* PHP Coupé
*
* Desenvolvimento PHP rápido e reaproveitável.
*
* @package PHP Coupe
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @since 04/05/2012
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @link http://www.samuelcorradi.net/phpcoupe
*/

/*
* O PHP Coupé suporta apenas a versão 5 do PHP.
*/
if( version_compare(PHP_VERSION, "5.0")<0 )
{
	trigger_error("PHP Coupé only works with PHP 5.0 or newer", E_USER_ERROR);
}

/*
* Setando o timezone.
*/
date_default_timezone_set('UTC');

/*
* Pasta com os arquivos do framework.
*/
$system_folder = 'phpcoupe';

/*
* Pasta onde está armazenado os arquivo da aplicação.
*/
$app_folder = 'app';

/*
* Pasta de acesso público.
* Geralmente arquvios incluídos nas visões (imagens, scripts, CSS, etc.)
*/
$public_folder = 'public';

/*
 * DENIFIÇÃO DE CONSTANTES
 */

/*
*  Versão atual do PHP Coupé
*/
define("COUPE", "0.7");

/*
* Nome do aplicativo é igual ao nome da sua pasta
*/
define('APPNAME', ucfirst($app_folder));

/*
* Define a barra de diretorios ('\'=>Win | '/'=>Unix like)
*/
define('DS', DIRECTORY_SEPARATOR);

/*
* Nome do arquivo base do programa (normalmente index.php)
*/
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

/*
* Caminho do programa no servidor web
*/
define('BASE_FOLDER', str_replace(DS . SELF, '', __FILE__) . DS);

/*
* Dominio onde o framework está rodando
*/
define('DOMAIN', "http://" . $_SERVER["HTTP_HOST"]);

/*
* Caminho para o framework no domínio (pode estar em um subdiretório)
*/
define('BASE_URL', DOMAIN . str_replace('/' . SELF, '', $_SERVER["PHP_SELF"]) );

/*
* Caminho para os arquivos do núcleo do framework
*/
define('WEBROOT', BASE_URL . '/' . APPNAME . '/webroot/'); // define('WEBROOT', APP . 'webroot' . DS);

/*
 * Base do caminho na URL. Mostra o caminho de pastas do programa na URL.
 */
define('BASE_PATH', str_replace('/' . SELF, '', $_SERVER["PHP_SELF"]) );

/*
* Caminho para a pasta com codigos base do framework
*/
define('BASE', BASE_FOLDER . $system_folder . DS);

/*
* Caminho para os arquivos do núcleo do framework
*/
define('CORE', BASE . 'core' . DS);

/*
* Caminho para a pasta com a aplicacao feita sobre o framework
*/
define('APP', BASE_FOLDER . $app_folder . DS);

/*
* Extensão dos arquivos (normalmente 'php')
*/
define('EXT', 'php');

/*
* ADICIONANDO OS ARQUIVOS BÁSICOS
*/

require_once CORE . 'Habilis' . DS . 'Config.php';

require_once CORE . 'Habilis' . DS . 'Idiom.php';

require_once CORE . 'Habilis' . DS . 'Dictionary.php';

require_once CORE . 'Habilis' . DS . 'Mapper.php';

require_once CORE . 'Coupe' . DS . 'Route.php';

require_once CORE . 'Habilis' . DS . 'Url.php';

require_once CORE . 'Coupe' . DS . 'View.php';

require_once CORE . 'Coupe' . DS . 'View' . DS . 'Template.php';

require_once CORE . 'Coupe' . DS . 'Model.php';

require_once APP . 'models' . DS . 'App.php';

require_once CORE . 'Habilis' . DS . 'Log.php';

require_once CORE . 'Coupe' . DS . 'Controller.php';

require_once CORE . 'Coupe.php';

/*
* DISPARA A EXECUÇÃO
*/

$coupe = \Coupe::getInstance();

$coupe->dispatcher();

// echo 'DS:' . DS . '<br />SELF: ' . SELF . '<br />BASE_PATH: ' . BASE_PATH . '<br />DOMAIN: ' . DOMAIN . '<br />BASE_URL: ' . BASE_URL . '<br />CORE: ' . CORE . '<br />APP: ' . APP . '<br />';
