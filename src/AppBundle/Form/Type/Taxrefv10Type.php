<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Taxrefv10Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Famille', null, array(
                'required'    => false,
            ))
            ->add('LbNom', null, array(
                'required'    => false,
            ))
            ->add('LbAuteur', null, array(
                'required'    => false,
            ))
            ->add('NomVern', null, array(
                'required'    => false,
            ))
            ->add('NomVernEng', null, array(
                'required'    => false,
            ))
            ->add('Url', null, array(
                'required'    => false,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Taxrefv10'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxrefv10';
    }


}
