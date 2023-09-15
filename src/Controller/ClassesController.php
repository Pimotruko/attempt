<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Form\ClassesType;
use App\Form\ClassesSearchType;
use App\Repository\ClassesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/classes')]
class ClassesController extends AbstractController
{
    #[Route('/', name: 'app_classes_index', methods: ['GET', 'POST'])]
    public function index(Request $request,ClassesRepository $classesRepository): Response
    {
        $form = $this->createForm(ClassesSearchType::class);
        $form->handleRequest($request);

        $sortProperty = $request->query->get('sort', 'name'); // Default sorting column
        $orderDirection = $request->query->get('direction', 'ASC'); // Default sorting direction

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->get('field')->getData();
            $query = $form->get('value')->getData();
            $queryBuilder = $classesRepository->findByField($field, $query);

        } else {
            // Create the query builder with sorting parameters
            $queryBuilder = $classesRepository->createQueryBuilder('s')
                ->orderBy('s.' . $sortProperty, $orderDirection);
        }

        $page = $request->query->getInt('page', 1);

        $perPage = 10;

        $offset = ($page - 1) * $perPage;

        $totalItems = count($queryBuilder->getQuery()->getResult());

        $totalPages = ceil($totalItems / $perPage);
        //dd($totalPages);
        $queryBuilder->setFirstResult($offset)->setMaxResults($perPage);

        $classes = $queryBuilder->getQuery()->getResult();
        

        return $this->render('classes/index.html.twig', [
            'classes' => $classes,
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sortProperty' => $sortProperty,
            'orderDirection' => $orderDirection,
        ]);
    }

    #[Route('/new', name: 'app_classes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $class = new Classes();
        $form = $this->createForm(ClassesType::class, $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($class);
            $entityManager->flush();

            return $this->redirectToRoute('app_classes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('classes/new.html.twig', [
            'class' => $class,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_classes_show', methods: ['GET'])]
    public function show(Classes $class): Response
    {
        return $this->render('classes/show.html.twig', [
            'class' => $class,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_classes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classes $class, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClassesType::class, $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_classes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('classes/edit.html.twig', [
            'class' => $class,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_classes_delete', methods: ['POST'])]
    public function delete(Request $request, Classes $class, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$class->getId(), $request->request->get('_token'))) {
            $entityManager->remove($class);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_classes_index', [], Response::HTTP_SEE_OTHER);
    }
}
