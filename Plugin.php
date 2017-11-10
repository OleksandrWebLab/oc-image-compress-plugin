<?php namespace PopcornPHP\ImageCompress;

use System\Classes\PluginBase;
use October\Rain\Database\Attach\File;
use October\Rain\Database\Attach\Resizer;
use PopcornPHP\ImageCompress\Models\Settings as ImageCompressSettings;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'ImageCompress',
            'description' => 'Simple compress for images',
            'author'      => 'Alexander Shapoval',
            'icon'        => 'icon-compress',
            'homepage'    => 'https://popcornphp.github.io'
        ];
    }

    public function boot()
    {
        File::extend(function ($model) {
            $model->bindEvent('model.beforeCreate', function () use ($model) {
                if (
                    $model->getContentType() == 'image/gif' ||
                    $model->getContentType() == 'image/png' ||
                    $model->getContentType() == 'image/jpeg' ||
                    $model->getContentType() == 'image/webp'
                ) {
                    $is_change_quality = ImageCompressSettings::get('is_change_quality');
                    $is_change_width = ImageCompressSettings::get('is_change_width');
                    $is_change_height = ImageCompressSettings::get('is_change_height');

                    if (
                        $is_change_quality == true ||
                        $is_change_width == true ||
                        $is_change_height == true
                    ) {
                        $options = [];
                        $width = false;
                        $height = false;

                        if ($is_change_quality == true) {
                            $options['quality'] = ImageCompressSettings::get('quality');
                        }

                        if ($is_change_width == true || $is_change_height == true) {
                            $options['mode'] = ImageCompressSettings::get('resize_mode');
                        }

                        if ($is_change_width == true) {
                            $width = ImageCompressSettings::get('max_width');
                        }

                        if ($is_change_height == true) {
                            $height = ImageCompressSettings::get('max_height');
                        }

                        $filePath = $model->getLocalPath();

                        Resizer::open($filePath)
                            ->resize($width, $height, $options)
                            ->save($filePath);

                        clearstatcache();

                        $model->file_size = filesize($filePath);
                    }
                }
            });
        });
    }

    public function registerSettings()
    {
        return [
            'compress' => [
                'label'       => 'Compress images',
                'description' => 'Image compression management',
                'category'    => 'system::lang.system.categories.system',
                'icon'        => 'icon-compress',
                'class'       => 'PopcornPHP\ImageCompress\Models\Settings',
                'order'       => 500,
                'keywords'    => 'images compress'
            ]
        ];
    }
}