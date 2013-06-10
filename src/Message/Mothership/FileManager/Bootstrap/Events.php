<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Mothership\ControlPanel\Event\Event;

use Message\Cog\Bootstrap\EventsInterface;

class Events implements EventsInterface
{
	public function registerEvents($dispatcher)
	{
		$dispatcher->addListener(Event::BUILD_MAIN_MENU, function($event) {
			$event->addItem('ms.cp.file_manager.listing', 'File Manager', array(
				'ms.cp.file_manager.listing',
				'ms.cp.file_manager.search.forward',
				'ms.cp.file_manager.search',
				'ms.cp.file_manager.upload',
				'ms.cp.file_manager.detail',
				'ms.cp.file_manager.edit',
				'ms.cp.file_manager.delete',
				'ms.cp.file_manager.restore',
			));
		});
	}
}