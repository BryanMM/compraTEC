<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit538f7ac4214ce573bc0bbbd9f9dfcba4
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GraphQL\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GraphQL\\' => 
        array (
            0 => __DIR__ . '/..' . '/webonyx/graphql-php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit538f7ac4214ce573bc0bbbd9f9dfcba4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit538f7ac4214ce573bc0bbbd9f9dfcba4::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
