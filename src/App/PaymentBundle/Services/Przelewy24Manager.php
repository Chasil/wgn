<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\PaymentBundle\Entity\Payment;
use App\PaymentBundle\Entity\Invoice;

/**
 * Class Przelewy24Manager
 *
 * @author wojciech przygoda
 */
class Przelewy24Manager implements PaymentMethodInterface {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var array post data
     */
    private $postData        =    array();

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
      $this->addValue('salt',$this->getSalt());
      $this->addValue('p24_merchant_id',$this->getMerchantId());
      $this->addValue('p24_pos_id',(int)$this->getPosId());
      $this->addValue('p24_api_version','3.2');
    }
    /**
     * Get host URL
     * @return string
     */
    public function getHost() {

        if($this->isEnableTestMode()){
            return $this->services->getParameter('przelewy24SandboxUrl');
        }

        return $this->services->getParameter('przelewy24Url');
    }
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() {
        if($this->isEnableTestMode()){
            return $this->services->getParameter('przelewy24SandBoxSald');
        }

        return $this->services->getParameter('przelewy24Sald');
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
     * Get posId
     * @return string
     */
    public function getPosId() {
        return $this->services->getParameter('przepewy24PosId');
    }

    /**
     * Add payment value
     *
     * @param string $name name
     * @param float $value value
     */
    public function addValue($name, $value) {

        $this->postData[$name] = $value;

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
        if($type == 'promo'){
            $returnUrl = $host.$this->services->get('router')->generate('user_account',array('payment'=>'success'));
            $statusUrl = $host.$this->services->get('router')->generate('payment_przelewy24_promo_status');
        }else {
            $returnUrl = $host.$this->services->get('router')->generate('frontend_offer_payment_success',array('id'=>$payment->getId()));
            $statusUrl = $host.$this->services->get('router')->generate('payment_przelewy24_status');
        }

        $this->addValue("p24_session_id",$payment->getSessionId());
        $this->addValue("p24_amount",$payment->getValue()*100);
        $this->addValue("p24_currency",'PLN');
        $this->addValue("p24_description",'Płatność za ogłoszenie. ID transakcji'. $payment->getId());
        $this->addValue("p24_email",$payment->getEmail());
        $this->addValue("p24_client",$payment->getFirstName().' '. $payment->getLastName());
        $this->addValue("p24_address",$payment->getAddress());
        $this->addValue("p24_zip",$payment->getZipCode());
        $this->addValue("p24_city",$payment->getCity());
        $this->addValue("p24_country",strtoupper($payment->getCountry()->getIsoCode()));
        $this->addValue("p24_phone",$payment->getPhone());
        $this->addValue("p24_language",'pl');
        $this->addValue("p24_phone",$payment->getPhone());
        $this->addValue("p24_url_return",$returnUrl);
        $this->addValue("p24_url_status",$statusUrl);

        $crc = md5($this->postData["p24_session_id"]."|".$this->getPosId()."|".$this->postData["p24_amount"]."|".$this->postData["p24_currency"]."|".$this->getSalt()) ;
        $this->addValue("p24_sign", $crc);

        $response = $this->callUrl("trnRegister",$this->postData);

        if($response["error"] == "0") {
            $redirectUrl = $this->getHost()."trnRequest/". $response['token'];
            return array('success'=>true,'redirectUrl'=>$redirectUrl);
        } else {
            return array('success'=>false,'redirectUrl'=>'test');
        }
    }
    /**
     * Confirm payment
     *
     * @return boolean
     * @throws \Exception
     */
    public function confirm() {
        $em = $this->services->get('doctrine')->getManager();
        $request = $this->services->get('request');
        $paymentManager = $this->services->get('payment.manager');
        $invoiceManager = $this->services->get('invoice.manager');
        $em->getConnection()->beginTransaction();

        try {
            $payment = $paymentManager->findBySessionId($request->get('p24_session_id'));
            $payment->setState(Payment::STATE_SUCCESS)
                    ->setTransferMethod($request->get('p24_method'))
                    ->setTransactionId($request->get('p24_order_id'))
                    ->setModifyDate(new \DateTime());
            $now = new \DateTime();
            $expireDate = clone $now;
            $promoExpire = clone $now;
            $days = 90;
            $promoDays = 14;
            $expireDate->modify('+'.$days.' days');
            $promoExpire->modify('+'.$promoDays.' days');

            $paymentItems = $payment->getPaymentItems();
            foreach($paymentItems as $item){
                $offer = $item->getOffer();
                $offer->setIsPublish(true)
                      ->setIsPromo($payment->getPromo())
                      ->setCreateDate($now)
                      ->setExpireDate($expireDate)
                      ->setDays($days)
                      ->setPromoExpire($promoExpire)
                      ->setPromoDays($promoDays)
                      ->setPaymentState(Payment::STATE_SUCCESS);

            }

            $paymentManager->save($payment);
            $invoiceManager->createFromPayment($payment);
            $this->addValue('p24_order_id',$payment->getTransactionId());
            $this->addValue("p24_session_id",$payment->getSessionId());
            $this->addValue("p24_amount",$payment->getValue()*100);
            $this->addValue("p24_currency",'PLN');
            $this->verify();

            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
        $this->sendMessage($payment);
        return true;
    }
    /**
     * Confirm promo payment
     *
     * @return boolean
     * @throws \Exception
     */
    public function confirmPromo() {
        $em = $this->services->get('doctrine')->getManager();
        $request = $this->services->get('request');
        $paymentManager = $this->services->get('payment.manager');
        $invoiceManager = $this->services->get('invoice.manager');
        $em->getConnection()->beginTransaction();

        try {
            $payment = $paymentManager->findBySessionId($request->get('p24_session_id'));
            $payment->setState(Payment::STATE_SUCCESS)
                    ->setTransferMethod($request->get('p24_method'))
                    ->setTransactionId($request->get('p24_order_id'))
                    ->setModifyDate(new \DateTime());

            $paymentItems = $payment->getPaymentItems();
            $promoDays = 14;

            $promoExpire = new \DateTime();
            $promoExpire->modify('+'.$promoDays.' days');

            foreach($paymentItems as $item){
                $offer = $item->getOffer();
                $offer->setIsPublish(true)
                      ->setIsPromo($payment->getPromo())
                      ->setPromoExpire($promoExpire)
                      ->setPromoDays($promoDays)
                      ->setPaymentState(Payment::STATE_SUCCESS);


            }
            $paymentManager->save($payment);
            $invoiceManager->createFromPayment($payment,Invoice::TYPE_PROMO);
            $this->addValue('p24_order_id',$payment->getTransactionId());
            $this->addValue("p24_session_id",$payment->getSessionId());
            $this->addValue("p24_amount",$payment->getValue()*100);
            $this->addValue("p24_currency",'PLN');
            $this->verify();

            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
        $this->sendMessage($payment);
        return true;
    }
    /**
     *
     * Function verify received from P24 system transaction's result.
     * @return array
     */
    public function verify() {

        $crc = md5($this->postData["p24_session_id"]."|".$this->postData["p24_order_id"]."|"
                  .$this->postData["p24_amount"]."|".$this->postData["p24_currency"]."|"
                  .$this->getSalt()) ;
        $this->addValue("p24_sign", $crc);

        $response = $this->callUrl("trnVerify",$this->postData);

        if($response['error']!='0'){
            throw new \Exception($response['errorMessage']);
        }

        return true;
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

    /**
     * Check if is in test mode
     *
     * @return boolean
     */
    private function isEnableTestMode(){
        return $this->services->getParameter('przelewy24TestMode');
    }
    /**
     * Call remote url
     * @param string $function function name
     * @param array $data data
     * @return array
     * @throws \Exception
     */
    private function callUrl($function, $data) {
        if(!in_array($function, array("trnRegister","trnRequest","trnVerify","testConnection"))) {
            throw new \Exception("Method not exists");
        }

        $url = $this->getHost().$function;
        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        if(!$ch) {
            throw new \Exception("Curl init error");
        }
        if(count($data)) {
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec ($ch);
        if(!$result){
            throw new \Exception("Curl exec error");
        }

        $info = curl_getinfo($ch);
        curl_close ($ch);

        if($info["http_code"]!=200) {
            return array("error"=>200,"errorMessage"=>"call:Page load error (".$info["http_code"].")");
        } else {
            $response = array();
            parse_str($result,$response);
            return $response;
        }
    }
}
