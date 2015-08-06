<?php

namespace ME\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', 'text', array('max_length'=> 20))
            ->add('password', 'password', array('max_length' => 30))
            ->add('mail', 'email', array('max_length' => 255))
            ->add('adresse', 'textarea', array('max_length' => 255))
            ->add('telephone', 'number', array('max_length' => 20))
            ->add('webSite', 'text', array('required' => false), array('max_length' => 40))
			->add('Valider', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ME\PlatformBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'me_platformbundle_user';
    }
}
