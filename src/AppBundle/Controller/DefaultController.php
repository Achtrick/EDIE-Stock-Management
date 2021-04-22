<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="Acceuil")
     */
    public function indexAction(Request $request)
    {
        $user = $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $userRole = $this->getUser()->getRoles();
            $username = $this->getUser()->getUserName();        
            if (in_array("ROLE_ADMIN", $userRole)) {
                return $this->redirect("/admin/");
            } elseif (in_array("ROLE_USER", $userRole)) {
                return $this->redirect("/personnels/");
            } 
        else {
            return $this->render('EDIE/home.html.twig' , array());
        }
        return $this->render('EDIE/home.html.twig');
        }
        return $this->render('EDIE/home.html.twig');
    }

    /**
     * @Route("/services", name="services")
     */
    public function servicesAction()
    {
        return $this->render('EDIE/services.html.twig');
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function aproposAction()
    {
        return $this->render('EDIE/apropos.html.twig');
    }


    /**
     * @Route("/contact", name="contact")
     */ 
    public function contactAction(Request $request)
    {
        // Create the form according to the FormType created previously.
        // And give the proper parameters
        $form = $this->createForm('AppBundle\Form\ContactType',null,array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('myapplication_contact'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);

            if($form->isValid()){
                // Send mail
                if($this->sendEmail($form->getData())){

                    $this->addFlash(
                    'notice',
                    'Mail envoyer avec succès merci de nous-contactez'
                    );
                    
                    return $this->redirectToRoute('contact');
                }else{
                    // An error ocurred, handle
                    var_dump("Errooooor :(");
                }
            }
        }

        return $this->render('EDIE/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function sendEmail($data){
        $myappContactMail = 'achref25471772@gmail.com';
        $myappContactPassword = 'Achref12813299';
        
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')->setUsername($myappContactMail)->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);
        
        $message = \Swift_Message::newInstance($data["subject"])
        ->setFrom(array($myappContactMail => "Message de la part de :".$data["name"]))
        ->setTo(array(
            $myappContactMail => $myappContactMail
        ))
        ->setBody($data["message"]." "
            .$data["email"]);
        
        return $mailer->send($message);
    }
}
