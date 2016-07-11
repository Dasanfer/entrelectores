<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Forms\ReviewType;

class StaticController extends Controller
{
    /**
     * @Route("/prensa", name="prensa")
     * @Route("/prensa/", name="prensa_slash")
     */
    public function prensaAction()
    {
        $response = $this->render('AppBundle:static:prensa.html.twig');
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/contacta", name="contacta")
     * @Route("/contacta/", name="contacta_slash")
     */
    public function contactaAction()
    {
        $response = $this->render('AppBundle:static:contacta.html.twig');
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/libros-promocionados", name="promocionados")
     * @Route("/libros-promocionados/", name="promocionados_slash")
     */
    public function promocionadosAction(Request $request)
    {
        $form = $this->getContactForm();

        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $this->sendContactEmail($data);
                $response = $this->render('AppBundle:static:promocionados.html.twig',array('contactForm' => $form->createView(),'thanks' => true));
            } else {
                $response = $this->render('AppBundle:static:promocionados.html.twig',array('contactForm' => $form->createView(),'thanks' => false));
            }

        } else {

            $response = $this->render('AppBundle:static:promocionados.html.twig',array('contactForm' => $form->createView(),'thanks' => false));
        }
        return $response;
    }

    /**
     * @Route("/terminos-y-condiciones", name="terminos")
     */
    public function terminosAction()
    {
        $response = $this->render('AppBundle:static:terminos.html.twig');
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    private function getContactForm(){
        return $this->createFormBuilder(array())
            ->setAction($this->generateUrl('promocionados'))
            ->add(
                'nombre',
                'text',
                array(
                    'label' => 'nombre completo',
                    'constraints' => array(new NotBlank()),
                    'attr' => array('placeholder' => 'Nombre completo')
                )
            )
            ->add(
                'libro',
                'text',
                array(
                    'label' => 'Título del libro a promocionar',
                    'attr' => array('placeholder' => 'Título del libro a promocionar'),
                    'constraints' => array(new NotBlank())
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'email',
                    'constraints' => array(new NotBlank(),new Email()),
                    'attr' => array('placeholder' => 'email')
                )
            )
            ->add(
                'program',
                'hidden',
                array(
                    'attr' => array('id' => 'promo_program_input'),
                    'required' => false
                )
            )
            ->add(
                'save',
                'submit',
                array(
                    'label' => 'quiero información del plan de promoción',
                    'attr' => array('class' => 'boton boton_verde boton_grande boton_inline')
                )
            )
            ->getForm();

    }

    private function sendContactEmail($data){

        $mailer = $this->get('mailer');
        $message = $mailer->createMessage()
            ->setSubject('Información promocionados')
            ->setFrom('promocionados@entrelectores.com')
            ->setTo('soporte@entrelectores.com')
            ->setBcc('pelayo.ramon@clouddistrict.com')
            ->setBody(print_r($data, true))
            ;
        $mailer->send($message);
    }

}
