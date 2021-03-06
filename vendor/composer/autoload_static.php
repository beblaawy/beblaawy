<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit939f58a2e03251c5c94a71d3d059861c
{
    public static $classMap = array (
        'BaseFacebook' => __DIR__ . '/..' . '/facebook/php-sdk/src/base_facebook.php',
        'Facebook' => __DIR__ . '/..' . '/facebook/php-sdk/src/facebook.php',
        'FacebookApiException' => __DIR__ . '/..' . '/facebook/php-sdk/src/base_facebook.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit939f58a2e03251c5c94a71d3d059861c::$classMap;

        }, null, ClassLoader::class);
    }
}
