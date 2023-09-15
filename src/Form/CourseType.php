<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Classes;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('students')
            ->add('students', EntityType::class, [
                'class' => Student::class, // Replace with the actual student entity class
                #'choice_label' => '__toString', // Replace with the property you want to display
                'multiple' => true, // Allow selecting multiple students
                'expanded' => true, // Render checkboxes for course selection
            ])
            ->add('classes', EntityType::class, [
                'class' => Classes::class, // Replace with the actual class entity class
                'choice_label' => 'name', // Replace with the property you want to display
                'multiple' => true, // Allow selecting multiple classes
                'expanded' => true, // Render checkboxes for course selection
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
