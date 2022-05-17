<?php
/**
 * AutoSuggest Field plugin for Craft CMS 4.x
 *
 * A fieldtype that allows you to select from existing values in the section.
 *
 * @link      https://dodeca.studio
 * @copyright Copyright (c) 2022 Dodeca Studio
 */

namespace dodecastudio\autosuggest\fields;

use dodecastudio\autosuggest\AutoSuggest;

use Craft;
use craft\elements\Entry;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;

/**
 * 
 * @author    Dodeca Studio
 * @package   AutoSuggest
 * @since     2.0.0
 *
 */
class AutoSuggestField extends Field
{
    // Public Properties
    // =========================================================================

    // /**
    //  * @var string
    //  */
    public $suggestionDefaults = '';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName () : string
    {
        return Craft::t('auto-suggest', 'Auto Suggest');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules () : array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['suggestionDefaults', 'string'],
            ['suggestionDefaults', 'default', 'value' => ''],
        ]);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType () : string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null) : mixed
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue ($value, ElementInterface $element = null) : mixed
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml () : ?string
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'auto-suggest/_components/fields/_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml ($value, ElementInterface $element = null) : string
    {

        $entrySuggestions = [];
        $defaultSuggestions = [];
        $siteId = $element->siteId;
        $sectionId = $element->sectionId;
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Get default values
        $defaults = trim($this->suggestionDefaults);
        if (!empty($defaults)) {
            $defaultSuggestions = preg_split("/\r\n|\n|\r/", $defaults);
            // Remove any rogue empty items
            $defaultSuggestions = array_filter($defaultSuggestions, 'strlen');
        }
        
        if ($siteId and $sectionId) {
            // Fetch suggestions
            $suggestionData = Entry::find()
            ->siteId($siteId)
            ->sectionId($sectionId)
            ->anyStatus()
            ->all();
            
            // Create array of results
            foreach($suggestionData as $suggestion) {
                if (!empty($suggestion[$this->handle]) && $suggestion[$this->handle] != NULL) {
                    $entrySuggestions[] = $suggestion[$this->handle];
                }
            }

            // Sort results by frequency, remove dupicates
            $filteredEntrySuggestions = array_count_values($entrySuggestions);
            arsort($filteredEntrySuggestions);
            // Limit to 200 suggestions
            $sortedSuggestions = array_slice(array_keys($filteredEntrySuggestions), 0, AutoSuggest::getInstance()->getSettings()->maxSuggestions, true);
        }

        $allSuggestions = array_values(array_unique(array_merge($sortedSuggestions, $defaultSuggestions)));

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'auto-suggest/_components/fields/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'suggestions' => [
                  [
                    'label' => Craft::t('site', 'Existing values in this section'),
                    'data' => $allSuggestions 
                  ]
                ],
            ]
        );
    }
}