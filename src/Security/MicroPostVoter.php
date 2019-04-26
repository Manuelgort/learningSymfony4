<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-25
 * Time: 16:36
 */

namespace App\Security;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE]))
        {
            return false;
        }

        if(!$subject instanceof MicroPost){
            return false;
        }
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, [User::ROLE_ADMIN]) )
        {
            return true;
        }
        $authenticatedUser = $token->getUser();

        if(!$authenticatedUser instanceof  User){
            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return $microPost->getUser()->getId() === $authenticatedUser->getId();

    }


}