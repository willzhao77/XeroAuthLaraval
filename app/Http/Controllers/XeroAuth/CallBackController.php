<?php

namespace App\Http\Controllers\XeroAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Storage;
require '../vendor/autoload.php';

class CallBackController extends Controller
{
    public function index()
    {
      // Storage Classe uses sessions for storing token > extend to your DB of choice
      $storage = new Storage();

      $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => getenv('CLIENT_ID'),
        'clientSecret'            => getenv('Client_Secret'),
        'redirectUri'             => 'http://localhost/callback',
        'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
        'urlAccessToken'          => 'https://identity.xero.com/connect/token',
        'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
      ]);

      // If we don't have an authorization code then get one
      if (!isset($_GET['code'])) {
        echo "Something went wrong, no authorization code found";
        exit("Something went wrong, no authorization code found");

      // Check given state against previously stored one to mitigate CSRF attack
      } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        echo "Invalid State";
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
      } else {

        try {
          // Try to get an access token using the authorization code grant.
          $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
          ]);

          $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
          // $config = \vendor\xeroapi\xero\php\oauth2\lib\ConfigurationConfiguration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
          $identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
            new \GuzzleHttp\Client(),
            $config
          );

          $result = $identityApi->getConnections();

          //set token to groble varable
          // config(['global.workflowtoken' => $accessToken->getToken()]);
          // $GLOBALS['workflowtoken'] = $accessToken->getToken();
          // dd($GLOBALS['workflowtoken']);
          // dd($accessToken->getToken());
          // Save my tokens, expiration tenant_id

          $filename = 'token.txt';
          file_put_contents($filename, $accessToken->getToken());

          // $storage->setToken(
          //     $accessToken->getToken(),
          //     $accessToken->getExpires(),
          //     $result[0]->getTenantId(),
          //     $accessToken->getRefreshToken(),
          //     $accessToken->getValues()["id_token"]
          // );

          // header('Location: ' . './authorizedResource.php');
          header('Location: ' . 'http://localhost:8080/mainpage');
          exit();

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
          echo "Callback failed";
          exit();
        }
      }
    }
}
