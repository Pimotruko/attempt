<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Form\CourseSearchType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/course')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'app_course_index', methods: ['GET', 'POST'])]
    public function index(Request $request,CourseRepository $courseRepository): Response
    {
        $form = $this->createForm(CourseSearchType::class);
        $form->handleRequest($request);

        $sortProperty = $request->query->get('sort', 'name'); // Default sorting column
        $orderDirection = $request->query->get('direction', 'ASC'); // Default sorting direction

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->get('field')->getData();
            $query = $form->get('value')->getData();
            $queryBuilder = $courseRepository->findByField($field, $query);

        } else {
            // Create the query builder with sorting parameters
            $queryBuilder = $courseRepository->createQueryBuilder('s')
                ->orderBy('s.' . $sortProperty, $orderDirection);
        }

        $page = $request->query->getInt('page', 1);

        $perPage = 10;

        $offset = ($page - 1) * $perPage;

        $totalItems = count($queryBuilder->getQuery()->getResult());

        $totalPages = ceil($totalItems / $perPage);
        //dd($totalPages);
        $queryBuilder->setFirstResult($offset)->setMaxResults($perPage);

        $courses = $queryBuilder->getQuery()->getResult();
        

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sortProperty' => $sortProperty,
            'orderDirection' => $orderDirection,
        ]);
    }

    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($course->getClasses() as $class) {
                $class->addCourse($course);
            }

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $originalClasses = new ArrayCollection();

        foreach ($course->getClasses() as $class) {
            $originalClasses->add($class);
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalClasses as $class) {
                if (!$course->getClasses()->contains($class)) {
                    $class->removeCourse($course);
                    $entityManager->persist($class);
                }
            }

            foreach ($course->getClasses() as $class) {
                $class->addCourse($course);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
