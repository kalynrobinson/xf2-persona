<?php

namespace Shinka\Persona;

use Shinka\Persona\Constants;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	/**
	 * Creates user-persona join table.
	 *
	 * @return void
	 */
    public function installStep1()
    {
		Constants::tableSchema()->create();
	}

	/**
	 * Drops user-persona table.
	 *
	 * @return void
	 */
	public function uninstallStep1()
	{
		Constants::tableSchema()->drop();
	}
}