<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Security;

use App\AdBundle\Entity\Ad;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class AdVoter
 *
 * @author wojciech przygoda
 */
class AdVoter extends Voter
{
    /**
     * @const attribute to check
     */
    const MANAGE = 'manage';

    /**
     *
     * @var AccessDecisionManagerInterface access decision manager
     */
    private $decisionManager;

    /**
     * Constructor
     *
     * @param AccessDecisionManagerInterface $decisionManager  access decision manager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     *
     * Check if vouter support attribute
     *
     * @param string $attribute attribute
     * @param Ad $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Ad) {
            return false;
        }

        return true;
    }

    /**
     *
     * Vote on attribute
     *
     * @param string $attribute attrinute
     * @param Ad $subject subject
     * @param TokenInterface $token token
     * @return boolean
     * @throws \LogicException
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        if ($this->decisionManager->decide($token, array('ROLE_MANAGER'))) {
            return true;
        }
        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $ad = $subject;

        switch($attribute) {
            case self::MANAGE:
                return $this->canManage($ad, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Check if user can manage ad
     *
     * @param Ad $ad ad
     * @param User $user user
     * @return boolean
     */
    private function canManage(Ad $ad, User $user)
    {
        $userOffice = $user->getOffice();
        if(!is_object($userOffice)){
            return false;
        }
        $office = $ad->getOffice();

        if(!is_object($office)){
            return false;
        }

        return $userOffice === $office;
    }
}
