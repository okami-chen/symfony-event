<?php

namespace OkamiChen\SymfonyEvent;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

class SfEventProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    public function provides()
    {
        return ['sfevent'];
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('sfevent', function () {

            $logger = null;

            if ($this->app['config']['app']['debug']) {
                $channel = $this->app['config']['sfevent']['logger'] ?? 'daily';
                $logger = logger()->stack([$channel]);
            }

            return new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch(), $logger);
        });
    }


    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'symfony-event');
        }


        $row = config('sfevent');

        /**
         * @var $sfevent TraceableEventDispatcher
         */
        $sfevent = $this->app->make('sfevent');

        if (count(Arr::get($row, 'listener', []))) {
            foreach ($row['listener'] as $event => $listeners) {
                foreach (array_unique($listeners) as $listener) {
                    $sfevent->addListener($event, (new $listener));
                }
            }
        }

        if (count(Arr::get($row, 'subscriber', []))) {
            foreach ($row['subscriber'] as $subscriber) {
                $sfevent->addSubscriber((new $subscriber));
            }
        }

    }
}
