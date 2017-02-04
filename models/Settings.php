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
        'quality' => 'required|numeric|min:1|max:100',

        'is_change_width' => 'boolean',
        'max_width' => 'required|numeric|min:1',

        'is_change_height' => 'boolean',
        'max_height' => 'required|numeric|min:1',
    ];

    public function initSettingsData()
    {
        $this->is_change_quality = false;
        $this->quality = 75;

        $this->is_change_size = false;
        $this->max_width = 800;
        $this->max_height = 600;
    }

    public function getQualityOptions() {
        return [
            1 => '1 - very low',
            10 => '10',
            20 => '20',
            25 => '25 - low',
            30 => '30',
            40 => '40',
            50 => '50 - normal',
            60 => '60',
            70 => '70',
            75 => '75 - recommended',
            80 => '80',
            90 => '90 - very high',
            100 => '100 - not compress',
        ];
    }
}