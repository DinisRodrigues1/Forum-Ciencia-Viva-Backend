<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6afe69ed6f643f40f119630ece3e9d21
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Slim\\Middleware\\' => 16,
            'Slim\\' => 5,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Container\\' => 14,
        ),
        'I' => 
        array (
            'Interop\\Container\\' => 18,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
        'D' => 
        array (
            'Dflydev\\FigCookies\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Slim\\Middleware\\' => 
        array (
            0 => __DIR__ . '/..' . '/dyorg/slim-token-authentication/src',
        ),
        'Slim\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/slim/Slim',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
        'Dflydev\\FigCookies\\' => 
        array (
            0 => __DIR__ . '/..' . '/dflydev/fig-cookies/src/Dflydev/FigCookies',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static $classMap = array (
        'app\\Auth' => __DIR__ . '/../..' . '/app/Auth.php',
        'app\\UnauthorizedException' => __DIR__ . '/../..' . '/app/UnauthorizedException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6afe69ed6f643f40f119630ece3e9d21::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6afe69ed6f643f40f119630ece3e9d21::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit6afe69ed6f643f40f119630ece3e9d21::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit6afe69ed6f643f40f119630ece3e9d21::$classMap;

        }, null, ClassLoader::class);
    }
}