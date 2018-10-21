<?php

namespace Shinka\Persona\XF\Pub\Controller;

use XF\Mvc\FormAction;

class AccountPersonas extends \XF\Pub\Controller\Account
{
    /**
     * Account -> Personas view, with forms for attaching,
     * detaching, requesting, and creating personas.
     *
     * @return void
     */
    public function actionPersonas()
    {
        $viewParams = [];
        $view = $this->view('Shinka\Persona:View', 'shinka_persona_account_view', $viewParams);
        return $this->addAccountWrapperParams($view, 'personas');
    }

    /**
     * POST-only action to delete a persona relation.
     *
     * @return void
     */
    public function actionDetachPersona()
    {
        $this->assertPostOnly();

        $persona = $this->assertPersonaExists(
            \XF::visitor(),
            $this->filter('persona_id', 'uint')
        );

        if (!$persona) {
            return $this->error('Persona does not exist.', 404);
        }

        $persona->delete();
        return $this->redirect('account/personas');
    }

    /**
     * POST-only action to approve a persona request.
     *
     * Persona requests can only be approved by Persona->User.
     *
     * @return void
     */
    public function actionApprovePersona()
    {
        $this->assertPostOnly();

        $persona = $this->assertPersonaExists(
            \XF::visitor(),
            $this->filter('persona_id', 'uint')
        );

        if (!$persona) {
            return $this->error('Persona does not exist.', 404);
        }

        $this->createPersona(
            $this->filter('persona_id', 'uint'),
            \XF::visitor()->user_id,
            true
        );

        return $this->redirect('account/personas');
    }

    /**
     * POST-only action to request a persona.
     *
     * Persona requests can only be approved by Persona->User.
     *
     * @return void
     */
    public function actionRequestPersona()
    {
        $this->assertPostOnly();

        // Fetch user
        $username = $this->filter('username', 'string');
        $user = $this->repository("XF:User")->getUserByNameOrEmail($username);

        $persona = $this->assertPersonaExists(
            \XF::visitor(),
            $user->user_id
        );

        if ($persona) {
            return $this->error(
                $persona->approved ?
                'Persona already exists.' :
                'Persona already requested.',
                422
            );
        }

        /** @todo Set this conditionally, per user/group permissions */
        $approved = false;

        $this->createPersona(
            \XF::visitor()->user_id,
            $user->user_id,
            $approved
        );

        return $this->redirect('account/personas');
    }

    /**
     * POST-only action to create a basic user
     * and associated persona relation.
     *
     * @return void
     */
    public function actionCreatePersona()
    {
        $persona = $this->repository("XF:User")->setupBaseUser();
        $this->userSaveProcess($persona)->run();

        $this->createPersona(
            \XF::visitor()->user_id,
            $persona->user_id,
            true
        );

        return $this->redirect('account/personas');
    }

    /**
     * Creates basic user from username and password.
     *
     * Implementation based on Admin > Create User.
     * @see \XF\Admin\Controller\User::userSaveProcess
     *
     * @param \XF\Entity\User $user
     * @return void
     */
    protected function userSaveProcess(\XF\Entity\User $user)
    {
        $form = $this->formAction();

        $input = $this->filter([
            'username' => 'str',
            'password' => 'str',
        ]);

        $password = $this->filter('password', 'str');
        $user->setOption('admin_edit', true);
        $form->basicEntitySave($user, ['username' => $input['username']]);

        $form->validate(function (FormAction $form) use ($input, $user) {
            if (!$input['password']) {
                $form->logError(\XF::phrase('please_enter_valid_password'), 'password');
            }
        });

        /** @var \XF\Entity\UserAuth $userAuth */
        $userAuth = $user->getRelationOrDefault('Auth');
        $form->setup(function () use ($userAuth, $input) {
            $userAuth->setPassword($input['password']);
        });

        return $form;
    }

    /**
     * Creates or updates a persona relation.
     * @see \Shinka\Persona\Service\Persona\Creator
     *
     * @param integer $parent_id
     * @param integer $persona_id
     * @param boolean $approved
     * @return void
     */
    protected function createPersona(int $parent_id, int $persona_id, $approved = false)
    {
        $this->service(
            'Shinka\Persona:Persona\Creator',
            $parent_id,
            $persona_id,
            $approved
        )->save();
    }

    /**
     * Undocumented function
     *
     * @param \XF\Entity\User $user
     * @param integer $persona_id
     * @return void
     */
    protected function assertPersonaExists(\XF\Entity\User $user, int $persona_id)
    {
        return $user->personaPivots->filter(
            function ($persona) use ($persona_id) {
                return $persona->persona_id === $persona_id || $persona->parent_id === $persona_id;
            }
        )->first();
    }
}
