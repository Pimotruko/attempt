<?php

namespace App\Admin;


use App\Entity\Course;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;


final class StudentAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Student Info', ['class' => 'col-md-9'])
                ->add('firstName')
                ->add('lastName')
                ->add('dateOfBirth', DateType::class, [
                    'years' => range(date('Y') - 30, date('Y') - 10), // Adjust the range as needed
                    'widget' => 'choice', // Use 'choice' widget for year selection
                ])
            ->end()
            
            ->with('Course Info', ['class' => 'col-md-3'])
                ->add('courses', ModelType::class, [
                    'class' => Course::class,
                    'multiple' => true, // If it's a ManyToMany relationship
                    'expanded' => true, // If you want checkboxes for multiple selection
                ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('firstName')
            ->add('lastName')
            ->add('courses');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('firstName')
            ->addIdentifier('lastName')
            ->addIdentifier('courses');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('firstName')
            ->add('lastName')
            ->add('courses')
            ->add('grades')
            ->add('classes');

    }
}