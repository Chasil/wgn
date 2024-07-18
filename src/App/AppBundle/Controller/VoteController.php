<?php

namespace App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class VoteController extends Controller
{
    public function voteAction() {
        $value = $this->get('request')->get('rate');
        $ip = $this->get('request')->getClientIp();

        $this->get('rating.manager')->vote($value,$ip);

        return new JsonResponse(['success'=>true]);
    }
}
