<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SearchRegisteredType extends AbstractType
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
                )));
    }

    public function getBlockPrefix()
    {
        return 'appbundle_search_registered';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}