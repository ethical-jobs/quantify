<?php

namespace EthicalJobs\Quantify;

use EthicalJobs\Quantify\Stores;

/**
 * Service provider
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Config file path
     *
     * @var string
     */
    protected $configPath = __DIR__.'/../config/quantify.php';  

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->publishes([
            $this->configPath => config_path('quantify.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom($this->configPath, 'quantify');        

        $this->registerStores();
    }

    /**
     * Register mertic stores
     * 
     * @return void
     */
    public function registerStores() : void
    {
        $this->app->bind(Stores\Store::class, function ($app) {

            $redis = $app['redis'];

            return new Stores\RedisStore($redis);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() : array
    {
        return [
            Stores\Store::class,
        ];
    }            
}