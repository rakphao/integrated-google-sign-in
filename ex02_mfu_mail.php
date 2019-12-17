<?php
/**
 * Integrated Google Sign-In
 *
 * @author Rakphao Theppan <rakphao.the@mfu.ac.th>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */
require_once 'vendor/autoload.php';
$redirectUri = 'http://localhost/integrated-google-sign-in/ex02_mfu_mail.php';

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
$client->setHostedDomain('mfu.ac.th');

$login_success = FALSE;

// authenticate code from Google OAuth Flow
if (isset($_GET['logout']))
{
	$client->revokeToken();
	header('location:ex02_mfu_mail.php');
}

if (isset($_GET['code']))
{
	$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

	//This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
	if ( ! isset($token['error']))
	{

		$client->setAccessToken($token['access_token']);

		// get profile info
		$google_oauth = new Google_Service_Oauth2($client);
		$google_account_info = $google_oauth->userinfo->get();

		$acceptedDomains = array('mfu.ac.th', 'lamduan.mfu.ac.th');
		if (in_array(substr($google_account_info->email, strrpos($google_account_info->email, '@') + 1), $acceptedDomains))
		{
			$login_success = TRUE;
			//Display logout Button
			echo "<a href='" . $redirectUri . "?logout=true'>Logout</a>";

			// now you can use this profile info to create account in your website and make user logged in.

			#$email = $google_account_info->email;
			#$name = $google_account_info->name;

			echo '<pre>';
			print_r(@$google_account_info);
			echo '</pre>';
		} else
		{
			$login_success = FALSE;
			echo "Error !! กรุณาล็อคอินด้วยอีเมล์ @mfu.ac.th, @lamduan.mfu.ac.th<br/>";
		}
	}
}


if ( ! $login_success)
{
	//Display Login Button
	echo "<a class='myButton' href='" . $client->createAuthUrl() . "'><svg version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" width=\"18px\" height=\"18px\" viewBox=\"0 0 48 48\" class=\"abcRioButtonSvg\"><g><path fill=\"#EA4335\" d=\"M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z\"></path><path fill=\"#4285F4\" d=\"M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z\"></path><path fill=\"#FBBC05\" d=\"M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z\"></path><path fill=\"#34A853\" d=\"M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z\"></path><path fill=\"none\" d=\"M0 0h48v48H0z\"></path></g></svg> 
				Google Login
			</a>";
}


?>
<style>
    .myButton {
        box-shadow: inset 0px 1px 0px 0px #ffffff;
        background: linear-gradient(to bottom, #f9f9f9 5%, #e9e9e9 100%);
        background-color: #f9f9f9;
        border-radius: 6px;
        border: 1px solid #dcdcdc;
        display: inline-block;
        cursor: pointer;
        color: #666666;
        font-family: Arial;
        font-size: 15px;
        font-weight: bold;
        padding: 12px 32px;
        text-decoration: none;
        text-shadow: 0px 1px 0px #ffffff;
    }

    .myButton:hover {
        background: linear-gradient(to bottom, #e9e9e9 5%, #f9f9f9 100%);
        background-color: #e9e9e9;
    }

    .myButton:active {
        position: relative;
        top: 1px;
    }

</style>
