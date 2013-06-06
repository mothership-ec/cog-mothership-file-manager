<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['files']->setPrefix('/file')->setParent('ms.cp');

		$router['files']->add('ms.file_manager.listing', '/', '::Controller:Listing#index');

		$router['files']->add('ms.file_manager.upload', '/', '::Controller:Upload#index')
			->setMethod('POST');

		$router['files']->add('ms.file_manager.detail', '/{fileID}', '::Controller:Detail#index')
			->setRequirement('fileID', '\d+');

		$router['files']->add('ms.file_manager.edit', '/{fileID}/edit', '::Controller:Detail#edit')
			->setRequirement('fileID', '\d+');

		$router['files']->add('ms.file_manager.delete', '/{fileID}/delete', '::Controller:Detail#delete')
			->setRequirement('fileID', '\d+');
	}
}