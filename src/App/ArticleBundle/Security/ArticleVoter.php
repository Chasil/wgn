<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Security;

use App\ArticleBundle\Entity\Article;
use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class ArticleVoter
 *
 * @author wojciech przygoda
 */
class ArticleVoter extends Voter
{
    /**
     * @const attribute to check
     */
    const OWNER = 'owner';

    /**
     *
     * @var AccessDecisionManagerInterface  access decision manager
     */
    private $decisionManager;

    /**
     * Constructor
     *
     * @param AccessDecisionManagerInterface $decisionManager  access decision manager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {

        $this->decisionManager = $decisionManager;
    }

    /**
     *
     * Check if vouter support attribute
     *
     * @param string $attribute attribute
     * @param Article $subject subject
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::OWNER))) {
            return false;
        }
        // only vote on Post objects inside this voter
        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }
    /**
     *
     * Vote on attribute
     *
     * @param string $attribute attribute
     * @param Article $subject subject
     * @param TokenInterface $token token
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_AUTHOR'))) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $article = $subject;

        return $this->isOwner($article, $user);

    }

    /**
     * Check if user is owner of the article
     *
     * @param Article $article article
     * @param User $user user
     * @return bool
     */
    private function isOwner(Article $article, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $article->getOwner();
    }
}
