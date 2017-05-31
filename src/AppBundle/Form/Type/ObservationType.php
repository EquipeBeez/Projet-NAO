<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ObservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateRecord', DateTimeType::class, array(
                'widget' => 'single_text',
                'label' => "Date d'enregistrement",
                'format' => 'dd/MM/yyyy',
                'attr' => array('readonly' => 'readonly'),
            ))
            ->add('dateObservation', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5'  => 'false'
            ))
            ->add('title')
            ->add('gpsLatitude', NumberType::class, array(
                'scale' => 6,
            ))
            ->add('gpsLongitude', NumberType::class, array(
                'scale' => 6,
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'En attente' => 'waiting',
                    'Validée' => 'validated',
                    'Rejetée' => 'rejected',
                ),
                'label' => 'Status',
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ))
            ->add('description')
            ->add('rejectMessage')
            ->add('image', FileType::class, array(
                'label' => 'Votre image : ',
                'required' => false,
                'data_class' => null
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Observation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_observation';
    }


}
