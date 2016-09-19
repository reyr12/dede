<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', 'password')
            ->add('email', 'email')            
            ->add('telefono')
#            ->add('roles', 'choice', array(
#                'choices'   => array('ROLE_COLONO' => 'COLONO', 'ROLE_TESORERO' => 'TESORERO', 'ROLE_MESADIRECTIVA' => 'MESA DIRECTIVA', 'ROLE_ADMIN' => 'ADMINISTRADOR' ),
#                'required'  => true,
#                ))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario'
        ));
    }

    public function getName()
    {
        return 'sisaf_sisafbundle_usuariotype';
    }
}
