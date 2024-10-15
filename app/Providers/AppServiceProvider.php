<?php

namespace MMVUEJS\Providers;

use MMVUEJS\Controllers\AdminController;
use MMVUEJS\Controllers\RestApiController;

class AppServiceProvider {
    /**
     * Get all the services to be registered.
     *
     * @return string[] Full list of service classes
     */
    public static function getServices(): array {
        return [
            AdminController::class,
            RestApiController::class,
        ];
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function registerServices(): void {
        foreach (self::getServices() as $class) {
            $service = $this->instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Instantiate the service class.
     *
     * @param string $class
     * @return object
     */
    private function instantiate(string $class): object {
        return new $class();
    }
}