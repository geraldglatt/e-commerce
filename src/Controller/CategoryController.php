<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderListCategory() 
    {
        //1. Aller chercher les catégories dans la base de données
        //(besoin donc d'un repository)
        $categories = $this->categoryRepository->findAll();

        //2. Renvoyer le rendu html sous la forme d'une Response($this-#render())
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request,SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

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
    public function edit($id,CategoryRepository $categoryRepository, EntityManagerInterface $em,Request $request,Security $security): Response
    {
        // $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas accès à cette ressource");
        //le code du dessus remplace le code commenté du dessous !
        // $user = $security->getUser();
        // $user = $this->getUser();

        // if($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if($this->isGranted("ROLE_ADMIN") === false) {
        //     throw new AccessDeniedHttpException("vous n'avez pas le droit d'accéder à cette ressource");
        // }

        $category = $categoryRepository->find($id);
        //si la catégorie n'existe pas,on lance une new notfoundHttpException
        if(!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas !"); 
        }

        // $security->isGranted('CAN_EDIT', $category);

        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie");

        //on récupère le user actuel
        // $user = $this->getUser();
        //si il n'est pas connecté on le redirige
        // if(!$user) {
        //     return $this->redirectToRoute("security_login");
        // }
        // //si ce n'est pas le propriétaire alors on lance une exception
        // if($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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
