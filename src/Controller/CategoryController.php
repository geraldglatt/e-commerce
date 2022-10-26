<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request,SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted()) {

            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist($category);
            $em->flush();

        return $this->redirectToRoute('homepage');

        }
        $formview = $form->createView();

        return $this->render('category/create.html.twig', [
            'formview' => $formview
        ]);

        
    }

    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit($id,CategoryRepository $categoryRepository, EntityManagerInterface $em,Request $request): Response
    {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em->flush();

            return $this->redirectToRoute('homepage');

        }
        
        $formview = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formview' => $formview
        ]);
    }
}
