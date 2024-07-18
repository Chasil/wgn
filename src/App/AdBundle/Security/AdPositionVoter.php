<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Security;

use App\AdBundle\Entity\AdPosition;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class AdPositionVoter
 *
 * @author wojciech przygoda
 */
class AdPositionVoter extends Voter
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
     * @param AccessDecisionManagerInterface $decisionManager access decision manager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     *
     * Check if vouter support attribute
     *
     * @param type $attribute atribute
     * @param AdPosition $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof AdPosition) {
            return false;
        }

        return true;
    }

    /**
     *
     * Vote on attribute
     *
     * @param string $attribute atribute
     * @param AdPosition $subject subject
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
        $adPosition = $subject;

        switch($attribute) {
            case self::MANAGE:
                return $this->canManage($adPosition, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Check if user can manage ad position
     *
     * @param AdPosition $adPosition ad position
     * @param User $user user
     * @return bool
     */
    private function canManage(AdPosition $adPosition, User $user)
    {

        return $adPosition->getIsOfficePosition();
    }
}
