<?php

namespace App\Form;

use App\Entity\Rxliste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RxlisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $submitparms = array('label' => 'Salva','attr' => array("class" => "btn-outline-primary bisubmit", "aria-label" => "Salva"));
        $builder
            ->add('submit', SubmitType::class, $submitparms)
            ->add('lista_desc')
            ->add('nome_cand')
            ->add('id_source')
            ->add('enti')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rxliste::class,
            'parametriform' => array()
        ]);
    }
}