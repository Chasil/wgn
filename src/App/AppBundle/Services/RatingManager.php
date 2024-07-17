<?php


namespace App\AppBundle\Services;

use App\AppBundle\Entity\Vote;
use Doctrine\Common\Persistence\ObjectManager;


class RatingManager {
    private $om;

    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }
    public function vote($value, $ip){

        $vote = new Vote($value,$ip);

        $this->om->persist($vote);
        $this->om->flush();
    }

    public function getRating(){
        $stats = $this->om->getRepository(Vote::class)->getRatingStats();

        return $stats['avg_rate'];
    }
    public function getVoteCount() {
        $stats = $this->om->getRepository(Vote::class)->getRatingStats();

        return $stats['rate_count'];
    }
}
