<?php

namespace PerfectOblivion\Services\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use PerfectOblivion\Services\QueuedService;
use PerfectOblivion\Services\ServiceCaller;
use PerfectOblivion\Services\Exceptions\ServiceHandlerMethodException;

trait SelfCallingService
{
    /**
     * Run the service.
     *
     * @return mixed
     */
    public static function call()
    {
        return app(ServiceCaller::class)->call(static::class, ...func_get_args());
    }

    /**
     * Push the service call to the queue.
     *
     * @return mixed
     */
    public static function queue()
    {
        if (! app(ServiceCaller::class)->hasHandler(static::class)) {
            throw ServiceHandlerMethodException::notFound(static::class);
        }

        return resolve(Dispatcher::class)->dispatch(
            new QueuedService(Container::getInstance()->make(static::class), func_get_args())
        );
    }
}
