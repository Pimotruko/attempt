<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('field', ChoiceType::class, [
                'choices' => [
                    'First Name' => 'firstName',
                    'Last Name' => 'lastName',
                    'Course' => 'courses'
                    // Add more fields as needed
                ],
                'label' => 'Search In',
            ])
            ->add('value', TextType::class, [
                'label' => 'Search Value',
                'required' => false, // Allow empty values
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Search',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET', // Use GET method for the form
        ]);
    }
}
