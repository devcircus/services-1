<?php

namespace PerfectOblivion\Services\Tests\Integration;

use Illuminate\Support\Facades\Artisan;

class ServiceCommandTest extends IntegrationTestCase
{
    use TestsCommands;

    /** @test */
    public function bright_service_command_makes_service_with_correct_methods()
    {
        Artisan::call('adr:service', ['name' => 'MyService']);

        include_once base_path().'/app/Services/MyService.php';

        $this->assertMethodExists(\App\Services\MyService::class, '__construct');
        $this->assertMethodExists(\App\Services\MyService::class, 'run');
    }
}
