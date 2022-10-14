<?php

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/hello/{prenom?world}", name="hello", methods={"GET", "POST"},
     * host="127.0.0.1", schemes={"http", "https"})
     */
    public function hello ($prenom)
    {
        // $html = $this->twig->myRender('hello.html.twig', [
        //     'prenom' => $prenom,
        // ]);
        return $this->render('hello.html.twig', [
            'prenom' => $prenom
        ]);
    }

    /**
     * @Route("/example", name="example", methods={"GET", "POST"})
     */
    public function example()
    {
        return $this->render('example.html.twig', [
            'age' => 33
        ]);
    }
    protected function myRender(string $path, array $variables = [])
    {
        $html = $this->twig->render($path, $variables);
        return new Response($html);
    }
}