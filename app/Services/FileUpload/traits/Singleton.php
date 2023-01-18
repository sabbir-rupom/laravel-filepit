<?php

namespace App\Services\FileUpload\Traits;

trait Singleton
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
