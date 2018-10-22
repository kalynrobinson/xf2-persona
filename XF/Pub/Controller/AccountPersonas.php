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

        $visitor = \XF::visitor();
        $persona = $this->assertPersonaExists(
            $visitor,
            $this->filter('persona_id', 'uint')
        );

        if (!$persona) {
            return $this->error('Persona does not exist.', 404);
        }

        $persona->delete();

        $action = 'persona_detached';
        // Send alert to user not requesting detachment.
        if ($persona->parent_id === $visitor->user_id) {
            $recipient = $persona->User;
            $sender_id = $persona->parent_id;
            if (!$persona->approved) $action = 'persona_request_revoked';
        } else {
            $recipient = $persona->Parent;
            $sender_id = $persona->persona_id;
            if (!$persona->approved) $action = 'persona_request_rejected';
        }

        $this->sendAlert($recipient, $sender_id, $sender_id, $action, 'user');

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

        $visitor = \XF::visitor();

        $persona = $this->assertPersonaExists(
            $visitor,
            $this->filter('persona_id', 'uint')
        );

        if (!$persona) {
            return $this->error('Persona does not exist.', 404);
        }

        $created = $this->createPersona(
            $this->filter('persona_id', 'uint'),
            $visitor->user_id,
            true
        );

        $this->sendAlert($persona->Parent, $visitor->user_id, $created->id, 'approved');

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
        $visitor = \XF::visitor();

        // Exit if user does not exist
        if (!$user) {
            return $this->error('User does not exist', 404);
        }

        // Exit if user and visitor are the same
        if ($visitor->user_id === $user->user_id) {
            return $this->error('Cannot request yourself', 422);
        }

        $persona = $this->assertPersonaExists(
            \XF::visitor(),
            $user->user_id
        );

        // Exit if persona already exists/requested
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

        $created = $this->createPersona(
            $visitor->user_id,
            $user->user_id,
            $approved
        );

        $this->sendAlert($user, $visitor->user_id, $created->id, 'request');

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
        return $this->service(
            'Shinka\Persona:Persona\Creator',
            $parent_id,
            $persona_id,
            $approved
        )->save();
    }

    /**
     * Finds matching persona in user's persona pivots.
     *
     * @param \XF\Entity\User $user
     * @param integer $persona_id
     * @return \XF\Entity\User $user|null
     */
    protected function assertPersonaExists(\XF\Entity\User $user, int $persona_id)
    {
        return $user->personaPivots->filter(
            function ($persona) use ($persona_id) {
                return $persona->persona_id === $persona_id || $persona->parent_id === $persona_id;
            }
        )->first();
    }

    /**
     * Creates user alert.
     *
     * @param \XF\Entity\User $recipient
     * @param int    $sender_id
     * @param int    $content_id
     * @param string $action
     * @param string [$content_type='persona']
     * @return void
     */
    public function sendAlert($recipient, $sender_id, $content_id, $action, $content_type = 'persona')
    {
        /** @var \XF\Repository\UserAlert $alertRepo */
        $alertRepo = $this->repository('XF:UserAlert');
        $alertRepo->alert(
            $recipient,
            $sender_id,
            '',
            $content_type,
            $content_id,
            $action
        );
    }
}
