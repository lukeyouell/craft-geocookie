<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\utilities;

use Craft;
use craft\base\Utility;
use craft\services\SystemSettings;

use lukeyouell\geocookie\GeoCookie;

class Log extends Utility
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('geo-cookie', 'Geo Cookie');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'geo-cookie';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@lukeyouell/geocookie/icon-mask.svg");
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {

        $settings = GeoCookie::$plugin->getSettings();

        return Craft::$app->getView()->renderTemplate(
            'geo-cookie/utility',
            [
                'settings' => $settings,
                'logs'     => self::getLogs()
            ]
        );
    }

    public static function getLogs()
    {
        return GeoCookie::$plugin->logService->getLogs();
    }
}
