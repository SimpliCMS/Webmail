<?php

namespace Modules\Webmail\Providers;

use Illuminate\Support\ServiceProvider;
use Konekt\Gears\Defaults\SimpleSetting;
use Konekt\Gears\Facades\Settings;
use Konekt\Gears\UI\TreeBuilder;

class WebmailSettingsServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot() {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

}
