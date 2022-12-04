<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController 
{

    // plus besoin du constructeur car la methode etends de l'AbstractController
    // donc il nous donne déjà 
    // protected $security;
    // protected $router;
    // protected $twig;

    // public function __construct(Security $security, RouterInterface $router, Environment $twig)
    // {
        // $this->security = $security;
        // $this->router = $router;
        // $this->twig = $twig;
    // }
    
    #[Route("/purchases", name: "purchases_index")]
    #[IsGranted("ROLE_USER", message: "Vous devez être connecté pour accéder à vos commandes")]
    public function index() {
        // 1. Nous devons nous assurer que la personne est connectée(sinon 
        // redirection vers la page d'accueil)->nous avons besoin de la classe Security
        /** @var User */
        $user = $this->getUser();// ici on récupère un utilisateur

        //plus besoin du if car utilisation de IsGranted(attention à bien importer la classe) dans la route de la méthode index()
        //  if(!$user) {
            // Redirection -> RedirectResponse
            // Générer un url en fonction du nom d'une route -> UrlGenerator ou RouterInterface
        //    throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes !");
            
        //}
        // 2. Nous voulons savoir qui est connecté->nous avons besoin de la classe Security

        // 3. Nous voulons passer l'utilisateur connecté à twig afin 
        // d'afficher ses commandes->Nous avons besoin de Environment de twig/Response
        //plus besoin car utilisation  de $this->render() provenant de Httpfoundation
        //$html =  $this->twig->render("purchase/index.html.twig", [
            //'purchases' => $user->getPurchases()
        //]);
        //return new Response($html);
        return $this->render("purchase/index.html.twig", [
            'purchases' => $user->getPurchases()
        ]);
    }
}