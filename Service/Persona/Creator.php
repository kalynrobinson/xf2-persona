<?php

namespace Shinka\Persona\Service\Persona;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/** @var User */
	protected $parent_id;

	/** @var int */
	protected $persona_id;

	public function __construct(\XF\App $app, $parent_id, $persona_id, $approved = false)
	{
		parent::__construct($app);
		
		$this->createPersona($parent_id, $persona_id, $approved);
	}
	
	public function createPersona($parent_id, $persona_id, $approved)
	{
		$persona = $this->em()->find('Shinka\Persona:Persona', [$parent_id, $persona_id]);

		if (!$persona) {
			$persona = $this->em()->create('Shinka\Persona:Persona');
			$persona->parent_id = $parent_id;
			$persona->persona_id = $persona_id;
		}
		$persona->approved = $approved;
        $this->persona = $persona;
	}

	protected function _validate()
	{
		$this->persona->preSave();
		return $this->persona->getErrors();
	}

	protected function _save()
	{
		$this->persona->save();
		return $this->persona;
	}
}