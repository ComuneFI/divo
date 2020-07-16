<?php

namespace App\Form;

use App\Entity\Utenti;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class UtentiType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $submitparms = array('label' => 'Salva', 'attr' => array("class" => "btn-outline-primary bisubmit", "aria-label" => "Salva"));
        $builder
                ->add('submit', SubmitType::class, $submitparms)
                ->add('username')
                ->add('psw')
                ->add('data_evento', DateTimeType::class, array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array('class' => 'bidatepicker'),
                ))
                ->add('enti')
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Utenti::class,
            'parametriform' => array()
        ]);
    }

}
