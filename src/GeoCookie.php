<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie;

use lukeyouell\geocookie\services\GeoCookieService as GeoCookieServiceService;
use lukeyouell\geocookie\variables\GeoCookieVariable;
use lukeyouell\geocookie\models\Settings;
use lukeyouell\geocookie\twigextensions\GeoCookieTwigExtension;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use craft\web\Request;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class GeoCookie
 *
 * @author    Luke Youell
 * @package   GeoCookie
 * @since     1.0.0
 *
 * @property  GeoCookieServiceService $geoCookieService
 */
class GeoCookie extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var GeoCookie
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        
        Craft::$app->view->registerTwigExtension(new GeoCookieTwigExtension());

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('geoCookie', GeoCookieVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/geo-cookie'))->send();
                }
            }
        );

        Craft::info(
            Craft::t(
                'geo-cookie',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'geo-cookie/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
