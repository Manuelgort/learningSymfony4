<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-24
 * Time: 21:40
 */

namespace App\form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('username', TextType::class)
             ->add('email', EmailType::class)
             ->add('plainPassword', RepeatedType::class,[
                 'type' => PasswordType::class,
                 'first_options'=>['label'=> 'Wachtwoord'],
                 'second_options'=>['label'=> 'Controle wachtwoord']
             ])
             ->add('fullName', TextType::class)
             ->add('AVG',CheckboxType::class,[
                 'mapped'=>false,
                 'constraints'=> new IsTrue(),
                 'label'=>'Ga akkoord met onze AVG voorwaarden',
             ])
             ->add('Registreren', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
       $resolver->setDefaults([
           'data_class'=> User::class
       ]);
    }


}