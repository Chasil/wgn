<?php
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 26.05.2019
 * Time: 12:23
 */

namespace App\AppBundle\EventListener;

use App\AppBundle\Entity\Redirect;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


class RedirectListener
{

    private $om;

    /**
     * RedirectListener constructor.
     * @param $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }


    public function onKernelRequest(GetResponseEvent  $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        $request = $event->getRequest();
        $uri  = $request->getUri();
        $parts = explode('?', $uri);
        $url = str_replace($request->getScriptName(), '', $parts[0]);
        $url = str_replace($request->getSchemeAndHttpHost(), '', $url);
        $redirect = $this->om->getRepository(Redirect::class)->findOneBy(['fromUrl'=>$url]);

        if(!is_object($redirect)){
            return;
        }
        $newUrl = $request->getSchemeAndHttpHost();

        if(!empty($request->getScriptName())) {
            $newUrl .= $request->getScriptName();
        }

        $newUrl .=  $redirect->getToUrl();

        if($redirect->getWithParams() && !empty($parts[1])) {
            $newUrl .= '?' . $parts[1];
        }

        $status = 302;

        if($redirect->isPermanent()) {
            $status = 301;
        }

        $response = new RedirectResponse($newUrl,$status);
        $event->setResponse($response);


    }
}