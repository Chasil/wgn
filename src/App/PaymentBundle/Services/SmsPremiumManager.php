<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \App\PaymentBundle\Entity\Payment;

/**
 * Class SmsPremiumManager
 *
 * @author wojciech przygoda
 */
class SmsPremiumManager implements PaymentMethodInterface {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var array post data
     */
    protected $postData;
    /**
     * Constructor
     *
     * @param Container $container services container
     */
    public function __construct(Container $container) {
      $this->services = $container;

    }
    /**
     * Process payment
     *
     * @param Payment $payment payment
     * @param string $type type
     * @return array
     */
    function process(Payment $payment, $type){
        $paymentManager = $this->services->get('payment.manager');
        $host = $this->services->get('request')->getSchemeAndHttpHost();
        if($type == 'promo'){
            $returnUrl = $host.$this->services->get('router')->generate('user_account',array('payment'=>'success'));
        }else {
            $returnUrl = $host.$this->services->get('router')->generate('frontend_offer_payment_success',array('id'=>$payment->getId()));
        }

        $this->addValue("p24_id_sprzedawcy", $this->getMerchantId());
        $this->addValue("p24_kwota", $payment->getValue()*100);
        $this->addValue("p24_sms", $payment->getSmsCode());

        if($this->sendResponse() == "OK"){
            $payment->setTransactionId(time())
                    ->setState(Payment::STATE_SUCCESS)
                    ->setModifyDate(new \DateTime())
                    ->setTransferMethod('SMS');
            $expireDate = new \DateTime();
            $promoExpire = new \DateTime();
            $days = 90;
            $promoDays = 14;
            $offer = $payment->getOffer();
            $offer->setIsPublish(true)
                  ->setIsPromo($payment->getPromo())
                  ->setExpireDate($expireDate->modify('+'.$days.' days'))
                  ->setDays($days)
                  ->setPromoExpire($promoExpire->modify('+'.$promoDays.' days'))
                  ->setPromoDays($promoDays)
                  ->setPaymentState(Payment::STATE_SUCCESS);
            $paymentManager->save($payment);
            $this->sendMessage($payment);
            return array('success'=>true,'redirectUrl'=>$returnUrl);
        }


    }
    /**
     * Add value
     *
     * @param string $name
     * @param float $value
     */
    public function addValue($name, $value) {

        $this->postData[$name] = $value;

    }
    /**
     * Get host URL
     * @return string
     */
    public function getHost() {
        return $this->services->getParameter('smsApiUrl');
    }
    /**
     * Get merchant id
     *
     * @return string
     */
    public function getMerchantId() {
        return $this->services->getParameter('przepewy24MerchantId');
    }
    /**
     * Send Response
     * @return string
     */
    private function sendResponse(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$this->postData);
        curl_setopt($ch, CURLOPT_URL,$this->services->getParameter('smsApiUrl'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        return curl_exec ($ch);
    }
    /**
     * Send masage to user
     * @param Payment $payment payment
     */
    private function sendMessage(Payment $payment){
        $mailer = $this->services->get('mailer');

        $message = $mailer->createMessage()
            ->setSubject('Płatność przyjęta')
            ->setFrom(array($this->services->getParameter('mail_sender_email')=>$this->services->getParameter('mail_sender_name')))
            ->setTo($payment->getEmail())
            ->setBody(
                $this->services->get('templating')->render(
                    'AppOfferBundle:Emails:paymentSuccess.html.twig',
                    array('offer'=>$payment->getOffer())
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }
}

