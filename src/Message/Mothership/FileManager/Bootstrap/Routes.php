<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router->add('filemanager.upload', '/files/view', '::Controller:Listing#upload')->setMethod('POST');
		$router->add('filemanager.listing', '/files/view', '::Controller:Listing#index');

		$router->add('filemanager.detail', '/files/detail/{fileID}', '::Controller:Detail#index')
			   ->setRequirement('fileID', '\d+');

		$router->add('filemanager.edit', '/files/detail/{fileID}/edit', '::Controller:Detail#edit')
			   ->setRequirement('fileID', '\d+');
	}
}