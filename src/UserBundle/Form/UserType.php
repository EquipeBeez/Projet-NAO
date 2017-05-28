<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    /** * {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => "Nom"))
            ->add('username', null, array('label' => "Nom d'utilisateur"))
            ->add('email', null, array('required' => false, 'label' => 'E-mail'))
            ->add('enabled', null, array('label' => 'Actif/Inactif'))
            ->add('newsletter', null, array('label' => 'Inscrit à la Newsletter'))
            ->add('roles', ChoiceType::class, array(
                'attr'  =>  array('class' => 'form-control',
                    'style' => 'margin:5px 0;'),
                'choices' =>
                    array
                    (
                        'Administrateur' => 'ROLE_SUPER_ADMIN',
                        'Moderateur' => 'ROLE_MODERATEUR',
                        'Naturaliste' => 'ROLE_USERNAT',
                        'Particulier' => 'ROLE_USER',
                    ) ,
                'multiple' => true,
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
