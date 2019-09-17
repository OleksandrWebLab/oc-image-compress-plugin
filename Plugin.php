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
            'description' => 'Native compress and resize for images',
            'author'      => 'Alexander Shapoval',
            'icon'        => 'icon-compress',
            'homepage'    => 'https://popcornphp.github.io',
        ];
    }

    public function boot()
    {
        File::extend(function ($model) {
            $model->bindEvent('model.beforeCreate', function () use ($model) {
                if ($model->isImage()) {
                    $isChangeQuality = ImageCompressSettings::get('is_change_quality');
                    $isChangeWidth = ImageCompressSettings::get('is_change_width');
                    $isChangeHeight = ImageCompressSettings::get('is_change_height');
                    $isMakeEnlarge = ImageCompressSettings::get('is_make_enlarge', false);

                    if (
                        $isChangeQuality == true ||
                        $isChangeWidth == true ||
                        $isChangeHeight == true
                    ) {
                        /**
                         * Prepare
                         */
                        $filePath = $model->getLocalPath();

                        $options = [];
                        $maxWidth = false;
                        $maxHeight = false;
                        list($originalWidth, $originalHeight) = getimagesize($filePath);

                        $image = Resizer::open($filePath);

                        /**
                         * Set options (set quality and mode resize)
                         */
                        if ($isChangeQuality == true) {
                            $options['quality'] = ImageCompressSettings::get('quality');
                        }

                        if ($isChangeWidth == true || $isChangeHeight == true) {
                            $options['mode'] = ImageCompressSettings::get('resize_mode');
                        }

                        $image->setOptions($options);

                        /**
                         * Set width and height
                         */
                        if ($isChangeWidth == true) {
                            $maxWidth = ImageCompressSettings::get('max_width');
                        }

                        if ($isChangeHeight == true) {
                            $maxHeight = ImageCompressSettings::get('max_height');
                        }

                        if (
                            ($isChangeWidth == true || $isChangeHeight == true) &&
                            ($isMakeEnlarge == true || $originalWidth > $maxWidth || $originalHeight > $maxHeight)
                        ) {
                            $image->resize($maxWidth, $maxHeight);
                        }

                        /**
                         * Save image, set new size of file
                         */
                        $image->save($filePath);

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
                'permissions' => ['popcornphp.imagecompress.access_settings'],
                'order'       => 500,
                'keywords'    => 'images compress',
            ],
        ];
    }
    
    public function registerPermissions() {
        return [
            'popcornphp.imagecompress.access_settings' => ['tab' => 'Image Compress', 'label' => 'Access to Image Compress settings'],
        ];
    }
}
