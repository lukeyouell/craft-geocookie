<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\models;

use lukeyouell\geocookie\GeoCookie;

use Craft;
use craft\base\Model;

/**
 * @author    Luke Youell
 * @package   GeoCookie
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
     public $anonymisation = true;

    /**
     * @var string
     */
     public $apiSource = 'ipapi';

    /**
     * @var string
     */
     public $apiKey = null;

    /**
     * @var integer
     */
     public $requestTimeout = 5;

    /**
     * @var string
     */
     public $fallbackIp = '8.8.8.8';

    /**
     * @var string
     */
     public $cookieName = 'geoCookie';

    /**
     * @var integer
     */
     public $cookieDuration = 168;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
      return [
          ['anonymisation', 'boolean'],
          [['apiSource', 'apiKey', 'fallbackIp', 'cookieName'], 'string'],
          [['requestTimeout', 'cookieDuration'], 'integer'],
          [['apiSource', 'requestTimeout', 'fallbackIp', 'cookieName', 'cookieDuration'], 'required']
      ];
    }
}
