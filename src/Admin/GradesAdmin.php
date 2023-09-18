<?php

namespace App\Admin;

use App\Entity\Student;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

final class GradesAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('student')
            ->add('course')
            ->add('class')
            ->add('grade');

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('student')
            ->add('course')
            ->add('grade');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('student')
            ->addIdentifier('course')
            ->addIdentifier('grade');
            
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('student')
            ->add('course')
            ->add('grade');
    }
}