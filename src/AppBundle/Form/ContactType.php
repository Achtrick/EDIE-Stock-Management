<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'placeholder' => 'Votre nom'),
                'constraints' => array(
                    new NotBlank(array("message" => "Entrer votre nom")),
                )
            ))
            ->add('subject', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'placeholder' => 'Sujet'),
                'constraints' => array(
                    new NotBlank(array("message" => "Entrer un sujet")),
                )
            ))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'placeholder' => 'Votre adrèsse mail'),
                'constraints' => array(
                    new NotBlank(array("message" => "Entrer une adrèsse mail valide")),
                    new Email(array("message" => "Votre adrèsse mail n'est pas valide")),
                )
            ))
            ->add('message', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'placeholder' => 'Votre message ici'),
                'constraints' => array(
                    new NotBlank(array("message" => "Entrer votre message")),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}