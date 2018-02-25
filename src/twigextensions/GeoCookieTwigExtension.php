<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\twigextensions;

use lukeyouell\geocookie\GeoCookie;

use Craft;

/**
 * @author    Luke Youell
 * @package   GeoCookie
 * @since     1.1.0
 */
class GeoCookieTwigExtension extends \Twig_Extension
{
  public function getName()
  {
      return 'Geo Cookie';
  }

  public function getFunctions()
  {
      return [
          new \Twig_SimpleFunction('geocookie', [$this, 'geocookie']),
      ];
  }

  public function geocookie()
  {
      return GeoCookie::$plugin->geoCookieService->location();
  }
}
