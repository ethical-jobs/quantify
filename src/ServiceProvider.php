<?php

namespace EthicalJobs\Quantify;

use Illuminate\Queue;
use Illuminate\Support\Facades\Event;
use EthicalJobs\Quantify\Stores;
use EthicalJobs\Quantify\Reporters;

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
     * Is quantify enabled
     *
     * @var string
     */
    protected $enabled = true;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        if ($this->app->runningUnitTests() && config('quantify.testing-disabled')) {
            $this->enabled = false;
        }
    }    

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

        if ($this->enabled) {
            $this->listenToQueue();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom($this->configPath, 'quantify');

        if ($this->enabled) {
            $this->bindRedisStore();
        } else {
            $this->bindNullStore();
        }
    }

    /**
     * Register redis store
     * 
     * @return void
     */
    public function bindRedisStore() : void
    {
        $this->app->bind(Stores\Store::class, function ($app) {

            $redis = $app['redis'];

            return new Stores\RedisStore($redis);
        });
    }

    /**
     * Register null store
     * 
     * @return void
     */
    public function bindNullStore() : void
    {
        $this->app->bind(Stores\Store::class, function ($app) {
            return new Stores\NullStore;
        });
    }    

    /**
     * Setup queue event listeners
     * 
     * @return void
     */
    protected function listenToQueue() : void
    {
        Event::listen(Queue\Events\JobProcessing::class, function ($event) {
            $queueReporter = resolve(Reporters\Queues::class);
            $queueReporter->startQueueJob($event);
        });

        Event::listen(Queue\Events\JobProcessed::class, function ($event) {
            $queueReporter = resolve(Reporters\Queues::class);
            $queueReporter->completeQueueJob($event);
        });

        Event::listen(Queue\Events\JobFailed::class, function ($event) {
            $queueReporter = resolve(Reporters\Queues::class);
            $queueReporter->completeQueueJob($event);
        });

        Event::listen(Queue\Events\JobExceptionOccurred::class, function ($event) {
            $queueReporter = resolve(Reporters\Queues::class);
            $queueReporter->completeQueueJob($event);
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