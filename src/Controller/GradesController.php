<?php

namespace App\Controller;

use App\Entity\Grades;
use App\Form\GradesType;
use App\Form\GradesSearchType;
use App\Repository\GradesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/grades')]
class GradesController extends AbstractController
{
    #[Route('/', name: 'app_grades_index', methods: ['GET', 'POST'])]
    public function index(Request $request,GradesRepository $gradesRepository): Response
    {
        $form = $this->createForm(GradesSearchType::class);
        $form->handleRequest($request);

        $sortProperty = $request->query->get('sort', 'student.firstName'); // Default sorting column
        $orderDirection = $request->query->get('direction', 'ASC'); // Default sorting direction

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->get('field')->getData();
            $query = $form->get('value')->getData();
            #dd($field);
            $queryBuilder = $gradesRepository->findByField($field, $query);

        } else {
            // Create the query builder with sorting parameters
            $queryBuilder = $gradesRepository->createQueryBuilder('g')
                ->leftJoin('g.student', 'student')
                ->leftJoin('g.course', 'course')
                ->orderBy($sortProperty, $orderDirection);
        }

        $page = $request->query->getInt('page', 1);

        $perPage = 5;

        $offset = ($page - 1) * $perPage;

        $totalItems = count($queryBuilder->getQuery()->getResult());

        $totalPages = ceil($totalItems / $perPage);
        //dd($totalPages);
        $queryBuilder->setFirstResult($offset)->setMaxResults($perPage);

        $grades = $queryBuilder->getQuery()->getResult();


        return $this->render('grades/index.html.twig', [
            'grades' => $grades,
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sortProperty' => $sortProperty,
            'orderDirection' => $orderDirection,
        ]);
    }

    #[Route('/new', name: 'app_grades_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $grade = new Grades();
        $form = $this->createForm(GradesType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grades/new.html.twig', [
            'grade' => $grade,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_grades_show', methods: ['GET'])]
    public function show(Grades $grade): Response
    {
        return $this->render('grades/show.html.twig', [
            'grade' => $grade,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_grades_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GradesType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grades/edit.html.twig', [
            'grade' => $grade,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_grades_delete', methods: ['POST'])]
    public function delete(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$grade->getId(), $request->request->get('_token'))) {
            $entityManager->remove($grade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    }
}
