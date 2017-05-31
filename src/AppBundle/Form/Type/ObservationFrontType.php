<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ObservationFrontType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('description', TextareaType::class, array(
                'attr' => array('style' => 'resize: none'),
            ))
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
