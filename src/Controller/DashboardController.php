<?php

namespace App\Controller;

use App\Service\Meal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class DashboardController extends AbstractController {
    public function __construct(private Meal $Meal)
    {
        
    }

    #[Route("/dashboard", "dashboard")]
    public function index(): Response { 
        return $this->render("/dashboard.html.twig", ["header" => "Dashboard", "actualMenu" => $this->Meal->getActualMenu()]);
    }
}