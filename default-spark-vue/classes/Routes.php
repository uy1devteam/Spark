<?php

class Routes  {
	private $routes;

	public function __construct() {
		$this->routes = [
			'' => [
				'modules' => ['main'],
				'dependencies' => []
			],
			'404' => [
				'modules' => ["404"]
			],
			'500' => [
				'modules' => ["500"]
			]
		];
	}

	public function getRoutes(): array {

		return $this->routes;
	}

}
