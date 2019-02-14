<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Form\TicketsType;
use App\Entity\Tickets;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', RepeatedType::class, [
            'type' => EmailType::class,
            'invalid_message' => " L'email ne correspond pas",
            'options' => ['attr' => ['class' => 'email-field']],
            'required' => true,
            'first_options'  => ['label' => 'Email'],
            'second_options' => ['label' => 'Confirmé Email'],
        ])
            ->add('date', DateType::class,[
                'label' => 'Date de la visite',
                'widget' => 'single_text',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('tickets_id', CollectionType::class, [
                'entry_type'   => TicketsType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => '  ',
                ])
            ->add('Valider les tickets', SubmitType::class,[
                'attr' => ['class' => 'btn btn-success '],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
