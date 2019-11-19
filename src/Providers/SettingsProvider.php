<?php

namespace Irony\Github\Upload\Providers;

use Irony\Github\Upload\Helpers\Settings;
use Flarum\Foundation\AbstractServiceProvider;

class SettingsProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->app->singleton(Settings::class);
    }
}
