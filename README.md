# PHPWebApplicationFrameworkBoostrap
This repository contains files necessary for installing and running a server with Julian Seymour's PHP web application framework. Copy the contents of src into /var/www.
src/html/header.php:
	This file includes configuration files and instantiates global variables necessary for the framework to function. If you make an alternate entry point into your application, it should include header.php at the top.
src/html/block.php:
	Verifies that the incoming request is not coming from a blocked IP address, and quickly exits if it is disallowed.
src/html/index.php:
	This file serves as the main entry point to the application, intercepting all incoming requests (through apache mod_rewrite rules, which you will have to write yourself).
src/config (example).php is an example of how to write config.php. You will need to write your own version of config.php and place it in /var/www for your application to work.
src/install.sh:
	This is the main install script. Run it as sudo and it will prompt you to enter the insall administrator's username, password and email address.
src/install2.php
	install.sh passes arguments to this file in order to continue the installation sequence. You should not run this script by hand.
  
