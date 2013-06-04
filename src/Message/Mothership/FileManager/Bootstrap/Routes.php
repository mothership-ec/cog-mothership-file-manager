<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router->add('filemanager.listing', '/files/view', '::Controller:Listing#index');
	}
}