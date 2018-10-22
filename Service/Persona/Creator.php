<?php

namespace Shinka\Persona\Service\Persona;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/** @var \Shinka\Persona\Entity\Persona */
	protected $persona;

	/**
	 * Creates persona with given information.
	 * 
	 * @param \XF\App $app
	 * @param int     $parent_id
	 * @param int     $user_id
	 * @param boolean $approved
	 * @return void
	 */
	public function __construct(\XF\App $app, $parent_id, $user_id, $approved = false)
	{
		parent::__construct($app);
		
		$this->createPersona($parent_id, $user_id, $approved);
	}
	
	/**
	 * Creates persona with given information.
	 * 
	 * @param int     $parent_id
	 * @param int     $user_id
	 * @param boolean $approved
	 * @return void
	 */
	public function createPersona($parent_id, $user_id, $approved)
	{
		$persona = $this->em()->findOne('Shinka\Persona:Persona', [
			"parent_id" => $parent_id, 
			"user_id" =>$user_id
		]);

		if (!$persona) {
			$persona = $this->em()->create('Shinka\Persona:Persona');
			$persona->parent_id = $parent_id;
			$persona->user_id = $user_id;
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