<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9cb98a3f90dd7f6afddb41797efec16b
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MMVUEJS\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MMVUEJS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9cb98a3f90dd7f6afddb41797efec16b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9cb98a3f90dd7f6afddb41797efec16b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9cb98a3f90dd7f6afddb41797efec16b::$classMap;

        }, null, ClassLoader::class);
    }
}
