<?php

namespace PerfectOblivion\Services\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use PerfectOblivion\Services\QueuedService;
use PerfectOblivion\Services\ServiceCaller;
use PerfectOblivion\Services\Exceptions\ServiceHandlerMethodException;

trait CallsServices
{
    /**
     * Call a service.
     *
     * @param  string  $service
     * @param  mixed  ...$params
     *
     * @return mixed
     */
    public function call(string $service, ...$params)
    {
        return Container::getInstance()->make(ServiceCaller::class)->call($service, ...$params);
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
    public function queue(string $service, ...$params)
    {
        if (! Container::getInstance()->make(ServiceCaller::class)->hasHandler($service)) {
            throw ServiceHandlerMethodException::notFound($service);
        }

        return resolve(Dispatcher::class)->dispatch(
            new QueuedService(Container::getInstance()->make($service), $params)
        );
    }
}
