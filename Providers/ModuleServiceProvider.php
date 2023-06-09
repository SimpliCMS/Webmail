<?php

namespace Modules\Webmail\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Illuminate\Support\Facades\DB;
use Schema;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The namespace for the module's models.
     *
     * @var string
     */
    protected $modelNamespace = 'Modules\Webmail\Models';

    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
         parent::boot();
        // Your module's boot logic here
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AdminMenuServiceProvider::class);
        $this->app->register(PluginServiceProvider::class);
        $this->app->register(WebmailPreferencesServiceProvider::class);
        $this->app->register(WebmailSettingsServiceProvider::class);
        $this->ViewPaths();
        $this->adminViewPaths();
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        // Your module's register logic here
    }
    
    public function ViewPaths() {
        $moduleLower = lcfirst('Webmail');
        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->where('id', 'site.theme')->first();
            $currentTheme = $setting->value;
        } else {
            $currentTheme = 'default';
        }
        $views = [
            base_path("themes/$currentTheme/views/modules/Webmail"),
            module_Viewpath('Webmail', $currentTheme),
            base_path("themes/default/views/modules/Webmail"),
            module_Viewpath('Webmail', 'default'),
            base_path("resources/views/modules/Webmail"),
        ];

        return $this->loadViewsFrom($views, $moduleLower);
    }

    public function adminViewPaths() {
        $moduleLower = lcfirst('Webmail');
        $currentTheme = 'admin';
        $views = [
            module_Viewpath('Webmail', $currentTheme),
            base_path("themes/$currentTheme/views/modules/Webmail"),
        ];

        return $this->loadViewsFrom($views, $moduleLower.'-admin');
    }
}

