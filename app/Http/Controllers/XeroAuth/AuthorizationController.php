<?php
namespace App\Http\Controllers\XeroAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Storage;
require '../vendor/autoload.php';


class AuthorizationController extends Controller
{
    public function index()
    {
      // return getenv('CLIENT_ID');
      // Storage Class uses sessions for storing access token (demo only)
      // you'll need to extend to your Database for a scalable solution
      // $storage = new StorageClass();
      $storage = new Storage();

      // session_start();

      $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => env('CLIENT_ID'),
        'clientSecret'            => env('Client_Secret'),
        'redirectUri'             => 'http://localhost/callback',
        'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
        'urlAccessToken'          => 'https://identity.xero.com/connect/token',
        'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
      ]);

      // Scope defines the data your app has permission to access.
      // Learn more about scopes at https://developer.xero.com/documentation/oauth2/scopes
      $options = [
          'scope' => ['workflowmax']
      ];

      // This returns the authorizeUrl with necessary parameters applied (e.g. state).
      $authorizationUrl = $provider->getAuthorizationUrl($options);

      // Save the state generated for you and store it to the session.
      // For security, on callback we compare the saved state with the one returned to ensure they match.
      $_SESSION['oauth2state'] = $provider->getState();

      // Redirect the user to the authorization URL.
      header('Location: ' . $authorizationUrl);
      exit();
    }
}
