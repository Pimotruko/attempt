<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Student;
use App\Form\StudentType;
use App\Form\SearchType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'app_student_index', methods: ['GET', 'POST'])]
   public function index(Request $request, StudentRepository $studentRepository)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $sortProperty = $request->query->get('sort', 'firstName'); // Default sorting column
        $orderDirection = $request->query->get('direction', 'ASC'); // Default sorting direction

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->get('field')->getData();
            $query = $form->get('value')->getData();
            $queryBuilder = $studentRepository->findByField($field, $query);

        } else {
            // Create the query builder with sorting parameters
            $queryBuilder = $studentRepository->createQueryBuilder('s')
                ->orderBy('s.' . $sortProperty, $orderDirection);
        }

        $page = $request->query->getInt('page', 1);

        $perPage = 5;

        $offset = ($page - 1) * $perPage;

        $totalItems = count($queryBuilder->getQuery()->getResult());

        $totalPages = ceil($totalItems / $perPage);
        //dd($totalPages);
        $queryBuilder->setFirstResult($offset)->setMaxResults($perPage);

        $students = $queryBuilder->getQuery()->getResult();


        return $this->render('student/index.html.twig', [
            'students' => $students,
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sortProperty' => $sortProperty,
            'orderDirection' => $orderDirection,
        ]);
    }

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add courses to the student
            foreach ($student->getCourses() as $course) {
                $course->addStudent($student);
            }

            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $originalCourses = new ArrayCollection();
    
        // Create a copy of the student's courses before form submission
        foreach ($student->getCourses() as $course) {
            $originalCourses->add($course);
        }
    
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Remove courses that were unselected in the form
            foreach ($originalCourses as $course) {
                if (!$student->getCourses()->contains($course)) {
                    $course->removeStudent($student);
                    $entityManager->persist($course); // Update the course entity
                }
            }
    
            // Add courses to the student
            foreach ($student->getCourses() as $course) {
                $course->addStudent($student);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
    }
}
