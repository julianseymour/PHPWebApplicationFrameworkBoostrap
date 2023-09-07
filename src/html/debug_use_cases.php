<?php

require_once "header.php";

use function JulianSeymour\PHPWebApplicationFramework\ends_with;
use function JulianSeymour\PHPWebApplicationFramework\app;
use JulianSeymour\PHPWebApplicationFramework\core\Debug;

$f = __METHOD__; //"debug_use_cases.php";
$print = false;

app()->setRequest(new \JulianSeymour\PHPWebApplicationFramework\app\Request());

$auth = new \JulianSeymour\PHPWebApplicationFramework\auth\AuthenticateUseCase();
$auth->execute();
$auth->dispose();

$count = 0;
foreach($bundler->getUseCaseDictionary() as $ucc){
	$count++;
	if(!ends_with($ucc, "UseCase") && !ends_with($ucc, "Router")){
		Debug::warning("{$f} {$ucc} should end with UseCase");
	}elseif($print){
		Debug::print("{$f} about to include {$path}");
		echo "<div>{$count}. {$ucc}</div><br>";
	}
	if(!class_exists($ucc)){
		Debug::warning("{$f} class \"{$ucc}\" does not exist");
	}
	$use_case = new $ucc();
	$generator_class = $use_case->getLoadoutGeneratorClass(null);
	if($generator_class){
		$generator = new $generator_class();
	}
}
Debug::print("{$f} successfully included {$count} use case files");
echo "SUCCESS";