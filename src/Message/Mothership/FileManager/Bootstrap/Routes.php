<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['files']->setPrefix('/files')->setParent('ms.cp');

		$router['files']->add('filemanager.listing', '/', '::Controller:Listing#index');

		$router['files']->add('filemanager.upload', '/', '::Controller:Upload#index')
			->setMethod('POST');

		$router['files']->add('filemanager.detail', '/{fileID}', '::Controller:Detail#index')
			   ->setRequirement('fileID', '\d+');

		$router['files']->add('filemanager.edit', '/{fileID}/edit', '::Controller:Detail#edit')
			   ->setRequirement('fileID', '\d+');

		$router['files']->add('filemanager.delete', '/{fileID}/delete', '::Controller:Detail#delete')
			   ->setRequirement('fileID', '\d+');
	}
}