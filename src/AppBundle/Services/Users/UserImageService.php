<?php
namespace AppBundle\Services\Users;
use AppBundle\Entity\BookList;

class UserImageService extends \Twig_Extension {
    private $doctrine;
    private $imageHandler;
    private $container;

    public function __construct($doctrine,$imageHandler,$container){
        $this->doctrine = $doctrine;
        $this->imageHandler = $imageHandler;
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'userImage' => new \Twig_Function_Method($this, 'userImage')
        );
    }

    public function userImage($user,$width = 150,$height = 150){
        if(!is_object($user)){
            print_r($user);
            return;
        }
        $imageDir = $user->getImageDir();
        if(!is_null($imageDir) && $imageDir != ''){
            $directory = $this->container->getParameter('gregwar_image.web_dir');
            return $this->imageHandler->open($directory .'/wp-content'. $imageDir)->resize($width,$height)->jpeg();
        }

        if(!is_null($user->getFacebookId())
            && $user->getFacebookId() != ''
            && $user->getFacebookId() != '/' ){
            return '//graph.facebook.com/'.$user->getFacebookId().'/picture';
        } else {
            $directory = $this->container->getParameter('gregwar_image.web_dir') .'/wp-content/';
            return $this->imageHandler->open($directory . 'default-avatar.png')->forceResize($width,$height)->jpeg();
        }
    }

    public function getName()
    {
        return 'user_image';
    }
}
