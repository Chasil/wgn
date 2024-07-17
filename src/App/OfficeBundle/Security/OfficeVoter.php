<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Security;

use App\OfficeBundle\Entity\Office;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class OfficeVoter
 *
 * @author wojciech przygoda
 */
class OfficeVoter extends Voter
{

    /**
     * @const attribute to check
     */
    const MANAGE = 'manage';

    /**
     * @const attribute to check
     */
    const PUBLISH = 'publish';
    const MANAGE_LINKS = 'manage_links';

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
     * Check if vouter support attribute
     *
     * @param string $attribute attribute
     * @param Office $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE,self::PUBLISH, self::MANAGE_LINKS))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Office) {
            return false;
        }

        return true;
    }

    /**
     * Vote on attribute
     *
     * @param string $attribute attribute
     * @param Office $subject subject
     * @param TokenInterface $token
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
        $office = $subject;

        switch($attribute) {
            case self::MANAGE:
                return $this->canManage($office, $user);
            case self::PUBLISH:
                return false;
            case self::MANAGE_LINKS:
                return $this->decisionManager->decide($token, array('ROLE_ADMIN'));
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Check if user can manage office
     *
     * @param Office $office office
     * @param User $user user
     * @return boolean
     */
    private function canManage(Office $office, User $user)
    {
        $userOffice = $user->getOffice();
        if(!is_object($userOffice)){
            return false;
        }

        return $userOffice === $office;
    }
}
