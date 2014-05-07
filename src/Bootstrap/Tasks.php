<?php

namespace Message\Mothership\FileManager\Bootstrap;

use Message\Cog\Bootstrap\TasksInterface;
use Message\Mothership\FileManager\Task;

class Tasks implements TasksInterface
{
	public function registerTasks($tasks)
	{
		$tasks->add(new Task\SyncFiles('file_manager:sync_files'), 'Checks files in public/files directory and adds to system');

	}
}