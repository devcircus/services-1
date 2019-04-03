# Perfect Oblivion - Services
### A Service class implementation for Laravel Projects.

[![Latest Stable Version](https://poser.pugx.org/perfect-oblivion/services/version)](https://packagist.org/packages/perfect-oblivion/services)
[![Build Status](https://img.shields.io/travis/perfect-oblivion/services/master.svg)](https://travis-ci.org/perfect-oblivion/services)
[![Quality Score](https://img.shields.io/scrutinizer/g/perfect-oblivion/services.svg)](https://scrutinizer-ci.com/g/perfect-oblivion/services)
[![Total Downloads](https://poser.pugx.org/perfect-oblivion/services/downloads)](https://packagist.org/packages/perfect-oblivion/services)

![Perfect Oblivion](https://res.cloudinary.com/phpstage/image/upload/v1554128207/img/Oblivion.png "Perfect Oblivion")

### Disclaimer
The packages under the PerfectOblivion namespace exist to provide some basic functionality that I prefer not to replicate from scratch in every project. Nothing groundbreaking here.

### Inspiration
The PerfectOblivion Service package scratches an itch I've had for a while. I routinely use single-action controllers with [Responder Classes](https://github.com/perfect-oblivion/responders), in combination with Service classes for gathering/manipulating data. The Service class is modeled after Laravel's jobs/dispatcher. I prefer to handle the bulk of the domain work outside of the controller. It's not that much more work and it allows for this logic to be used outside the HTTP layer.

Example:
```php
namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Services\StoreNewTaskService;
use App\Http\Requests\StoreNewTaskRequest;
use App\Http\Responders\Task\StoreResponder;
use PerfectOblivion\Services\Traits\CallsServices; // the trait could be added to your parent Controller class

class Store extends Controller
{
    use CallsServices;

    /**
     * The service used to store a new task.
     *
     * @var \App\Http\Responders\Task\StoreResponder
     */
    private $responder;

    /**
     * Construct a new Store controller.
     *
     * @param  \App\Http\Responders\Task\StoreResponder  $responder
     */
    public function __construct(StoreResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Handle the task store action.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreTaskRequest $request)
    {
        $task = $this->call(StoreNewTaskService::class, ($request->validated()));
        // or
        // StoreNewTaskService::call($request->validated());

        return $this->responder->withPayload($task);
    }
}

```

My controllers simply defer to a Service to handle the dirty work, then, using [Responders](https://github.com/perfect-oblivion/responders), the response is sent. A very clean approach.

## Installation
You can install the package via composer. From your project directory, in your terminal, enter:
```bash
composer require perfect-oblivion/services
```

In Laravel > 5.6.0, the ServiceProvider will be automtically detected and registered.
If you are using an older version of Laravel, add the package service provider to your config/app.php file, in the 'providers' array:
```php
'providers' => [
    //...
    PerfectOblivion\Services\ServicesServiceProvider::class,
    //...
];
```

### Package Configuration
If you would like to change any of the package configuration options, run the following command in your terminal:
```bash
php artisan vendor:publish
```
and choose the 'PerfectOblivion/Services' option.

This will copy the package configuration (service-classes.php) to your 'config' folder.
See the configuration file below, for all options available:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | Set the namespace for Service classes.
    |
    */
    'namespace' => 'Services',

    /*
    |--------------------------------------------------------------------------
    | Suffixes
    |--------------------------------------------------------------------------
    |
    | Set the suffix for the Service classes.
    |
    */
    'suffix' => 'Service',

    /*
    |--------------------------------------------------------------------------
    | Method Name
    |--------------------------------------------------------------------------
    |
    | Set the method name for handling services.
    |
     */
    'method' => 'run',

    /*
    |--------------------------------------------------------------------------
    | Duplicate Suffixes
    |--------------------------------------------------------------------------
    |
    | If you have a Service suffix set in the config and try to generate a Service that also includes the suffix,
    | the package will recognize this duplication and rename the Service to remove the extra suffix.
    | This is the default behavior. To override and allow the duplication, change to false.
    |
    */
    'override_duplicate_suffix' => true,
];
```

## Usage
Once the package is installed and the config is copied (optionally), you can begin generating your Services.

### Generating a Service class
From inside your project directory, in your terminal, run:

```bash
php artisan adr:service StoreNewTask
```

Based on the configuration options above, this will create an 'App\Services\StoreNewTaskService' class.

> Note, by default, the 'run' method will be called when you 'call' your service. You can change this method name in the configuration file.

Example Service class:

```php
// Service Class
namespace App\Services;

use App\Models\Repositories\TaskRepository;
use PerfectOblivion\Common\Payloads\Payload;

class StoreNewTaskService
{
    /**
     * The parameters for building a new Task.
     *
     * @var array
     */
    public $repo;

    /**
     * Construct a new StoreNewTaskService.
     *
     * @param  \App\Models\Repositories\TaskRepository  $repo
     */
    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Handle the call to the service.
     *
     * @param  mixed  $params
     *
     * @return mixed
     */
    public function run($params)
    {
        return $this->repo->create($params);
    }
}
```
As in the example above, simply typehint any dependencies on the Service constructor. These dependencies will be resolved by Laravel from the container. Any parameters passed when calling the service, will be passed to the "run" method of the service.

### How to call Services
At this time, there are several options for calling a service.

First, you may use the included 'CallsServices' trait:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;
use PerfectOblivion\Services\Traits\CallsServices;

class StoreTaskController extends Controller
{
    use CallsServices;

    public function store(Request $request)
    {
        $task = $this->call(StoreNewTaskService::class, ($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```

The next option is to include the ServiceCaller via dependency injection, the use the "call" method:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;
use PerfectOblivion\Services\ServiceCaller;

class StoreTaskController extends Controllerr
{
    private $caller;

    public function __construct(ServiceCaller $caller)
    {
        $this->caller = $caller;
    }

    public function store(Request $request)
    {
        $task = $this->caller->call(StoreNewTaskService::class, ($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```

Next, the service has the ability to call itself:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;

class StoreTaskController extends Controller
{
    public function store(Request $request)
    {
        $task = StoreNewTaskService::call($request->all());

        return view('tasks.show', ['task' => $task]);
    }
}
```

Last, and probably the most common way, is to inject the Service class in your Controller or Action, and call the 'run' method:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;

class StoreTaskController extends Controllerr
{
    private $service;

    public function __construct(StoreNewTaskService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $task = $this->service->run($request->all());

        return view('tasks.show', ['task' => $task]);
    }
}
```

### Note on Service class parameters
When calling your service class, you may pass multiple parameters:
```php
$this->call(MyService::class, $params, $anotherParam);
// or
$this->serviceCaller->call(MyService::class, $params, $anotherParam):
// or
MyService::call($params, $anotherParam);
// or
$service->run($params, $anotherParam);
```

I've found that usually, one array of parameters is sufficient, but you may have cases where you need to pass another parameter. Simply add these parameters when you call the Service, and these parameters will be passed to the 'run' method of your service. Be sure the 'run' method parameters match the arguments used, when the service is called:
```php
// MyServiceClass

public function run($data, $mystring, $anotherString)
{
    //
}

// In your controller
MyService::call($params, $string1, $string2);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email clay@phpstage.com instead of using the issue tracker.

## Roadmap

We plan to work on flexibility/configuration soon, as well as release a framework agnostic version of the package.

## Credits

- [Clayton Stone](https://github.com/devcircus)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
