<?php
/* $Id: save.php 5681 2012-09-25 09:27:36Z daintree $*/
error_reporting(E_ALL && ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('max_execution_time', '580');
/*Klaus Opto advised that timeout issues made it difficult to install even though max_execution_time has been set - Tim though set_time_limit should also be set - it might be a windows thing - Klaus was using WAMP */
set_time_limit(580); 

require_once('../includes/MiscFunctions.php');
// Start a session
if(!defined('SESSION_STARTED')){
	session_name('ba_session_id');
	session_start();
	define('SESSION_STARTED', true);
}

if(!isset($_SESSION['MaxLogoSize'])) {
    $_SESSION['MaxLogoSize'] = 10 * 1024;       // Limit logo file size.
}
//deal with check boxes
if(!isset($_POST['install_tables'])) {
	$_POST['install_tables'] = false;
} else {
	$_POST['install_tables'] = true;
}
if(!isset($_POST['DemoData'])) {
	$_POST['DemoData'] = false;
} else {
	$_POST['DemoData'] = true;
}

function Replace_Dodgy_Characters ($DodgyString) {
	$CleanString = str_replace("'",'',$DodgyString);
	$CleanString = str_replace('"','',$CleanString);
	$CleanString = str_replace(' ','_',$CleanString);
	$CleanString = str_replace(',','_',$CleanString);
	$CleanString = str_replace('-','_',$CleanString);
	$CleanString = str_replace("\\",'_',$CleanString);
	$CleanString = str_replace('/','_',$CleanString);
	$CleanString = str_replace('?','_',$CleanString);
	return $CleanString;
}

function set_error($message) {

	if(isset($message) AND $message != '') {
		// Copy values entered into session so user doesn't have to re-enter everything
		if(isset($_POST['company_name'])) {
			$_SESSION['ba_url'] = $_POST['ba_url'];
			if(!isset($_POST['operating_system'])) {
				$_SESSION['operating_system'] = 'linux';
			} else {
				$_SESSION['operating_system'] = $_POST['operating_system'];
			}
			if(!isset($_POST['world_writeable'])) {
				$_SESSION['world_writeable'] = false;
			} else {
				$_SESSION['world_writeable'] = true;
			}
			$_SESSION['database_host'] = $_POST['database_host'];
			$_SESSION['database_username'] = Replace_Dodgy_Characters($_POST['database_username']);
			$_SESSION['database_password'] = $_POST['database_password'];
			$_SESSION['install_tables'] = $_POST['install_tables'];
			$_SESSION['database_name'] = Replace_Dodgy_Characters($_POST['company_name']);
			$_SESSION['db_file'] = $_POST['DemoData'] ? 'demo' : 'not';
			$_SESSION['timezone'] = $_POST['timezone'];
			$_SESSION['company_name'] = Replace_Dodgy_Characters($_POST['company_name']);
			$_SESSION['admin_email'] = $_POST['admin_email'];
			$_SESSION['admin_password'] = $_POST['admin_password'];

		}
		// Set the message
		$_SESSION['message'] = $message;
		// Specify that session support is enabled
		$_SESSION['session_support'] = '<font class="good">Enabled</font>';
		// Redirect to first page again and exit
		header('Location: index.php?sessions_checked=true');
		exit();
	}
}

// Function to workout what the default permissions are for files created by the webserver
function default_file_mode($temp_dir) {
	$v = explode('.',PHP_VERSION);
	$v = $v[0].$v[1];

	if($v > 41 && is_writable($temp_dir)) {
		$filename = $temp_dir.'/test_permissions.txt';
		$handle = fopen($filename, 'w');
		fwrite($handle, 'This file is to get the default file permissions');
		fclose($handle);
		$default_file_mode = '0'.mb_substr(sprintf('%o', fileperms($filename)), -3);
		unlink($filename);
	} else {
		$default_file_mode = '0777';
	}
	return $default_file_mode;
}

// Function to workout what the default permissions are for directories created by the webserver
function default_dir_mode($temp_dir) {
	$v = explode('.',PHP_VERSION);
	$v = $v[0].$v[1];
	if ($v > 41 && is_writable($temp_dir)) {
		$dirname = $temp_dir.'/test_permissions/';
		mkdir($dirname);
		$default_dir_mode = '0'.mb_substr(sprintf('%o', fileperms($dirname)), -3);
		rmdir($dirname);
	} else {
		$default_dir_mode = '0777';
	}
	return $default_dir_mode;
}



if (isset($_POST['path_to_root'])) {
	$path_to_root = $_POST['path_to_root'];
} else {
	$path_to_root = '..';
}

// Begin check to see if form was even submitted
// Set error if no post vars found

if (!isset($_POST['company_name'])) {
	set_error('Please fill-in the form below');
}
// End check to see if form was even submitted

// Begin path and timezone details code

// Check if user has entered the installation url
if (!isset($_POST['ba_url']) || $_POST['ba_url'] == '') {
	set_error('Please enter an absolute URL');
} else {
	$ba_url = $_POST['ba_url'];
}

// Remove any slashes at the end of the URL
if(mb_substr($ba_url, mb_strlen($ba_url) - 1, 1) == "/") {
	$ba_url = mb_substr($ba_url, 0, mb_strlen($ba_url) - 1);
}
if(mb_substr($ba_url, mb_strlen($ba_url) - 1, 1) == "\\") {
	$ba_url = mb_substr($ba_url, 0, mb_strlen($ba_url) - 1);
}
if(mb_substr($ba_url, mb_strlen($ba_url) - 1, 1) == "/") {
	$ba_url = mb_substr($ba_url, 0, mb_strlen($ba_url) - 1);
}
if(mb_substr($ba_url, mb_strlen($ba_url) - 1, 1) == "\\") {
	$ba_url = mb_substr($ba_url, 0, mb_strlen($ba_url) - 1);
}
// End path

// Begin operating system specific code
// Get operating system
if (!isset($_POST['operating_system']) || $_POST['operating_system'] != 'linux' && $_POST['operating_system'] != 'windows') {
	set_error('Please select a valid operating system');
} else {
	$operating_system = $_POST['operating_system'];
}
// Work-out file permissions
if($operating_system == 'windows') {
	$file_mode = '0777';
	$dir_mode = '0777';
} elseif (isset($_POST['world_writeable']) && $_POST['world_writeable'] == 'true') {
	$file_mode = '0777';
	$dir_mode = '0777';
} else {
	$file_mode = default_file_mode('../includes');
	$dir_mode = default_dir_mode('../includes');
}
// End operating system specific code

// Begin database details code
// Check if user has entered a database host
if (!isset($_POST['database_host']) || $_POST['database_host'] == '') {
	set_error('Please enter a database host name');
}
// Check if user has entered a database username
if (!isset($_POST['database_username']) || $_POST['database_username'] == '') {
	set_error('Please enter a database username');
}
// Check if user has entered a database password
if (!isset($_POST['database_password'])) {
	set_error('Please enter a database password');
}
// Check if user has entered a database name
if (!isset($_POST['company_name']) || $_POST['company_name'] == '') {
	set_error('Please enter a company name');
} else {
	$_POST['company_name'] = Replace_Dodgy_Characters($_POST['company_name']);
}

if (!isset($_POST['timezone']) || $_POST['timezone'] == ''){
	set_error('Please enter timezone');
}

// Use webERP logo if none supplied.

// Check if the user has entered a correct path
if (!file_exists($path_to_root.'/sql/mysql/weberp-demo.sql')){
	set_error('It appears the Absolute path that you entered is incorrect');
}

// Get admin email and validate it
if (!isset($_POST['admin_email']) || $_POST['admin_email'] == ''){
	set_error('Please enter an email for the Administrator account');
} else {
	if (IsEmailAddress($_POST['admin_email'])==false) {
		set_error('Please enter a valid email address for the Administrator account');
	}
}
// Get the two admin passwords entered, and check that they match
if (!isset($_POST['admin_password']) || $_POST['admin_password'] == '') {
	set_error('Please enter a password for the Administrator account');
}
if (!isset($_POST['admin_repassword']) || $_POST['admin_repassword'] == '') {
	set_error('Please make sure you re-enter the password for the Administrator account');
}
if ($_POST['admin_password'] != $_POST['admin_repassword']){
	// Zero BOTH passwords before returning form to user.
	$_POST['admin_password'] = '';
	$_POST['admin_repassword'] = '';
	set_error('The two Administrator account passwords you entered do not match');
}
// End admin user details code

$config_filename = $path_to_root . '/config.php';
// only make a new company directory structure if we are kicking off a new company
// no need to bother if just setting up the demo data
$CompanyDir = $path_to_root . '/companies/' . $_POST['company_name'];
if ($_POST['DemoData']==false){
	$Result = mkdir($CompanyDir);
	$Result = mkdir($CompanyDir . '/part_pics');
	$Result = mkdir($CompanyDir . '/EDI_Incoming_Orders');
	$Result = mkdir($CompanyDir . '/reports');
	$Result = mkdir($CompanyDir . '/EDI_Sent');
	$Result = mkdir($CompanyDir . '/EDI_Pending');
	$Result = mkdir($CompanyDir . '/reportwriter');
	$Result = mkdir($CompanyDir . '/pdf_append');
	$Result = mkdir($CompanyDir . '/FormDesigns');
	copy ($path_to_root . '/companies/weberpdemo/FormDesigns/GoodsReceived.xml', $CompanyDir . '/FormDesigns/GoodsReceived.xml');
	copy ($path_to_root . '/companies/weberpdemo/FormDesigns/PickingList.xml', $CompanyDir . '/FormDesigns/PickingList.xml');
	copy ($path_to_root . '/companies/weberpdemo/FormDesigns/PurchaseOrder.xml', $CompanyDir . '/FormDesigns/PurchaseOrder.xml');

	// Now have a destination to place the logo image.
	if (isset($_FILES['LogoFile'])) {
	    if ($_FILES['LogoFile']['error'] == UPLOAD_ERR_OK) {
		$result = move_uploaded_file($_FILES['LogoFile']['tmp_name'],
						    $CompanyDir . '/logo.jpg');
	    } elseif ($_FILES['LogoFile']['error'] == UPLOAD_ERR_INI_SIZE ||
		      $_FILES['LogoFile']['error'] == UPLOAD_ERR_FORM_SIZE) {
			set_error( 'Logo file is too big - Limit: ' . $_SESSION['MaxLogoSize'] );
	    } elseif ($_FILES['LogoFile']['error'] == UPLOAD_ERR_NO_FILE ) {
		//  No logo file, so use the default.
		copy( $path_to_root . '/logo_server.jpg', $CompanyDir . '/logo.jpg');
	    } else {
		set_error( 'Error uploading logo file' );
	    }
	} else {
		//  No logo file, so use the default.
		copy( $path_to_root . '/logo_server.jpg', $CompanyDir . '/logo.jpg');
	}
}

//$msg holds the text of the new config.php file
$msg = "<?php\n\n";
$msg .= "// User configurable variables\n";
$msg .= "//---------------------------------------------------\n\n";
$msg .= "//DefaultLanguage to use for the login screen and the setup of new users - the users language selection will override\n";
$msg .= "\$DefaultLanguage ='en_GB.utf8';\n\n";
$msg .= "// Whether to display the demo login and password or not on the login screen\n";
$msg .= "\$allow_demo_mode = False;\n\n";
$msg .= "//  Connection information for the database\n";
$msg .= "// \$host is the computer ip address or name where the database is located\n";
$msg .= "// assuming that the web server is also the sql server\n";
$msg .= "\$host = '" . $_POST['database_host'] . "';\n\n";

$msg .= "// assuming that the web server is also the sql server\n";
$msg .= "\$dbType = 'mysqli';\n";

$msg .= "// assuming that the web server is also the sql server\n";
$msg .= "\$dbuser = '" . $_POST['database_username'] . "';\n";
$msg .= "// assuming that the web server is also the sql server\n";
$msg .= "\$dbpassword = '" . $_POST['database_password'] . "';\n";

$msg .= "// The timezone of the business - this allows the possibility of having;\n";

$msg .= "date_default_timezone_set('" . $_POST['timezone'] . "');\n";
$msg .= "\$AllowCompanySelectionBox = true;\n";
if ($_POST['DemoData'] ==false){
	$msg .= "\$DefaultCompany = '" . $_POST['company_name']. "';\n";
} else {
	$msg .= "\$DefaultCompany = 'weberpdemo';\n";
}
$msg .= "\$SessionLifeTime = 3600;\n";
$msg .= "\$MaximumExecutionTime =120;\n";
$msg .= "\$CryptFunction = 'sha1';\n";
$msg .= "\$DefaultClock = 12;\n";

$msg .= "\$rootpath = dirname(htmlspecialchars(\$_SERVER['PHP_SELF']));\n";
$msg .= "if (isset(\$DirectoryLevelsDeep)){\n";
$msg .= "   for (\$i=0;\$i<\$DirectoryLevelsDeep;\$i++){\n";
$msg .= "\$rootpath = mb_substr(\$rootpath,0, strrpos(\$rootpath,'/'));\n";
$msg .= "} }\n";

$msg .= "if (\$rootpath == '/' OR \$rootpath == '\\\') {;\n";
$msg .= "\$rootpath = '';\n";
$msg .= "}\n";
$msg .= "error_reporting (E_ALL & ~E_NOTICE);\n";
$msg .= "?>";

if (!$zp = fopen($path_to_root . '/config.php', 'w')){
	set_error("Cannot open the configuration file ($config_filename)");
} else {
	if (!fwrite($zp, $msg)){
		fclose($zp);
		set_error("Cannot write to the configuration file ($config_filename)");
	}
	// close file
	fclose($zp);
}

// Try connecting to database

$db = mysqli_connect($_POST['database_host'], $_POST['database_username'], $_POST['database_password']);
if (!$db){
	set_error('Database host name, username and/or password incorrect. MySQL Error:<br />'. mysqli_connect_error());
}

if($_POST['install_tables'] == true){

	/* Need to read in the sql script and process the queries to initate a new DB */
	if ($_POST['DemoData'] == true){ //installing the demo data
		$SQLScriptFile = file($path_to_root . '/sql/mysql/weberp-demo.sql');
		//need to drop any pre-existing weberpdemo database
		mysqli_query($db, "DROP DATABASE 'weberpdemo'");
	} else { //creating a new database with no demo data
		$SQLScriptFile = file($path_to_root . '/sql/mysql/weberp-new.sql');
	}
	mysqli_query($db, 'CREATE DATABASE IF NOT EXISTS `' . mysqli_real_escape_string($db, $_POST['company_name']) . '`');
	mysqli_select_db($db, $_POST['company_name']);
	$ScriptFileEntries = sizeof($SQLScriptFile);
	$SQL ='';
	$InAFunction = false;
	for ($i=0; $i<$ScriptFileEntries; $i++) {

		$SQLScriptFile[$i] = trim($SQLScriptFile[$i]);
		//ignore lines that start with -- or USE or /*
		if (mb_substr($SQLScriptFile[$i], 0, 2) != '--'
			AND mb_strstr($SQLScriptFile[$i],'/*')==FALSE
			AND mb_strlen($SQLScriptFile[$i])>1){

			$SQL .= ' ' . $SQLScriptFile[$i];

			//check if this line kicks off a function definition - pg chokes otherwise
			if (mb_substr($SQLScriptFile[$i],0,15) == 'CREATE FUNCTION'){
				$InAFunction = true;
			}
			//check if this line completes a function definition - pg chokes otherwise
			if (mb_substr($SQLScriptFile[$i],0,8) == 'LANGUAGE'){
				$InAFunction = false;
			}
			if (mb_strpos($SQLScriptFile[$i],';')>0 AND ! $InAFunction){
				// Database created above with correct name.
				if (strncasecmp($SQL, ' CREATE DATABASE ', 17)
				    AND strncasecmp($SQL, ' USE ', 5)){
					$SQL = mb_substr($SQL,0,mb_strlen($SQL)-1);
					$result = mysqli_query($db,$SQL);
				    }
				    $SQL = '';
			}

		} //end if its a valid sql line not a comment
	} //end of for loop around the lines of the sql script
}
$sql = "UPDATE www_users
			SET password = '" . sha1($_POST['admin_password']) . "',
				email = '".mysqli_real_escape_string($db, $_POST['admin_email']) ."'
			WHERE userid = 'admin'";
$result = mysqli_query($db,$sql);
$sql = "UPDATE companies
			SET coyname = '". mysqli_real_escape_string($db, $_POST['company_name']) . "'
			WHERE coycode = 1";
$result = mysqli_query($db,$sql);

session_unset();
session_destroy();

header('Location: ' . $path_to_root . '/index.php');
ini_set('max_execution_time', '60');
echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $path_to_root . '/index.php?' . SID . "'>";
?>
