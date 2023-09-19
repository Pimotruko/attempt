<?php

namespace App\Admin;

use App\Entity\Course;
use App\Entity\Classes;
use App\Entity\Student;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CourseAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
        ->with('Course Info', ['class' => 'col-md-6'])
            ->add('name')
            ->add('description')
        ->end()

        ->with('Students', ['class' => 'col-md-3'])
            ->add('students', ModelType::class, [
                'class' => Student::class,
                'multiple' => true, // If it's a ManyToMany relationship
                'expanded' => true, // If you want checkboxes for multiple selection
            ])
        ->end()
        
        ->with('Classes', ['class' => 'col-md-3'])
            ->add('classes', ModelType::class, [
                'class'=>Classes::class,
                'multiple' => true, // If it's a ManyToMany relationship
                'expanded' => true, // If you want checkboxes for multiple selection
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->addIdentifier('description');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description')
            ->add('students');
    }

    public function toString(object $object): string
    {
        return $object instanceof Course
            ? $object->getName()
            : 'Course';
    }

}