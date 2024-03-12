<?php

namespace Application;

use DevNet\Core\Extensions\ApplicationBuilderExtensions;
use DevNet\Core\Extensions\ServiceCollectionExtensions;
use DevNet\Core\Hosting\WebHost;

class Program
{
    public static function main(array $args = [])
    {
        $builder = WebHost::createDefaultBuilder($args);
        $builder->register(function ($services) {
            // Services
        });

        $host = $builder->build();
        $host->start(function ($app) {
            // Middlewares
            $app->useRouter();
            $app->useEndpoint(function ($routes) {
                // Routes
                $routes->mapGet("/", fn () => "Hello World!");
            });
        });
    }
}
