<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Security;

use App\OfferBundle\Entity\Offer;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class OfferVoter
 *
 * @author wojciech przygoda
 */
class OfferVoter extends Voter
{
    /**
     * @const attribute to check
     */
    const EDIT = 'edit';

    /**
     * @const attribute to check
     */
    const DELETE = 'delete';

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
     * @param string $attribute
     * @param Offer $subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT,self::DELETE,))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Offer) {
            return false;
        }

        return true;
    }

    /**
     *
     * Vote on attribute
     *
     * @param string $attribute
     * @param Offer $subject
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
        $offer = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($offer, $user);
            case self::DELETE:
                return $this->canDelete($offer, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Check if user can delete offer
     *
     * @param Offer $offer
     * @param User $user
     * @return type
     */
    private function canDelete(Offer $offer, User $user)
    {
        return $user === $offer->getUser();
    }

    /**
     * Check if user can edit offer
     *
     * @param Offer $offer
     * @param User $user
     * @return type
     */
    private function canEdit(Offer $offer, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $offer->getUser();
    }
}
