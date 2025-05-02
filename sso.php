<?php
/*

install
composer require league/oauth2-client

*/
session_start();
include("./vendor/autoload.php");
//require_once('includes/load.php');

$guzzyClient = new GuzzleHttp\Client([
    'defaults' => [
        \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 5,
        \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true],
     \GuzzleHttp\RequestOptions::VERIFY => false,
]);

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '25',    // The client ID assigned to you by the provider
    'clientSecret'            => 'OQAHrvmuPsbTGrfzU8fobHPnUh6x8iacHtNd9n6h',    // The client password assigned to you by the provider
    'redirectUri'             => 'http://localhost:8888/sso.php',
    'urlAuthorize'            => 'https://sso.poliwangi.ac.id/oauth/authorize',
    'urlAccessToken'          => 'https://sso.poliwangi.ac.id/oauth/token',
    'urlResourceOwnerDetails' => 'https://sso.poliwangi.ac.id/api/user'
]);

$provider->setHttpClient($guzzyClient);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Optional, only required when PKCE is enabled.
    // Get the PKCE code generated for you and store it to the session.
    $_SESSION['oauth2pkceCode'] = $provider->getPkceCode();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    
    exit('Invalid state');

} else {

    try {
        
        /*$guzzle = new GuzzleHttp\Client([
            'defaults' => [
                \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 5,
                \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true],
             \GuzzleHttp\RequestOptions::VERIFY => false,
        ]);

        $response = $guzzle->post('https://sso.poliwangi.ac.id/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => '13',
                'client_secret' => 'rXrh3f1U5TD92TsGBV1xjC6jfBwrxc0YfXBjoVt1',
                'redirect_uri' => 'http://localhost:9999/oatuh.php',
                'code' => $_GET['code']
            ],
        ]);
        
        print_r($response);exit();*/
        // Optional, only required when PKCE is enabled.
        // Restore the PKCE code stored in the session.
        $provider->setPkceCode($_SESSION['oauth2pkceCode']);

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        /*echo 'Access Token: ' . $accessToken->getToken() . "<br>";
        echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
        echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
        echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";*/

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);
        $userArr = $resourceOwner->toArray();
        
        $user_id = false;
        
        //print_r($userArr);exit();
        
        $sql  = sprintf("SELECT usr_id, nama, usernama, password, level_id FROM user WHERE usernama ='%s' LIMIT 1", $userArr['username']);
        //echo $sql;exit();
        $result = mysqli_query($con,$sql);
        if(mysqli_num_rows($result)){
            $user = mysqli_fetch_assoc($result);
            //print_r($user);exit();
            $user_id = $user['usr_id'];
        }else{

           
            $sql  = sprintf("INSERT INTO user (`usr_id`, `nama`, `nip_nik_cuti`, `id_absen`, `usernama`, `password`, `level_id`, `alamat`) VALUES (null, '%s', '', '', '%s', '%s', '3', '')", $userArr['name'],$userArr['name'],sha1($userArr['last_login_at']));
            //echo $sql;exit();
            $result = mysqli_query($con,$sql);

            $sql  = sprintf("SELECT usr_id, nama, usernama, password, level_id FROM user WHERE usernama ='%s' LIMIT 1", $userArr['username']);
            $result = mysqli_query($con,$sql);
            if(mysqli_num_rows($result)){
                $user = mysqli_fetch_assoc($result);
                //print_r($user);exit();
                $user_id = $user['usr_id'];
            }
        }
        
        if($user_id){
             $session->login($user_id);
             $user         = current_user();
             $session->msg("s", "Selamat Datang ".$user['nama']);
             if(cekNama($usernama))redirect('profile.php',false);
             redirect('download.php',false);
        } else {
            $session->msg("d", "Maaf user atau password salah.");
            redirect('index.php',false);
        }

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}
