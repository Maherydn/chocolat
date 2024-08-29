<?php

namespace App\Controller\admin\article;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\SellType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/article', name: 'admin.article.')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $articles = $articleRepository->findByUser($userId);
        return $this->render('/admin/article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $article->setUser($user);

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('/admin/article/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'update', requirements: ['id' => Requirement::DIGITS], methods: ['POST', 'GET'])]
    public function update(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin.article.index');
        }
        return $this->render('/admin/article/update.html.twig', [
            'form' => $form,
            'article' => $article
        ]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Article $article, EntityManagerInterface $em)
    {
        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute('admin.article.index');
    }

    #[Route('/sell/{id}', name: 'sell', requirements: ['id' => Requirement::DIGITS], methods: ['POST', 'GET'])]
    public function sell(EntityManagerInterface $em, Article $article, Request $request): Response
    {
        $form = $this->createForm(SellType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $quantitySold = $form->get('quantitySold')->getData();

            if ($quantitySold <= $article->getQuantity()) {
                $article->setQuantity($article->getQuantity() - $quantitySold);
                $em->flush();
                return $this->redirectToRoute('admin.article.index');
            };

            if ($quantitySold > $article->getQuantity()) {
                return $this->redirectToRoute('admin.article.index');
            };

        }
        return $this->render('admin/article/sell.html.twig', [
            'form' => $form,
            'articles' => $article
        ]);
    }
}
