<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Security;

use App\OfficeBundle\Entity\OfficeImage;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class ImageVoter
 *
 * @author wojciech przygoda
 */
class ImageVoter extends Voter
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
     * Check if vouter support attribute
     *
     * @param string $attribute attribute
     * @param OfficeImage $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof OfficeImage) {
            return false;
        }

        return true;
    }

    /**
     * Vote on attribute
     *
     * @param string $attribute attribute
     * @param OfficeImage $subject subject
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

        return false;

        throw new \LogicException('This code should not be reached!');
    }

}
