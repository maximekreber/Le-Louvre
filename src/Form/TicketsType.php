<?php

namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Orders;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstname', TextType::class,[
            'label' => 'Prénom',
        ])
        ->add('lastname', TextType::class,[
            'label' => 'Nom de famille',
        ])
        ->add('country', CountryType::class,[
            'label' => 'Pays',
        ])
        ->add('birthdate', BirthdayType::class,[
            'label' => 'Date de naissance',
            ])
        ->add('allday', ChoiceType::class, [
            'choices'  => [
                'Oui, je visite toute la journée' => true,
                'Non, je visite après 14 heures.' => false,],
            'label' => 'Visitez vous toute la journée ?  ',
            ])
        ->add('reduced', ChoiceType::class, [
            'choices'  => [
                'Oui, je dispose d\'un tarif réduit' => true,
                'Non, je ne dispose pas d\'un tarif réduit' => false,],
            'label' => 'Disposez vous d\'un tarif réduit ?
            (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)  ',
            ]);
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
