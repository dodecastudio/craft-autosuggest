<?php
/**
 * AutoComplete plugin for Craft CMS 3.x
 *
 * @link      https://dodeca.studio
 * @copyright Copyright (c) 2022 Dodeca Studio
 */

namespace dodecastudio\autocomplete;

use dodecastudio\autocomplete\fields\AutoCompleteField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;


/**
 * 
 * @author    Dodeca Studio
 * @package   AutoComplete
 * @since     1.0.0
 *
 */
class AutoComplete extends Plugin
{

    // Static Properties

    /**
     * @var AutoComplete
     */
    public static $plugin;

    // Public Properties

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = AutoCompleteField::class;
            }
        );

        Craft::info(
            Craft::t(
                'auto-complete',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

    }

    // Settings

    protected function createSettingsModel()
    {
        return new Settings();
    }

}

