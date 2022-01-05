<?php

namespace Plugins\PluginMarket\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class PluginMarketServiceProvider extends ServiceProvider
{
    /**
     * @var string $pluginName
     */
    protected string $pluginName = 'PluginMarket';

    /**
     * @var string $pluginNameLower
     */
    protected string $pluginNameLower = 'pluginmarket';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            plugin_path($this->pluginName, 'Config/config.php') => config_path($this->pluginNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            plugin_path($this->pluginName, 'Config/config.php'), $this->pluginNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/plugins/' . $this->pluginNameLower);

        $sourcePath = plugin_path($this->pluginName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->pluginNameLower . '-plugin-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->pluginNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/plugins/' . $this->pluginNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->pluginNameLower);
        } else {
            $this->loadTranslationsFrom(plugin_path($this->pluginName, 'Resources/lang'), $this->pluginNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/plugins/' . $this->pluginNameLower)) {
                $paths[] = $path . '/plugins/' . $this->pluginNameLower;
            }
        }
        return $paths;
    }
}
