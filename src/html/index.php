<?php

require_once 'header.php';

use function JulianSeymour\PHPWebApplicationFramework\mark;
use function JulianSeymour\PHPWebApplicationFramework\starts_with;
use JulianSeymour\PHPWebApplicationFramework\app\Request;
use JulianSeymour\PHPWebApplicationFramework\app\workflow\SimpleWorkflow;
use JulianSeymour\PHPWebApplicationFramework\core\Debug;
use JulianSeymour\PHPWebApplicationFramework\error\ErrorMessage;
use JulianSeymour\PHPWebApplicationFramework\security\condemn\CondemnIpAddressUseCase;
use JulianSeymour\PHPWebApplicationFramework\use_case\Router;

$f = "index.php";
$print = false;
//split request URI into segments at /
	$request = new Request();
	$__applicationInstance->setRequest($request);
//filter simple URL honeypot in POST
	$condemn = false;
	if(!empty($_POST) && array_key_exists("url", $_POST) && !empty($_POST['url'])){
		Debug::warning("{$f} user fell for the URL honeypot in POST");
		$condemn = true;
		$request->setRequestURISegments($request->getRawRequestURISegments());
	}else{
		if($print){
			Debug::print("{$f} about to rewrite GET parameters");
		}
		$request->rewriteGetParameters();
		//filter simple URL honeypot in GET
			if(array_key_exists("url", $_GET) && !empty($_GET['url'])){
				Debug::warning("{$f} user fell for the URL honeypot in GET");
				exit();
				$condemn = true;
			}
	}
//get action attribute
	$action = $request->getAction(); //do not call this before rewriting GET parameters
//condemn the user's IP address is applicable
	if($condemn){
		Debug::warning("User fell for the honeypot!");
		include '/security/condemn'."/CondemnIpAddressUseCase.php";
		$use_case = new CondemnIpAddressUseCase();
		$use_case->setAction($_SERVER['REQUEST_URI']);
		$use_case->setReasonLogged(BECAUSE_HONEYPOT_SIMPLE);
		$workflow = new SimpleWorkflow();
		$workflow->handleRequest($request, $use_case);
		exit();
	}
//get use case class
	$bundler = $__applicationInstance->getModuleBundler();
	$use_case_class = $bundler->getUseCaseFromAction($action);
	if($print){
		Debug::print("{$f} use case class is \"{$use_case_class}\"");
	}
	if($__clock){
		mark("Before instantiating use case");
	}
//instantiate use case
	$use_case = new $use_case_class();
//deal with routers
	if($use_case instanceof Router){
		$use_case = $use_case->getUseCase($request);
	}else{
		$print = $use_case_class::debugUseCase();
	}
//block cross-origin requests unless otherwise allowed
	$origin = Request::getOriginHeader();
	if($origin !== $_SERVER['REMOTE_ADDR']){
		$parsed = parse_url($origin);
		$host = $parsed['host'];
		if(starts_with($host, "www.")){
			$host = substr($host, 4);
			if($print){
				Debug::print("{$f} stripped www. from host for new origin string \"{$host}\"");
			}
		}
		if($host !== WEBSITE_DOMAIN){
			$status = $use_case->validateCrossOriginRequest($parsed);
			if($status !== SUCCESS){
				$err = ErrorMessage::getResultMessage($status);
				Debug::warning("{$f} cross origin request validaton for host \"{$parsed['host']}\" failed with error message \"{$err}\"");
				$use_case->blockCrossOriginRequest();
			}elseif($print){
				Debug::print("{$f} use case \"{$use_case_class}\" approved CORS request for origin \"{$origin}\"");
			}
		}elseif($print){
			Debug::print("{$f} request origin is this website's domain");
		}
	}elseif($print){
		Debug::print("{$f} request origin is the user's IP address");
	}
//handle request
	$print = false;
	if($print){
		Debug::print("{$f} executing {$use_case_class} in process {$pid}");
	}
	$workflow_class = $use_case->getDefaultWorkflowClass();
	$workflow = new $workflow_class();
	$__applicationInstance->setWorkflow($workflow);
	if($print){
		$random = sha1(random_bytes(32));
	}
	$workflow->handleRequest($request, $use_case);
	if($print){
		Debug::print("{$f} finished handling request. Random string is \"{$random}\"");
	}
	if($__clock){
		mark("Termination");
		$__endmicro = microtime(true);
		$time = $__endmicro - $__startmicro;
		Debug::print("Finished handling request for {$_SERVER['REQUEST_URI']} in {$time} seconds; exiting process {$pid}");
	}
	exit();
