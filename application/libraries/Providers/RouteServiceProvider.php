<?php namespace NaassonTeam\LogViewer\Providers;

use NaassonTeam\LogViewer\Http\Routes\LogViewerRoute;
use Arcanedev\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class     RouteServiceProvider
 *
 * @package  NaassonTeam\LogViewer\Providers
 * @author   NaassonTeam <info@naasson.com>
 */
class RouteServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get Route attributes
     *
     * @return array
     */
    public function routeAttributes()
    {
        return array_merge($this->config('attributes', []), [
            'namespace' => 'NaassonTeam\\LogViewer\\Http\\Controllers',
        ]);
    }

    /**
     * Check if routes is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config('enabled', false);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        if ($this->isEnabled()) {
            static::mapRouteClasses([
                LogViewerRoute::class,
            ]);
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get config value by key
     *
     * @param  string      $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    private function config($key, $default = null)
    {
        return $this->app['config']->get("log-viewer.route.$key", $default);
    }
}
