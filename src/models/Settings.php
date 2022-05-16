<?php

namespace dodecastudio\autosuggest\models;

use craft\base\Model;

class Settings extends Model
{
    
    // Maximum number of suggestions to return
    public $maxSuggestions = 200;

    public function rules()
    {
        return [
            ['maxSuggestions', 'required'],
        ];
    }
}
