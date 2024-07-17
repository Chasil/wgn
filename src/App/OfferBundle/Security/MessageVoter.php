<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Security;

use App\OfferBundle\Entity\Message;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class MessageVote
 *
 * @author wojciech przygoda
 */
class MessageVoter extends Voter
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
     * @const attribute to check
     */
    const VIEW = 'view';

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
     * @param Message $subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT,self::DELETE,self::VIEW))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Message) {
            return false;
        }

        return true;
    }
    /**
     *
     * Vote on attribute
     *
     * @param string $attribute
     * @param Message $subject
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
        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $message = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($message, $user);
            case self::VIEW:
                return $this->canView($message, $user);
            case self::DELETE:
                return $this->canDelete($message, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }
    /**
     *
     * Check if user can delete article
     *
     * @param Message $message
     * @param User $user
     * @return bool
     */
    private function canDelete(Message $message, User $user)
    {
        return $user === $message->getRecipient();
    }

    /**
     * Check if user can view article
     * @param Message $message
     * @param User $user
     * @return type
     */
    private function canView(Message $message, User $user)
    {
        return $user === $message->getRecipient();
    }

    /**
     * Check if user can edit article
     * @param Message $message
     * @param User $user
     * @return type
     */
    private function canEdit(Message $message, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $message->getRecipient();
    }
}
