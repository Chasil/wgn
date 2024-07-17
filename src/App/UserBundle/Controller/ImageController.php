<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\UserBundle\Form\AvatarType;

/**
 * Class ImageController
 *
 * @author wojciech przygoda
 */
class ImageController extends Controller
{
    /**
     * Add image
     * @return Response
    */
    public function addAction() {

        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();

        $file = $this->getRequest()->files->get('file');
        $fileName = $file->getClientOriginalName();
        $dir = __DIR__.'/../../../../web/uploads/avatar/'.$user->getId();


        if(!file_exists($dir)){
            mkdir($dir);
        }
        $file->move($dir,$fileName);
        $user->setAvatar($fileName);
        $userManager->save($user);

        $imageUrl = $this->get('liip_imagine.cache.manager')
                         ->getBrowserPath('/uploads/avatar/'.$user->getId().'/'.$user->getAvatar(), 'avatar_offer', array());
        return new JsonResponse(array('success'=>true,
                                      'imageUrl'=>$imageUrl));
    }
    /**
     * Delete image
     * @return Response
    */
    public function deleteAction()
    {
        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();
        $fileName = $user->getAvatar();
        $user->setAvatar(null);
        $dir = __DIR__.'/../../../../web/uploads/avatar/'.$user->getId().'/';
        @unlink($dir.$fileName);
        $userManager->save($user);
        return new JsonResponse(array('success'=>true));
    }
}
