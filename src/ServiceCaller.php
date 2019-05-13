<?php

namespace PerfectOblivion\Services;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use PerfectOblivion\Services\Exceptions\ServiceHandlerMethodException;

class ServiceCaller extends AbstractServiceCaller
{
    /**
     * Call a service through its appropriate handler.
     *
     * @param  string  $service
     * @param  mixed  ...$params
     *
     * @return mixed
     */
    public function call($service, ...$params)
    {
        if (! $this->hasHandler($service)) {
            throw ServiceHandlerMethodException::notFound($service);
        }

        return $this->container->make($service)->{$this::$handlerMethod}(...$params);
    }

    /**
     * Push the service call to the queue..
     *
     * @param  string  $service
     * @param  mixed  ...$params
     *
     * @throws \PerfectOblivion\Services\Exceptions\ServiceHandlerMethodException
     *
     * @return mixed
     */
    public function queue($service, ...$params)
    {
        if (! $this->hasHandler($service)) {
            throw ServiceHandlerMethodException::notFound($service);
        }

        return resolve(Dispatcher::class)->dispatch(
            new QueuedService(Container::getInstance()->make($service), $params)
        );
    }
}
