<?php

namespace MMVUEJS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Init {
	/**
	 * Store all the classes inside an array
	 * @return string[] Full list of classes
	 */
	public static function getServices(): array {
		return [
			AdminController::class,
			RestApiController::class,
		];
	}

	/**
	 * Register services
	 * @return void
	 */
	public static function registerServices(): void {
		foreach ( self::getServices() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	private static function instantiate( $class ) {
		return new $class();
	}
}