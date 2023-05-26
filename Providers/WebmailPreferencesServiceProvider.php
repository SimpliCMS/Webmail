<?php

namespace Modules\Webmail\Providers;

use Illuminate\Support\ServiceProvider;
use Konekt\Gears\Registry\PreferencesRegistry;
use \Konekt\Gears\Defaults\SimplePreference;

class WebmailPreferencesServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot() {
        // Your boot logic here
                $prefsRegistry = app('gears.preferences_registry');
        $prefsRegistry->addByKey('webmail.mail_host');
        $prefsRegistry->addByKey('webmail.mail_port');
        $prefsRegistry->addByKey('webmail.mail_username');
        $prefsRegistry->addByKey('webmail.mail_password');

        $prefsRegistry->add(new SimplePreference('webmail.mail_host'));
        $prefsRegistry->add(new SimplePreference('webmail.mail_port'));
        $prefsRegistry->add(new SimplePreference('webmail.mail_username'));
        $prefsRegistry->add(new SimplePreference('webmail.mail_password'));

        $prefsTreeBuilder = $this->app['appshell.preferences_tree_builder'];
        $prefsTreeBuilder->addChildNode('general', 'webmail', __('Webmail'))
                ->addPreferenceItem(
                        'webmail',
                        ['text', ['label' => __('Mail Host')]], 'webmail.mail_host')
                ->addPreferenceItem(
                        'webmail',
                        ['text', ['label' => __('Mail Port')]], 'webmail.mail_port')
                ->addPreferenceItem(
                        'webmail',
                        ['text', ['label' => __('Mail Username')]], 'webmail.mail_username')
                ->addPreferenceItem(
                        'webmail',
                        ['text', ['label' => __('Mail Password')]], 'webmail.mail_password');
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
