<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use AppBundle\Entity\User;
use AppBundle\Entity\Chantier;
use AppBundle\Entity\Marchandise;

class GestionController extends Controller
{
    /**
     * @Route("/personnels/", name="Personnels")
     */
    public function indexAction(Request $request)
    {
        $username = $this->getUser()->getUserName();
    	$em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT * FROM chantier where chantier.prop = :n;';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('n', $username);
        $statement->execute();
        $chantiers = $statement->fetchAll();

        return $this->render('GESTION-STOCK/chantier.html.twig' , array(
        'chantiers'=>$chantiers));
    }

   	/**
     * @Route("/personnels/chantier/search", name="searchchantier")
     */
    public function SearchchantierAction()
    {
        $username = $this->getUser()->getUserName();

        if ( ! empty($_POST['search'])){
        $data = '%'.$_POST['search'].'%';
        }

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT * FROM chantier WHERE (chantier.nom LIKE :n AND chantier.prop = :p);';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('n', $data);
        $statement->bindValue('p', $username);
        $statement->execute();
        $chantiers = $statement->fetchAll();

        if ($chantiers == null) {
            $this->addFlash(
                'notice',
                'Aucun résultat trouvé !'
                );
                return $this->redirect('/personnels/');
        }else{
            $this->addFlash(
                'notice',
                'Résultat pour votre recherche'
                );
        }

        return $this->render('Gestion-Stock/chantier.html.twig', array(
        'chantiers' => $chantiers));
    }

    /**
     * @Route("/personnels/chantier/créer/", name="créerchantier")
     */
    public function CréerchantierAction(Request $request)
    {
        $username = $this->getUser()->getUserName();

        $chantier = new Chantier;
        $form = $this->createFormBuilder($chantier)
        ->add('nom', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => 'Nom', 'class' => 'form-control' , 'style' => 'margin-bottom:15px'))) 
        ->add('datedebut', DateType::class, array('label' => 'Date Début','input' => 'string', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd','attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))  
        ->add('etat',ChoiceType::class, array('label' => 'Etat', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'choices' => array('En cours' => 'En cours' ,'Finie' => 'Finie'),'choices_as_values' => true,'multiple'=>false,'expanded'=>false))
        ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
        ->getForm();
              
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                
        $prop = $username;
        $nom = $form['nom']->getData();
        $datedebut = $form['datedebut']->getData();
        $etat = $form['etat']->getData();

        $chantier->setProp($prop);
        $chantier->setNom($nom);
        $chantier->setDatedebut($datedebut);
        $chantier->setEtat($etat);

        $em = $this -> getDoctrine() -> getManager();
        $em->persist($chantier);
        $em->flush();

        $this->addFlash(
        'notice',
        'Chantier crée avec succès'
        );
        return $this->redirect('/personnels/');
        }
        return $this->render('Gestion-Stock/creerchantier.html.twig', array(
        'form' => $form->createView() ));
        }

    /**
     * @Route("/personnels/chantier/consultermarchandise/{id}/", name="consultermarchandise")
     */
    public function ConsultermarchandiseAction(Request $request, $id)
    {
        $chantier = $this->getDoctrine()
        ->getRepository('AppBundle:Chantier')
        ->find($id);

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT * FROM marchandise where marchandise.chantier = :c;';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('c', $id);
        $statement->execute();
        $marchandises = $statement->fetchAll();

        $marchandise = new Marchandise;
        $form = $this->createFormBuilder($marchandise)
        ->add('nom', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => 'Nom', 'class' => 'form-control' , 'style' => 'margin-bottom:15px'))) 
        ->add('quantite', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => '00', 'input' => 'integer', 'class' => 'form-control' , 'style' => 'margin-bottom:15px')))  
        ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
        ->getForm();
              
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){      
        $nom = $form['nom']->getData();
        $quantite = $form['quantite']->getData();

        $marchandise->setNom($nom);
        $marchandise->setQuantite($quantite);
        $marchandise->setChantier($id);

        $em = $this -> getDoctrine() -> getManager();
        $em->persist($marchandise);
        $em->flush();

        $this->addFlash(
        'notice',
        'Marchandise crée avec succès'
        );
        return $this->redirect('/personnels/chantier/consultermarchandise/'.$id.'/');
        }

        return $this->render('Gestion-Stock/marchandise.html.twig', array(
        'marchandises' => $marchandises, 'chantier' => $chantier, 'form' => $form->createView()));
    }

    /**
     * @Route("/personnels/chantier/consultermarchandise/search/{id}", name="searchmarchandise")
     */
    public function SearchmarchandiseAction(Request $request, $id)
    {
        if ( ! empty($_POST['search'])){
        $data = '%'.$_POST['search'].'%';
        }

        $chantier = $this->getDoctrine()
        ->getRepository('AppBundle:Chantier')
        ->find($id);

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT * FROM marchandise WHERE (marchandise.nom LIKE :n AND marchandise.chantier = :c);';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('n', $data);
        $statement->bindValue('c', $id);
        $statement->execute();
        $marchandises = $statement->fetchAll();
        
        if ($marchandises == null) {
            $this->addFlash(
                'notice',
                'Aucun résultat trouvé !'
                );
                return $this->redirect('/personnels/chantier/consultermarchandise/'.$id.'/');
        }else{
            $this->addFlash(
                'notice',
                'Résultat pour votre recherche'
                );
        }

        $marchandise = new Marchandise;
        $form = $this->createFormBuilder($marchandise)
        ->add('nom', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => 'Nom', 'class' => 'form-control' , 'style' => 'margin-bottom:15px'))) 
        ->add('quantite', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => '00', 'input' => 'integer', 'class' => 'form-control' , 'style' => 'margin-bottom:15px')))  
        ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
        ->getForm();
              
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){      
        $nom = $form['nom']->getData();
        $quantite = $form['quantite']->getData();

        $marchandise->setNom($nom);
        $marchandise->setQuantite($quantite);
        $marchandise->setChantier($id);

        $em = $this -> getDoctrine() -> getManager();
        $em->persist($marchandise);
        $em->flush();

        $this->addFlash(
        'notice',
        'Marchandise crée avec succès'
        );
        return $this->redirect('/personnels/chantier/consultermarchandise/'.$id.'/');
        }

        return $this->render('Gestion-Stock/marchandise.html.twig', array(
        'marchandises' => $marchandises, 'chantier' => $chantier, 'form' => $form->createView()));
    }

    /**
     * @Route("/personnels/chantier/consultermarchandise/plusmarchandise/{id}", name="plusmarchandise")
     */
    public function PlusmarchandiseAction(Request $request, $id)
    {

        if ( ! empty($_POST['plus'])){
            
            $quantiteplus = $_POST['plus'];
            $marchandiseplus = $this->getDoctrine()
            ->getRepository('AppBundle:Marchandise')
            ->find($id);

            $chantier = $this->getDoctrine()
            ->getRepository('AppBundle:Chantier')
            ->find($marchandiseplus -> getChantier());

            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = 'SELECT * FROM marchandise where marchandise.chantier = :c;';
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->bindValue('c', $chantier -> getId());
            $statement->execute();
            $marchandises = $statement->fetchAll();

            $marchandise = new Marchandise;
            $form = $this->createFormBuilder($marchandise)
            ->add('nom', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => 'Nom', 'class' => 'form-control' , 'style' => 'margin-bottom:15px'))) 
            ->add('quantite', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => '00', 'input' => 'integer', 'class' => 'form-control' , 'style' => 'margin-bottom:15px')))  
            ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
            ->getForm();
                  
            $form ->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){      
            $nom = $form['nom']->getData();
            $quantite = $form['quantite']->getData();

            $marchandise->setNom($nom);
            $marchandise->setQuantite($quantite);
            $marchandise->setChantier($id);

            $em = $this -> getDoctrine() -> getManager();
            $em->persist($marchandise);
            $em->flush();

            $this->addFlash(
            'notice',
            'Marchandise crée avec succès'
            );
            return $this->redirect('/personnels/chantier/consultermarchandise/'.$chantier -> getId().'/');
            }

            $marchandiseplus -> setQuantite(($marchandiseplus -> getQuantite()) + $quantiteplus);
            $em1 = $this -> getDoctrine() -> getManager();
            $em1->persist($marchandiseplus);
            $em1->flush();

        return $this->redirect('/personnels/chantier/consultermarchandise/'.$chantier -> getId().'/');
        }

        return $this->render('Gestion-Stock/marchandise.html.twig', array(
        'marchandises' => $marchandises, 'chantier' => $chantier, 'form' => $form->createView()));

    }

    /**
     * @Route("/personnels/chantier/consultermarchandise/minusmarchandise/{id}", name="minusmarchandise")
     */
    public function MinusmarchandiseAction(Request $request, $id)
    {

        if ( ! empty($_POST['minus'])){

            $quantiteminus = $_POST['minus'];
            $marchandiseminus = $this->getDoctrine()
            ->getRepository('AppBundle:Marchandise')
            ->find($id);

            $chantier = $this->getDoctrine()
            ->getRepository('AppBundle:Chantier')
            ->find($marchandiseminus -> getChantier());

            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = 'SELECT * FROM marchandise where marchandise.chantier = :c;';
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->bindValue('c', $chantier -> getId());
            $statement->execute();
            $marchandises = $statement->fetchAll();
            
            $marchandise = new Marchandise;
            $form = $this->createFormBuilder($marchandise)
            ->add('nom', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => 'Nom', 'class' => 'form-control' , 'style' => 'margin-bottom:15px'))) 
            ->add('quantite', TextType::class, array('attr' => array('label' => 'Nom','placeholder' => '00', 'input' => 'integer', 'class' => 'form-control' , 'style' => 'margin-bottom:15px')))  
            ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
            ->getForm();
                  
            $form ->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){      
            $nom = $form['nom']->getData();
            $quantite = $form['quantite']->getData();

            $marchandise->setNom($nom);
            $marchandise->setQuantite($quantite);
            $marchandise->setChantier($id);

            $em = $this -> getDoctrine() -> getManager();
            $em->persist($marchandise);
            $em->flush();

            $this->addFlash(
            'notice',
            'Marchandise crée avec succès'
            );
            return $this->redirect('/personnels/chantier/consultermarchandise/'.$chantier -> getId().'/');
            }
            
            $newquantite = (($marchandiseminus -> getQuantite()) - $quantiteminus);
            $usedquantite = (($marchandiseminus -> getUsedquantite()) + $quantiteminus);
            
            if ($newquantite < 0) {
                $this->addFlash(
                'notice',
                'La marchandise ne contient pas assez de pièces'
                );
            }else{
                $marchandiseminus -> setUsedquantite($usedquantite);
                $marchandiseminus -> setQuantite($quantiteminus);
                $em1 = $this -> getDoctrine() -> getManager();
                $em1->persist($marchandiseminus);
                $em1->flush();
            }
        return $this->redirect('/personnels/chantier/consultermarchandise/'.$chantier -> getId().'/');
        }

    return $this->render('Gestion-Stock/marchandise.html.twig', array('marchandises' => $marchandises, 'chantier' => $chantier, 'form' => $form->createView()));

    }

    /**
     * @Route("/personnels/chantier/modifierchantier/{id}", name="modifierchantier")
     */
    public function ModifierchantierAction(Request $request, $id)
    {

        $chantier = $this->getDoctrine()
        ->getRepository('AppBundle:Chantier')
        ->find($id);

        $Finie = "Finie";
        $chantier -> setEtat($Finie);
        $em = $this -> getDoctrine() -> getManager();
        $em->persist($chantier);
        $em->flush();

        $username = $this->getUser()->getUserName();
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT * FROM chantier where chantier.prop = :n;';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('n', $username);
        $statement->execute();
        $chantiers = $statement->fetchAll();

        return $this->redirect('/personnels/');
        return $this->render('GESTION-STOCK/chantier.html.twig' , array(
        'chantiers'=>$chantiers));


    }
       
}