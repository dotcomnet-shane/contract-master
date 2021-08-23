<?php
/**
 * Description: This file is used to set global constants, variables and settings.
 *              This file is included in all pages throughout the framework.
 */

session_start();

//Set time started into $_SESSION
if(!isset($_SESSION['timeStart']))
{
    $_SESSION['timeStart'] = microtime(true);
}

/*Define constants*/

//Dev or live
if(isset($_SERVER['SERVER_NAME']) && (strpos($_SERVER['SERVER_NAME'], '.local') || strpos($_SERVER['SERVER_NAME'], 'localhost')))
{
    define('LIVE', false);
    define('DEBUG', true);

    //debug stuff
    ini_set('display_errors', 1);
    echo '<pre>';
}
else
{
    define('LIVE', true);
    define('DEBUG', false);
}

//Site
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
{
    define('PROTOCOL', 'https://');
}
else
{
    define('PROTOCOL', 'http://');
}

if(isset($_SERVER['SERVER_NAME']))
{
    if($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443)
    {
        define('URL', PROTOCOL . $_SERVER['SERVER_NAME'] . '/');
    }
    else
    {
        define('URL', PROTOCOL . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/');
    }
}
else
{ //CGI Support
    define('URL', PROTOCOL . $_SERVER['HTTP_HOST'] . substr($_SERVER['DOCUMENT_ROOT'], strrpos($_SERVER['DOCUMENT_ROOT'], '/')) . '/');
}


const SITE_NAME = 'Property.CoZa DocuSigner';

//File paths
if(isset($_SERVER['CONTEXT_DOCUMENT_ROOT']))
{
    define('ROOT_PATH', $_SERVER['CONTEXT_DOCUMENT_ROOT']);
}
else
{
    define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
}

const DISK_LOCATION = ROOT_PATH . '/';

const IMAGE_PATH = URL . 'assets/images/';

const PAGE_TITLE = 'Property.CoZa DocuSigner'; //Default page title

//Database Configuration
$databaseConfigurations = array();
$databaseConfigurations[] = array('username' => 'root', '' => 'onl1ne', 'address' => '127.0.0.1', 'database' => 'docusign', 'port' => '3306',);


/*Autoload classes*/
function classes_autoload($class_name)
{
    if(is_readable(DISK_LOCATION . 'classes/class.' . $class_name . '.php'))
    {
        require_once(DISK_LOCATION . 'classes/class.' . $class_name . '.php');
    }
}

function router_autoload($class_name)
{
    $newClassName = substr($class_name, strrpos($class_name, '_') + 1);

    if(is_readable(DISK_LOCATION . 'route/router.' . $newClassName . '.php'))
    {
        require_once(DISK_LOCATION . 'route/router.' . $newClassName . '.php');
    }
}

spl_autoload_register('classes_autoload');
spl_autoload_register('router_autoload');

/*Miscellaneous settings*/
//Timezone
date_default_timezone_set("Africa/Khartoum");

//Connect to Databases
//foreach($databaseConfigurations as $configurations)
//{
//    global ${'db_' . $configurations['database']};
//    ${'db_' . $configurations['database']} = new database($configurations);
//}