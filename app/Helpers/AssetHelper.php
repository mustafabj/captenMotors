<?php

namespace App\Helpers;

class AssetHelper
{
    /**
     * Get versioned asset URL
     */
    public static function versioned($path, $version = null)
    {
        if ($version === null) {
            // Check for specific asset version first
            $version = config("assets.versions.{$path}", config('assets.version', '1.0.0'));
        }
        
        // In development mode, use timestamp for cache busting
        if (config('assets.development_mode', false)) {
            $version = time();
        }
        
        return asset($path) . '?v=' . $version;
    }

    /**
     * Get versioned JavaScript URL
     */
    public static function js($path, $version = null)
    {
        return self::versioned('js/' . $path, $version);
    }

    /**
     * Get versioned CSS URL
     */
    public static function css($path, $version = null)
    {
        return self::versioned('css/' . $path, $version);
    }

    /**
     * Get versioned image URL
     */
    public static function image($path, $version = null)
    {
        return self::versioned('images/' . $path, $version);
    }
} 