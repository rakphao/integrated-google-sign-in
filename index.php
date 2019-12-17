<?php
/**
 * index.php
 *
 * Integrated Google Sign-In
 *
 * @author Rakphao Theppan <rakphao.the@mfu.ac.th>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */
require_once 'vendor/autoload.php';

// load configuration
require 'config.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
$client->setPrompt('select_account consent');

$login_success = FALSE;

// authenticate code from Google OAuth Flow
if (isset($_GET['logout']))
{
	$client->revokeToken();
	header('location:index.php');
}

if (isset($_GET['code']))
{
	$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

	//This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
	if ( ! isset($token['error']))
	{
		$login_success = TRUE;

		$client->setAccessToken($token['access_token']);

		// get profile info
		$google_oauth = new Google_Service_Oauth2($client);
		$google_account_info = $google_oauth->userinfo->get();

		//Display logout Button
		echo "<a href='" . $redirectUri . "?logout=true'>Logout</a>";

		// now you can use this profile info to create account in your website and make user logged in.

		#$email = $google_account_info->email;
		#$name = $google_account_info->name;

		echo '<pre>';
		print_r(@$google_account_info);
		echo '</pre>';
	}
}


if ( ! $login_success)
{
	//Display Login Button
	echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
}
