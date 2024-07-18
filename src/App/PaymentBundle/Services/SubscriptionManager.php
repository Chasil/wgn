<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\PaymentBundle\Entity\Payment;
/**
 * Class SubscriptionManager
 *
 * @author wojciech przygoda
 */
class SubscriptionManager implements PaymentMethodInterface {

    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
    }
    /**
     * Process payment
     *
     * @param Payment $payment payment
     * @param string $type type
     * @return array
     */
    public function process(Payment $payment, $type) {
        $host = $this->services->get('request')->getSchemeAndHttpHost();
        $userManager = $this->services->get('user.manager');
        $paymentManager = $this->services->get('payment.manager');
        $subscriptionManager = $this->services->get('subscription.manager');
        $em = $this->services->get('doctrine')->getManager();
        switch($type){
            case 'add':
                $redirectUrl = $host.$this->services->get('router')->generate('user_account');
            break;
            case 'renew':
                $redirectUrl = $host.$this->services->get('router')->generate('user_account');
            break;
        }
        $em->getConnection()->beginTransaction();

        try {
            $user = $userManager->getCurrentLogged();

            if(!is_object($user) || !$user->hasActiveSubscription()){
                throw new \Exception('No user account or active subscription');
            }
            $items = $payment->getPaymentItems();
            $subscription = $user->getActiveSubscription();

            $free = $subscription->getNumberOfAvailable() - $subscription->getNumberOfUsed();

            if(count($items) > $free && $subscription->getNumberOfAvailable() > 0){
                throw new \Exception('Inadequate amount of credits');
            }

            $expireDate = new \DateTime();
            $promoExpire = new \DateTime();
            $days = 90;
            $promoDays = 0;


            foreach($items as $item){
                $subscriptionManager->reduceAvailable($subscription);
                $item->setValue(0);
                $offer = $item->getOffer();
                $offer->setIsPublish(true)
                      ->setIsPromo(false)
                       ->setCreateDate(new \DateTime())
                      ->setExpireDate($expireDate->modify('+'.$days.' days'))
                      ->setDays($days)
                      ->setPromoExpire($promoExpire->modify('+'.$promoDays.' days'))
                      ->setPromoDays($promoDays)
                      ->setPaymentState(Payment::STATE_SUCCESS);
            }

            $payment->setTransactionId(time())
                    ->setValue(0)
                    ->setUser($user)
                    ->setPaymentMethod(Payment::TYPE_SUBSCRIPTION)
                    ->setState(Payment::STATE_SUCCESS)
                    ->setModifyDate(new \DateTime())
                    ->setTransferMethod(Payment::TYPE_SUBSCRIPTION)
                    ->copyDataFormUser();

            $paymentManager->save($payment);
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            return array('success'=>false,'error'=>$e->getMessage());
        }
        return array('success'=>true,'redirectUrl'=>$redirectUrl);

    }

}
