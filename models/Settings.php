<?php namespace PopcornPHP\ImageCompress\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;

class Settings extends Model
{
    public $implement = [
        'System.Behaviors.SettingsModel',
    ];

    public $settingsCode = 'compress_settings';
    public $settingsFields = 'fields.yaml';

    use Validation;
    public $rules = [
        'is_change_quality' => 'boolean',
        'quality'           => 'required|numeric|min:1|max:100',
        'resize_mode'       => 'in:auto,crop,exact,portrait,landscape',
        'is_change_width'   => 'boolean',
        'max_width'         => 'required|numeric|min:1',
        'is_change_height'  => 'boolean',
        'max_height'        => 'required|numeric|min:1',
        'is_make_enlarge'   => 'boolean',
    ];

    public function initSettingsData()
    {
        $this->is_change_quality = false;
        $this->quality = 75;

        $this->resize_mode = 'auto';
        $this->is_change_width = false;
        $this->max_width = 800;
        $this->is_change_height = false;
        $this->max_height = 600;
        $this->is_make_enlarge = false;
    }
}