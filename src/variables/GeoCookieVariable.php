<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\variables;

use lukeyouell\geocookie\GeoCookie;

use Craft;

class GeoCookieVariable
{
    // Public Methods
    // =========================================================================

     public function location()
     {
         return GeoCookie::$plugin->geoService->location();
     }
}
