<?php

define("WEBSITE_DOMAIN", "example.com");

define("ADMIN_NAME", "admin");
define("WEBSITE_NAME", "Example Site");
define("WEBSITE_NAME_PASCALCASE", "ExampleSite");
define("DOMAIN_CAMELCASE", "example.com");
define("DOMAIN_BASE", "example");
define("DOMAIN_TLX", "com");
define("HOST_DIRECTORY", null);
define("SERVER_PUBLIC_IP_ADDRESS", "123.456.69.420");

define("MESSAGE_LIMIT", 50);
define("MAX_FAILED_LOGINS_BY_NAME", 5);
define("MAX_FAILED_LOGINS_BY_IP", 6);
define("FILE_SIZE_LIMIT", 5000000);

define("MFA_OTP_LENGTH", 6);
define("MFA_KEYGEN_INTERVAL", 30);

define("MINIMUM_PASSWORD_LENGTH", 12);

define("IMAGE_MAX_DIMENSION", 1920);

define("HCAPTCHA_SITE_KEY", "some-uuid");
define("HCAPTCHA_SECRET", "big-hex-number");

define("APPLICATION_CONFIG_CLASS_NAME", "ExampleApplicationConfig");

define(
	"WEBSITE_LOGO_URI_HORIZONTAL_DARK", 
	HOST_DIRECTORY."/images/example1.png"
);
define(
	"WEBSITE_LOGO_URI_HORIZONTAL_LIGHT", 
	HOST_DIRECTORY."/images/example2.png"
);
define(
	"WEBSITE_LOGO_URI_VERTICAL_DARK", 
	HOST_DIRECTORY."/images/example3.png"
);
define(
	"WEBSITE_LOGO_URI_VERTICAL_LIGHT", 
	HOST_DIRECTORY."/images/example4.png"
);

define("WEBSITE_LOGO_URI_BADGE_DARK", HOST_DIRECTORY."/images/example5.png");

define("ULTRA_LAZY", true);

define("CACHE_ENABLED", true);
define("FILE_CACHE_ENABLED", true);
define("HTML_CACHE_ENABLED", false);
define("QUERY_CACHE_ENABLED", true);
define("JAVASCRIPT_CACHE_ENABLED", false);
define("CSS_CACHE_ENABLED", false);
define("JSON_CACHE_ENABLED", false);
define("USER_CACHE_ENABLED", true);

define("CUSTOM_ROLE_PREFIX", "}%");

define("SECURE_FILE_PRIV", "/var/lib/mysql-files/");

define("UNDECLARED_FLAGS_ENABLED", true);

define("CONTACT_EMAIL_ADDRESS", "contact@".WEBSITE_DOMAIN);

define("LANGUAGE_DEFAULT", LANGUAGE_ENGLISH);
define("LOCALE_DEFAULT", "en_US");
