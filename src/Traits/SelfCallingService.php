<?php

namespace PerfectOblivion\Services\Traits;

use PerfectOblivion\Services\ServiceCaller;

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
}
