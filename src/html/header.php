<?php

$f = "header.php";

$print = false;
$__clock = false;
if($__clock){
	$__mark = microtime(true);
	$__startmicro = $__mark;
}
$__START = time();
error_reporting(-1);

$pid = getmypid();
if(!isset($_GET['MEMPROF_PROFILE']) && !isset($_POST['MEMPROF_PROFILE'])){
	//memprof_enable();
}
//ini_set("session.cookie_samesite", "Strict");
//ini_set('display_errors', 'On');
//set_error_handler("var_dump");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//set document root for installation only
	$parsed = [];
	global $argv;
	$install = false;
	if(isset($argv)){
		parse_str($argv[1], $parsed);
		if(isset($parsed['install_password'])){
			$install = true;
		}
	}
	if($install){
		$_SERVER['DOCUMENT_ROOT'] = getcwd()."/html";
	}
	if(!defined("FRAMEWORK_INSTALL_DIRECTORY")){
		define("FRAMEWORK_INSTALL_DIRECTORY", $_SERVER['DOCUMENT_ROOT']."/../vendor/julianseymour/php-web-application-framework/src");
	}
//static files needed by the framework
	require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/../vapid.php";
	require_once $_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php';
	if(!$install){
		require_once $_SERVER['DOCUMENT_ROOT']."/block.php";
	}
	use JulianSeymour\PHPWebApplicationFramework\core\Debug;
//set locale
	if(!$install){
		if(defined('LC_MESSAGES')){
			if($print){
				Debug::print("{$f} LC_MESSAGES is defined as \"".LC_MESSAGES."\"");
			}
			if(is_array($_COOKIE) && array_key_exists("cookieRegionCode", $_COOKIE)){
				$region = $_COOKIE["cookieRegionCode"];
				if($print){
					Debug::print("{$f} got region code \"{$region}\" from cookies");
				}
			}else{
				$region = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
				if($print){
					Debug::print("{$f} got region code \"{$region}\" from IP address");
				}
			}
			if(is_array($_COOKIE) && array_key_exists("cookieLanguageCode", $_COOKIE)){
				$lang = $_COOKIE["cookieLanguageCode"];
				if($print){
					Debug::print("{$f} got language code \"{$lang}\" from cookies");
				}
			}else{
				$lang = \JulianSeymour\PHPWebApplicationFramework\default_lang_region($region);
				if($print){
					Debug::print("{$f} got language code \"{$lang}\" from region");
				}
			}
			$locale = "{$lang}_{$region}";
			$set = setlocale(LC_MESSAGES, $locale, "{$locale}.utf8", "{$locale}.UTF8", $lang);
			if(false === $set){
				Debug::error("{$f} setting locale failed");
			}elseif($print){
				Debug::print("{$f} successfully set locale to \"{$locale}\"");
			}
			$bound = bindtextdomain("messages", "/var/www/locale");
			if(false === $bound){
				Debug::error("{$f} failed to bind text domain");
			}elseif($print){
				Debug::print("{$f} successfully bound text domain to \"{$bound}\"");
			}
		}elseif($print){
			Debug::print("{$f} LC_MESSAGES is undefined");
		}
		textdomain("messages");
	}
	if($__clock){
		Debug::print("{$f} started process {$pid} at {$__mark}");
	}
//application configuration class
	if(!defined("APPLICATION_CONFIG_CLASS_NAME")){
		Debug::error("{$f} application class name is undefined");
	}
	$acn = APPLICATION_CONFIG_CLASS_NAME;
	if($print){
		Debug::print("{$f} application class name is \"{$acn}\"");
	}
//instantiate ApplicationConfig
	$__applicationConfig = new $acn();
//instantiate applicationInstance
	$__applicationInstance = new \JulianSeymour\PHPWebApplicationFramework\app\ApplicationRuntime($__applicationConfig);
	if(defined("DEBUG_MODE") && DEBUG_MODE){
		$__applicationInstance->setFlag("debug", true);
	}
	if($install){
		$__applicationInstance->setFlag("install", true);
	}
	global $mysqli;
	if(isset($mysqli) && $mysqli instanceof mysqli){
		if($print){
			Debug::print("{$f} mysqli is set, handing it over to database connection manager");
		}
		$__applicationInstance->getDatabaseManager()->setConnection($mysqli);
	}elseif($print){
		Debug::print("{$f} mysqli is not set");
	}
//instantiate module bundler XXX TODO ModuleBundler class can be overridden
	$abc = $__applicationConfig->getModuleBundlerClass();
	$bundler = new $abc();
	$bundler->load($__applicationConfig->getModuleClasses());
	$__applicationInstance->setModuleBundler($bundler);
//include generic PHP files
	foreach($bundler->getPhpFileInclusionPaths() as $fn){
		require_once $fn;
	}
	