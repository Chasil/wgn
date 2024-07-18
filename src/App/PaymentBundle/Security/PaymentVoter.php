<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Security;

use App\PaymentBundle\Entity\Payment;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class PaymentVoter
 *
 * @author wojciech przygoda
 */
class PaymentVoter extends Voter
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
     * Check if vouter support attribute
     *
     * @param string $attribute attribute
     * @param OfficeImage $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT,self::DELETE,self::VIEW))) {
            return false;
        }
        // only vote on Post objects inside this voter
        if (!$subject instanceof Payment) {
            return false;
        }

        return true;
    }

    /**
     * Vote on attribute
     *
     * @param string $attribute attribute
     * @param Payment $subject payment
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

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $payment = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($payment, $user);
            case self::VIEW:
                return $this->canView($payment, $user);
            case self::DELETE:
                return $this->canDelete($payment, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Check if user can delete payment
     *
     * @param Payment $payment payment
     * @param User $user user
     * @return boolean
     */
    private function canDelete(Payment $payment, User $user)
    {
        return $user === $payment->getUser();
    }

    /**
     * Check if user can view payment
     *
     * @param Payment $payment payment
     * @param User $user user
     * @return boolean
     */
    private function canView(Payment $payment, User $user)
    {
        return $user === $payment->getUser();
    }

    /**
     * Check if user can edit payment
     *
     * @param Payment $payment payment
     * @param User $user user
     * @return boolean
     */
    private function canEdit(Payment $payment, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $payment->getUser();
    }
}
