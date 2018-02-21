<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\services;

use lukeyouell\geocookie\GeoCookie;

use Craft;
use craft\base\Component;

/**
 * @author    Luke Youell
 * @package   GeoCookie
 * @since     1.0.0
 */
class GeoCookieService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
     public function location()
     {

       // Get settings
       $settings = GeoCookie::$plugin->getSettings();

       // Request ip address
       $ipAddress = $this->getIpAddress($settings);

       // Request cookie
       $cookie = $this->getCookie($settings);

       if ($cookie) {

         // Cookie already exists, so set the location as the cookie value and set 'cached' to true to show that the cookie already existed

         $location = $cookie->value;
         $location->cached = true;

         return $location;

       } else {

         // Cookie doesn't exist, so fetch the user's location using api source and store it as a cookie, set 'cached' to false to show that the cookie didn't exist

         $apiSource = $settings->apiSource;

         $location = $this->getLocation($settings, $ipAddress);
         // $location->cached = false;

         return $location;

       }

     }

     /**
      */
     public function getIpAddress($settings)
     {

       // Request visitor's ip address, if unable to do so use the fallback value
       $ipAddress = Craft::$app->request->getUserIP();

       if (!$ipAddress) {

         // Unable to source ip address so use fallback value
         $ipAddress = $settings->fallbackIp;

       }

       // If anonymisation is enabled
       if ($settings->anonymisation) {

         // Check if ip address is ipv4 or ipv6
         if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

           // ip address is ipv4
           $delimiter = '.';
           $explode = explode($delimiter, $ipAddress);

         } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {

           // ip address is ipv6
           $delimiter = ':';
           $explode = explode($delimiter, $ipAddress);

         } else {

           // ip address is neither ipv4 or ipv6
           $explode = false;

         }

         if ($explode) {

           // Fetch last element
           $lastElement = count($explode) - 1;

           // Set last element as 0 (anonymising the ip address)
           $explode[$lastElement] = '0';

           // Update ip address with anonymised version by imploding
           $ipAddress = implode($delimiter, $explode);

         }

       }

       return $ipAddress;

     }

     /**
      */
     public function getCookie($settings)
     {

       // Request cookie to check if it already exists
       $cookie = Craft::$app->request->cookies->get($settings->cookieName);

       if ($cookie) {

         // Cookie exists, so return the cookie object
         return $cookie;

       } else {

         // Cookie doesn't exist so return false
         return false;

       }

     }

     /**
      */
     public function getLocation($settings, $ipAddress)
     {

       // Request location based on ip address

       // Set client based on preferred api source

       switch ($settings->apiSource) {

         case 'dbip':

           $clientUrl = 'http://api.db-ip.com';

           $clientPath = 'v2/'.$settings->apiKey.'/'.$ipAddress;

           break;

         case 'extremeiplookup':

           $clientUrl = 'https://extreme-ip-lookup.com';

           $clientPath = 'json/'.$ipAddress;

           break;

         case 'freegeoip':

           $clientUrl = 'https://freegeoip.net';

           $clientPath = 'json/'.$ipAddress;

           break;

         case 'ipapi':

           $clientUrl = 'https://ipapi.co';

           $clientPath = $ipAddress.'/json';

           break;

         case 'ipapicom':

           $clientUrl = 'http://ip-api.com';

           $clientPath = 'json/'.$ipAddress;

           break;

         case 'ipfind':

           $clientUrl = 'https://ipfind.co';

           $clientPath = '?ip='.$ipAddress.'&auth='.$settings->apiKey;

           break;

         case 'ipinfo':

           $clientUrl = 'https://ipinfo.io';

           $clientPath = $ipAddress.'/json';

           break;

         case 'keycdn':

           $clientUrl = 'https://tools.keycdn.com';

           $clientPath = 'geo.json?host='.$ipAddress;

           break;

         default:

           $clientUrl = 'https://ipapi.co';

           $clientPath = $ipAddress.'/json';

       }

       $client = new \GuzzleHttp\Client([
         'base_uri' => $clientUrl,
         'http_errors' => false,
         'timeout' => $settings->requestTimeout
       ]);

       try {

         $response = $client->request('GET', $clientPath);

         if ($response->getStatusCode() === 200) {

           // Request was successful, so decode json response and store as a cookie

           $location = json_decode($response->getBody());
           $location->cached = false;

           $cookies = Craft::$app->response->cookies;

           $cookies->add(new \yii\web\Cookie([
               'name' => $settings->cookieName,
               'value' => $location,
               // current timestamp + (cookieDuration setting in hours x number of seconds in an hour)
               'expire' => time() + ($settings->cookieDuration * 3600)
           ]));

           $cookie = $cookies->get($settings->cookieName);

           return $cookie->value;

         } else {

           // Request failed so return false
           return (object) array('error' => true, 'statusCode' => $response->getStatusCode());

         }

       } catch (\Exception $e) {

         return (object) array('error' => true, 'errorMessage' => $e->getMessage());

       }

     }
}
