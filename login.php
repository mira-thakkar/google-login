<?php

require_once dirname(__FILE__).'\google-api-php-client\src\Google\autoload.php';
session_start();

$appName="Application_name";
$client_id="Provide Clinet_ID here";
$client_secret="Provide Client_Secret here";
$redirect_uri="Set Redirect Uri here"

//create client object with app credentials
$client = new Google_client();
$client->setApplicationName($appName);
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);

//set scope to get permission for accessing client detail
$client->setScopes(array('https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/plus.me', 'https://www.googleapis.com/auth/email', 'https://www.googleapis.com/auth/profile'));

$oauth2 = new Google_Service_Oauth2($client);
$plus  = new Google_Service_Plus($client);

if (isset($_GET['code']))
{
//authencticate user
$client->authenticate($_GET['code']);

//get access token
$_SESSION['token'] = $client->getAccessToken();
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
$set_asess_token = $client->setAccessToken($_SESSION['token']);
}
else{
$authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
$user_data = $oauth2->userinfo->get();
$me = $plus->people->get('me');
?>

//display user data
<img src="<?php $me['image']['url'] ?>" height='100px' width='100px'/>

<?php
echo "Name: ". $me['displayName'];
echo "Email:".$user_data['email'];
echo "Gplus Id: ".$me['id'];
echo "Gender: ". $me['gender'];
echo "Location:  ". $me['placesLived'][0]['value'];
echo "Tagline: ". $me['tagline'];
echo "Places: ". $me['organizations'][0]['name'];
}
else
{
?>
<a href="<?php echo $authUrl; ?> ">Login</a>
<?php 
}
?>