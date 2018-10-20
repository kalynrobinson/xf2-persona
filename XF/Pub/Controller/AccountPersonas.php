<?php

namespace Shinka\Persona\XF\Pub\Controller;

class AccountPersonas extends \XF\Pub\Controller\Account
{
    public function actionPersonas($params)
    {
        $visitor = \XF::visitor();

        $viewParams = [];
        return $this->view('Shinka\Persona:View', 'shinka_persona_account_view', $viewParams);
    }
    
	public function actionDetachPersona()
	{
        $persona_id = $this->filter('persona_id', 'uint');
        $persona = \XF::visitor()->personaPivots->filter(
            function ($persona) use ($persona_id) {
                return $persona->persona_id === $persona_id || $persona->parent_id === $persona_id;
            }
        );

        if ($persona->count() === 1) {
            $persona->first()->delete();
            return $this->redirect('account/personas');
        }
        
        return $this->error('Persona does not exist.', 404);
    }
    
	public function actionApprovePersona()
	{
		$creator = $this->service(
            'Shinka\Persona:Persona\Creator', 
            $this->filter('persona_id', 'uint'),
            \XF::visitor()->user_id,
            true
        );
        $creator->save();
        
        return $this->redirect('account/personas');
    }
    
	public function actionRequestPersona()
	{
        $username = $this->filter('username', 'string');
        $user = $this->repository("XF:User")->getUserByNameOrEmail($username);

        /** @todo Set this conditionally, per user/group permissions */
        $approved = false;

		$creator = $this->service(
            'Shinka\Persona:Persona\Creator', 
            \XF::visitor()->user_id,
            $user->user_id,
            $approved
        );

        $creator->save();
        
        return $this->redirect('account/personas');
    }
}