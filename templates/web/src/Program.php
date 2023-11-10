<?php

namespace Application;

use DevNet\Web\Extensions\ApplicationBuilderExtensions;
use DevNet\Web\Extensions\ServiceCollectionExtensions;
use DevNet\Web\Hosting\WebHost;

class Program
{
    public static function main(array $args = [])
    {
        $builder = WebHost::createDefaultBuilder($args);
        $configuration = $builder->Configuration;

        $builder->register(function ($services) use ($configuration) {
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
