<?php

namespace PerfectOblivion\Services;

use Illuminate\Contracts\Container\Container;

abstract class AbstractServiceCaller
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The handler method to be called.
     *
     * @var string
     */
    public static $handlerMethod = 'run';

    /**
     * Create a new service caller instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Call a service through its appropriate handler.
     *
     * @param  string  $service
     * @param  mixed  ...$params
     *
     * @return mixed
     */
    abstract public function call($service, ...$params);

    /**
     * Determine if the service handler method exists.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    public function hasHandler($service)
    {
        return method_exists($service, $this::$handlerMethod);
    }

    /**
     * Set the handler method name for services.
     *
     * @param  string  $method
     */
    public static function setHandlerMethod(string $method = 'run')
    {
        static::$handlerMethod = $method;
    }
}
