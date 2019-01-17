<?php

namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Orders;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('date', DateType::class)
        ->add('name', TextType::class)
        ->add('surname', TextType::class)
        ->add('birthdate', BirthdayType::class)
        ->add('allday', CheckboxType::class)
        ->add('reduced', CheckboxType::class)
        ->add('price', CheckboxType::class)
        ->add('token', CheckboxType::class)
        ->add('order_id', EntityType::class, [
            // looks for choices from this entity
            'class' => Orders::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'id',])
        // ->add('save', SubmitType::class, array('label' => 'Commander'))
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
