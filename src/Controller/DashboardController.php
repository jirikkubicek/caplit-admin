<?php

namespace App\Controller;

use App\Service\Meal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
final class DashboardController extends AbstractController
{
    /**
     * @param Meal $meal
     * @return Response
     */
    #[Route("/", "dashboard")]
    public function index(Meal $meal): Response
    {
        $meal->buildActualMenu();

        return $this->render(
            "/dashboard.html.twig",
            [
                "header" => "Dashboard",
                "actualMenu" => $meal->getActualMenu()
            ]
        );
    }
}
