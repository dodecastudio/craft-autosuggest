<?php
/**
 * AutoComplete Field plugin for Craft CMS 3.x
 *
 * A fieldtype that allows you to select from existing values in the section.
 *
 * @link      https://dodeca.studio
 * @copyright Copyright (c) 2022 Dodeca Studio
 */

namespace dodecastudio\autocomplete\fields;

use dodecastudio\autocomplete\AutoComplete;

use Craft;
use craft\elements\Entry;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;

/**
 * 
 * @author    Dodeca Studio
 * @package   AutoComplete
 * @since     1.0.0
 *
 */
class AutoCompleteField extends Field
{
    // Public Properties
    // =========================================================================

    // /**
    //  * @var string
    //  */
    // public $limitToSubfolder = '';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName (): string
    {
        return Craft::t('auto-complete', 'Auto Complete');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules ()
    {
        $rules = parent::rules();

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType (): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue ($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue ($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml ()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'auto-complete/_components/fields/_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml ($value, ElementInterface $element = null): string
    {

        $allSuggestions = [];
        $siteId = $element->siteId;
        $sectionId = $element->sectionId;
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);
        
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
                    $allSuggestions[] = $suggestion[$this->handle];
                }
            }

            // Sort results by frequency, remove dupicates
            $filteredSuggestions = array_count_values($allSuggestions);
            arsort($filteredSuggestions);
            // Limit to 200 suggestions
            $sortedSuggestions = array_slice(array_keys($filteredSuggestions), 0, AutoComplete::getInstance()->getSettings()->maxSuggestions, true);
        }

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'auto-complete/_components/fields/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'suggestions' => [
                  [
                    'label' => Craft::t('site', 'Existing values in this section'),
                    'data' => $sortedSuggestions 
                  ]
                ],
            ]
        );
    }
}