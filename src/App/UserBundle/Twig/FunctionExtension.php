<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Twig;


/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('roleName',
                                       [$this, 'getRoleName'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('isOnline',[$this,'isOnline'],['is_safe' => ['html']]),
        ];
    }
    /**
     * Get role name
     *
     * @param string $role role
     * @return string
     */
    public function getRoleName($role) {

        switch($role){
            case 'ROLE_USER':
                $name = 'Użytkownik';
            break;
            case 'ROLE_BUISNESS':
                $name = 'Abonament';
            break;
            case 'ROLE_WRITER':
                $name = 'Dziennikarz';
            break;
            case 'ROLE_AUTHOR':
                $name = 'Redaktor';
            break;
            case 'ROLE_MANAGER':
                $name = 'Menadżer';
            break;
            case 'ROLE_ADMIN':
                $name = 'Administrator';
            break;
            case 'ROLE_SUPER_ADMIN':
                $name = 'Super Administrator';
            break;
            default:
                $name = '';
            break;
        }
        return $name;
    }
    /**
     * Check if user is online
     *
     * @param \DateTime $lastActivity last activity
     * @return boolean
     */
    public function isOnline($lastActivity){
        if(!$lastActivity instanceof \DateTime){
            return false;
        }
        $now = new \DateTime();

        return ($lastActivity->modify('+ 2 minutes') > $now);
    }
    /**
     * Get function name
     *
     * @return string
     */
    public function getName()
    {
        return 'user_function_extension';
    }
}

