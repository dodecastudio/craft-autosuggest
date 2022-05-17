<?php
/**
 * AutoSuggest plugin for Craft CMS 4.x
 *
 * @link      https://dodeca.studio
 * @copyright Copyright (c) 2022 Dodeca Studio
 */

namespace dodecastudio\autosuggest;

use dodecastudio\autosuggest\models\Settings;
use dodecastudio\autosuggest\fields\AutoSuggestField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;


/**
 * 
 * @author    Dodeca Studio
 * @package   AutoSuggest
 * @since     2.0.0
 *
 */
class AutoSuggest extends Plugin
{

    // Static Properties

    /**
     * @var AutoSuggest
     */
    public static $plugin;

    // Public Properties

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public bool $hasCpSettings = false;

    /**
     * @var bool
     */
    public bool $hasCpSection = false;

    // Public Methods

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = AutoSuggestField::class;
            }
        );

        Craft::info(
            Craft::t(
                'auto-suggest',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

    }

    // Settings

    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new Settings();
    }

}

