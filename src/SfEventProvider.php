<?php

namespace OkamiChen\SymfonyEvent;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SfEventProvider extends ServiceProvider
{

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

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sfevent', EventDispatcher::class);
    }


    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $this->app->make('sfevent')->addListener($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            $this->app->make('sfevent')->addSubscriber($subscriber);
        }
    }
}
