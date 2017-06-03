<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SearchSpeciesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldsearch', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Recherche (Taper un mot ici)',
                    'class' => 'rechercheEspece',
                )));
    }

    public function getBlockPrefix()
    {
        return 'appbundle_search_species';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
