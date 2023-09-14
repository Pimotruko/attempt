<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'choice',
                'years' => range(date('Y') - 30, date('Y') - 10), // Adjust the range as needed
            ])
            ->add('courses', EntityType::class, [
                'class' => Course::class, // Replace with the actual Course entity class
                'choice_label' => 'name', // Replace with the property you want to display
                'multiple' => true, // Allow selecting multiple courses
                'expanded' => true, // Render checkboxes for course selection
            ])
            #->add('classes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
