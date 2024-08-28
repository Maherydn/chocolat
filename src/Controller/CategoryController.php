<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/category', name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $category,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'update', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function update(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/update.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('admin.category.index');
    }

}
