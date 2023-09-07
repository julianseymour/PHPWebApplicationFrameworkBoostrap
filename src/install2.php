<?php

include_once 'html/header.php';

use JulianSeymour\PHPWebApplicationFramework\core\Debug;
use JulianSeymour\PHPWebApplicationFramework\error\ErrorMessage;
use JulianSeymour\PHPWebApplicationFramework\app\InstallFrameworkUseCase;

$install = new InstallFrameworkUseCase();
$status = $install->execute();
if($status !== SUCCESS){
	$f = __FILE__;
	$err = ErrorMessage::getResultMessage($status);
	Debug::warning("{$f} installation returned error status \"{$err}\"");
}
