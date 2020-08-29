<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label'=>'Mail :'))
            ->add('nom', TextType::class, array('label'=>'Nom :'))
            ->add('prenom', TextType::class, array('label'=>'Prenom :'))
            ->add('username', TextType::class, array('label'=>'Pseudo :'))
            //->add('password',PasswordType::class)
            ->add('password',RepeatedType::class, [
                'type'=> PasswordType::class,
                'invalid_message' => 'Les deux champs Mot de Passe doivent correspondre !',
                'options' => ['attr' => ['class' => 'form-control mb-4']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe svp '],
                'constraints'   => array(
                            new NotBlank(),
                            new Length(array('max' => 255)),
                            new Regex(array(
                                'pattern'   => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[0-9a-zA-Z]/',
                                'match'     => true,
                                'message'   => 'Votre mot de passe doit comporter au moins 8 caractères et contenir au moins une majuscule, un chiffre et une ponctuation'
                            ))
                           )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

