<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PageNotFoundController extends AbstractController
{
    /**
     * @return Response
     */
    public function pageNotFound(): Response
    {
        $this->addFlash("error", "Požadovaná stránka nebyla nalezena");
        return $this->redirectToRoute("dashboard");
    }
}