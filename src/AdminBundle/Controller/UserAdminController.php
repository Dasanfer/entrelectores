<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserAdminController extends CRUDController
{
    
    public function loginAction($id = null) {

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        
        $username=$object->getUsername();
        
        return new RedirectResponse($this->generateUrl('homepage',array("_switch_user"=>$username)));
    }
}
