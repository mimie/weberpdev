<?php

// User configurable variables
//---------------------------------------------------

//DefaultLanguage to use for the login screen and the setup of new users - the users language selection will override
$DefaultLanguage ='en_GB.utf8';

// Whether to display the demo login and password or not on the login screen
$allow_demo_mode = False;

//  Connection information for the database
// $host is the computer ip address or name where the database is located
// assuming that the web server is also the sql server
//$host = '10.110.215.92';
$host = 'localhost';

// assuming that the web server is also the sql server
$dbType = 'mysql';
// assuming that the web server is also the sql server
$dbuser = 'root';
// assuming that the web server is also the sql server
$dbpassword = 'mysqladmin';
// The timezone of the business - this allows the possibility of having;
putenv('TZ=Asia/Manila');
$AllowCompanySelectionBox = true;
$DefaultCompany = 'IIAP_DEV';
$SessionLifeTime = 3600;
$MaximumExecutionTime =120;
//$SessionSavePath = '/mnt/html/weberp_sessions/weberpdev';
$SessionSavePath = '/var/www/weberp_sessions/weberpdev';
$CryptFunction = 'sha1';
$DefaultClock = 12;
$rootpath = dirname(htmlspecialchars($_SERVER['PHP_SELF']));
if (isset($DirectoryLevelsDeep)){
   for ($i=0;$i<$DirectoryLevelsDeep;$i++){
$rootpath = mb_substr($rootpath,0, strrpos($rootpath,'/'));
} }
if ($rootpath == '/' OR $rootpath == '\\') {;
$rootpath = '';
}
error_reporting (E_ALL & ~E_NOTICE);
?>
