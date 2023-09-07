#!/bin/bash

#1. end script if user is not root (it won't work anyway if they aren't)
if [ "$(id -u)" -ne 0 ]; then
	echo 'This script must be run by root' >&2
	exit 1
fi

#2. read admin username/password/email address
read -p "Enter administrator username: " username
read -p "Enter administrator password: " password
read -p "Enter administrator email address: " email

#3. create database user for installation
install_password=`cat /dev/urandom | base64 | head -n 1`
echo "Install password is $install_password"
mysql --execute="create user 'installer'@'localhost' identified by '$install_password'; grant select,usage,create,insert,update,delete,references,alter,drop,super,create routine,alter routine,execute,create user,file,grant option on *.* to 'installer'@'localhost'; flush privileges;"

#4. create vapid public and private keys for push notifications
openssl ecparam -genkey -name prime256v1 -out private_key.pem
public=`openssl ec -in private_key.pem -pubout -outform DER|tail -c 65|base64|tr -d '=' |tr '/+' '_-'`
public="${public//[$'\t\r\n ']}"
private=`openssl ec -in private_key.pem -outform DER|tail -c +8|head -c 32|base64|tr -d '=' |tr '/+' '_-'`
private="${private//[$'\t\r\n ']}"
reader=`tr -dc 'A-Za-z0-9!"#$%&()*+,-./:;<=>?@[\]^_{|}~' < /dev/urandom | head -c 32 ; echo`
reader="${reader//[$'\t\r\n ']}"
writer=`tr -dc 'A-Za-z0-9!"#$%&()*+,-./:;<=>?@[\]^_{|}~' < /dev/urandom | head -c 32 ; echo`
writer="${writer//[$'\t\r\n ']}"
echo "<?php " > /var/www/vapid.php
echo "define('PUSH_API_SERVER_PUBLIC_KEY', '$public');" >> /var/www/vapid.php
echo "define('VAPID_PRIVATE_KEY', '$private');" >> /var/www/vapid.php
echo "define('PUBLIC_READER_PASSWORD', '$reader');" >> /var/www/vapid.php
echo "define('PUBLIC_WRITER_PASSWORD', '$writer');" >> /var/www/vapid.php 
echo " ?>" >> /var/www/vapid.php
rm private_key.pem

#5. the rest gets executed by install2.php
install_password=`echo $install_password | tr -d '\n' | sed 's/+/_/g'`
install_password="${install_password//[$'\t\r\n ']}"
username=`echo $username | tr -d '\n' | base64 | sed 's/+/_/g'`
username="${username//[$'\t\r\n ']}"
password=`echo $password | tr -d '\n' | base64 | sed 's/+/_/g'`
password="${password//[$'\t\r\n ']}"
email=`echo $email | tr -d '\n' | base64 | sed 's/+/_/g'`
email="${email//[$'\t\r\n ']}"
php install2.php "install_password=$install_password&username=$username&password=$password&email=$email"
