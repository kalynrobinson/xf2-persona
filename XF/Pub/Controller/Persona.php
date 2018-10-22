<?php

namespace Shinka\Persona\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Persona extends \XF\Pub\Controller\AbstractController
{
    /**
     * Change active session to given user, then redirect.
     *
     * @return void
     */
    public function actionSwitch()
    {
        $user_id = $this->filter('user_id', 'uint');
        $visitor = \XF::visitor();
        $user = $this->assertPersonaExists($visitor, $user_id);

        // Exit if visitor is not attached to user
        if (!$user) {
            return $this->error(\XF::phrase('shinka_persona_does_not_exist'), 404);
        }   

        // Switch active session
        /** @var \XF\ControllerPlugin\Login $loginPlugin */
        $loginPlugin = $this->plugin('XF:Login');
        $loginPlugin->completeLogin($user, true);

        return $this->redirect('index');
    }

    /**
     * Find persona by user ID.
     *
     * @param \XF\Entity\User $user
     * @param int $user_id
     * @return \XF\Entity\User|null
     */
    protected function assertPersonaExists(\XF\Entity\User $user, int $user_id) {
        return $user->Personas->filter(function ($persona) use ($user_id) {
            return $persona->user_id === $user_id;
        })->first();
    }
}
