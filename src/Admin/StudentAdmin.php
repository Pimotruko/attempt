<?php

namespace App\Admin;

use App\Entity\Course;
use app\Entity\Student;
use Doctrine\ORM\Mapping\Entity;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class StudentAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Student Info', ['class' => 'col-md-9'])
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('dateOfBirth')
            ->end()

            ->with('Course Info', ['class' => 'col-md-3'])
                ->add('courses', ModelType::class, [
                    'class' => Course::class,
                    'multiple' => true,
                    'expanded' => true,
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
            ->add('classes')
            ->add('createdAt', null, ['label' => 'Created At', 'format' => 'Y-m-d H:i:s'])
            ->add('modifiedAt', null, ['label' => 'Modified At', 'format' => 'Y-m-d H:i:s']);
    }
}