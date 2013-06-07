<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['files']->setPrefix('/files')->setParent('ms.cp');

		$router['files']->add('ms.cp.file_manager.listing', '/', '::Controller:Listing#index');

		$router['files']->add('ms.cp.file_manager.search.forward', '/search', '::Controller:Listing#searchRedirect')
			->setMethod('POST');

		$router['files']->add('ms.cp.file_manager.search', '/search/{term}', '::Controller:Listing#search');

		$router['files']->add('ms.cp.file_manager.upload', '/', '::Controller:Upload#index')
			->setMethod('POST');

		$router['files']->add('ms.cp.file_manager.detail', '/{fileID}', '::Controller:Detail#index')
			->setRequirement('fileID', '\d+');

		$router['files']->add('ms.cp.file_manager.edit', '/{fileID}/edit', '::Controller:Detail#edit')
			->setRequirement('fileID', '\d+')
			->setMethod('POST');

		$router['files']->add('ms.cp.file_manager.delete', '/{fileID}/delete', '::Controller:Detail#delete')
			->setRequirement('fileID', '\d+')
			->setMethod('DELETE');

		$router['files']->add('ms.cp.file_manager.restore', '/{fileID}/restore', '::Controller:Detail#restore')
			->setRequirement('fileID', '\d+')
			->setMethod('GET');
	}
}