<?php
//define('APPROOT', dirname(__FILE__).'/');
//session_name('itop-'.md5(APPROOT));
//session_start();
//echo "start<br>";
//$_SESSION['EXT_USER'] = array_values($_SESSION['samlUserdata']['User.email'])[0] . '<br>';
//echo $_SESSION['EXT_USER'];
//echo $_POST['SAMLResponse'];
$sConfigFile = 'conf/production/config-itop.php';
$sStartPage = './pages/UI.php';
$sSetupPage = './setup/index.php';
//echo implode('<br>',$_SERVER);
//echo $_SESSION . "<br>";
//echo $_SESSION['samlUserdata'] . "<br>";
//echo "<h3> PHP List All Session Variables</h3>";
//    foreach ($_SESSION as $key=>$val)
//        echo $key." ".$val."<br/>";
//echo implode('<br>',$_SESSION);
//foreach($_SERVER as $key=>$value) {
//  if(substr($key, 0, 7) == 'MELLON_') {
//      echo($key . '=' . $value . "\r\n");
//        }
/**
 * Check that the configuration file exists and has the appropriate access rights
 * If the file does not exist, launch the configuration wizard to create it
 */  
if (file_exists(dirname(__FILE__).'/'.$sConfigFile))
{
	if (!is_readable($sConfigFile))
	{
		echo "<p><b>Error</b>: Unable to read the configuration file: '$sConfigFile'. Please check the access rights on this file.</p>";
	}
	else if (is_writable($sConfigFile))
	{
		echo "<p><b>Security Warning</b>: the configuration file '$sConfigFile' should be read-only.</p>";
		echo "<p>Please modify the access rights to this file.</p>";
		echo "<p>Click <a href=\"$sStartPage\">here</a> to ignore this warning and continue to run iTop.</p>";
	}
	else
	{
		header("Location: $sStartPage");
	}
}
else
{
	// Config file does not exist, need to run the setup wizard to create it
	header("Location: $sSetupPage");
}
?>
