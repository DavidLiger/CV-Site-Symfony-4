<?php

namespace App\Form;

use Eni\MediaBundle\Form\DataTransformer\StringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ReinitMdpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsPass = array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'Le mot de passe ne correspond pas.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options'  => array('label' => 'Nouveau mot de passe'),
                        'second_options' => array('label' => 'Confirmation mot de passe'),
                        'constraints'   => array(
                            new NotBlank(),
                            new Length(array('max' => 255)),
                            new Regex(array(
                                'pattern'   => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[0-9a-zA-Z\+\-_]/',
                                'match'     => true,
                                'message'   => 'message.alert.password.length'
                            )
                        ))
                    );

        $builder
             
                ->add('plainPassword', RepeatedType::class, $optionsPass)
               
                ;
               
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'new_password';
    }


}
