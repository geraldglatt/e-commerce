<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    protected $calculator;

    public function __construct(Calculator $calculator) {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{prenom<\w+>?world}", name="hello", methods={"GET", "POST"},
     * host="127.0.0.1", schemes={"http", "https"})
     */
    public function hello (Request $request, $prenom, LoggerInterface $logger, Slugify $slugify, Environment $twig)
    {
        dump($twig);

        dump($slugify->slugify("Hello world"));

        $logger->error("Mon message de log");

        $tva = $this->calculator->calcul(100);
        dd($tva);

        return new Response("hello $prenom");
    }
}