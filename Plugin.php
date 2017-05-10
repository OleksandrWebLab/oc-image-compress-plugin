<?php namespace PopcornPHP\ImageCompress;

use PopcornPHP\ImageCompress\Models\Settings;
use System\Classes\PluginBase;
use October\Rain\Database\Attach\File;
use October\Rain\Database\Attach\Resizer;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'ImageCompress',
            'description' => 'Simple compress for images',
            'author' => 'Alexander Shapoval',
            'icon' => 'icon-compress',
            'homepage' => 'https://github.com/PopcornPHP/oc-imagecompress-plugin'
        ];
    }

    public function boot()
    {
        File::extend(function ($model) {
            $model->bindEvent('model.afterSave', function () use ($model) {
                if (
                    $model->getContentType() == 'image/gif' ||
                    $model->getContentType() == 'image/png' ||
                    $model->getContentType() == 'image/jpeg'
                ) {
                    $is_change_quality = Settings::get('is_change_quality');
                    $is_change_size = Settings::get('is_change_size');
                    
                    if (
                        $is_change_quality == true ||
                        $is_change_size == true
                    ) {
                        $width = false;
                        $height = false;

                        $options = [
                            'quality' => 100,
                        ];

                        if ($is_change_quality == true) {
                            $options['quality'] = Settings::get('quality');
                        }

                        if ($is_change_size == true) {
                            $width = Settings::get('max_width');
                            $height = Settings::get('max_height');
                        }

                        $filePath = storage_path() . '/app/' . $model->getDiskPath();
                        Resizer::open($filePath)
                            ->resize($width, $height, $options)
                            ->save($filePath);
                    }
                }
            });
        });
    }

    public function registerSettings()
    {
        return [
            'compress' => [
                'label' => 'Compress images',
                'description' => 'Image compression management',
                'category' => 'system::lang.system.categories.system',
                'icon' => 'icon-compress',
                'class' => 'PopcornPHP\ImageCompress\Models\Settings',
                'order' => 500,
                'keywords' => 'images'
            ]
        ];
    }
}
