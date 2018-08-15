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

use lukeyouell\geocookie\models\Settings;
use lukeyouell\geocookie\twigextensions\GeoCookieTwigExtension;
use lukeyouell\geocookie\utilities\Log;
use lukeyouell\geocookie\variables\GeoCookieVariable;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\services\Utilities;
use craft\web\Request;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class GeoCookie extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    // Public Properties
    // =========================================================================

    public $schemaVersion = '1.1.0';

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(new GeoCookieTwigExtension());

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/geo-cookie'))->send();
                }
            }
        );

        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Log::class;
            }
        );

        // Register components
        $this->setComponents([
            'geoService' => \lukeyouell\geocookie\services\GeoService::class,
            'logService' => \lukeyouell\geocookie\services\LogService::class,
        ]);
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        // Get the settings that are being defined by the config file
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'geo-cookie/settings',
            [
                'settings' => $settings,
                'overrides' => array_keys($overrides)
            ]
        );
    }
}
