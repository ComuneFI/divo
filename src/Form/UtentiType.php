<?php

namespace App\Form;

use App\Entity\Utenti;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UtentiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $submitparms = array('label' => 'Salva','attr' => array("class" => "btn-outline-primary bisubmit", "aria-label" => "Salva"));
        $builder
            ->add('submit', SubmitType::class, $submitparms)
            ->add('username')
            ->add('psw')
            
            ->add('enti')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utenti::class,
            'parametriform' => array()
        ]);
    }
}