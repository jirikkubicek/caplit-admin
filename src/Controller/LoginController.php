<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\Type\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController {
    public function __construct(
        private EntityManagerInterface $EntityManager, 
        private UserPasswordHasherInterface $UserPasswordHasher,
        private Security $Security
    ) { }

    #[Route("/login", "login")]
    public function index(Request $Request): Response {
        if($this->Security->isGranted("IS_AUTHENTICATED")) {
            return $this->redirectToRoute("dashboard");
        }

        $LoginForm = $this->createForm(LoginType::class);
        $LoginForm->handleRequest($Request);

        if($LoginForm->isSubmitted() && $LoginForm->isValid()) {
            $username = $LoginForm->getData()["username"];
            $password = $LoginForm->getData()["password"];

            $User = $this->EntityManager->getRepository(Users::class)->findOneBy(["username" => $username]);

            if($User !== null) {
                if(!$this->UserPasswordHasher->isPasswordValid($User, $password)) { 
                    $this->addFlash("error", "Zadali jste chybné heslo.");
                } else {
                    $this->Security->login($User, "form_login");
                    $this->addFlash("success", "Vše v pořádku. Přihlášení probíhá.");
                    return $this->redirectToRoute("dashboard");
                }
            } else {
                $this->addFlash("error", "Uživatel nenalezen.");
            }
        } 

    return $this->render("login.html.twig", ["LoginForm" => $LoginForm, "header" => "Přihlášení"]);
    }

    #[Route("/logout", "logout")]
    public function logout(): Response {
        $this->Security->logout();

        return $this->redirectToRoute("login");
    }

    private function createAdminUser(): void {
        $User = new Users();
        $User
            ->setName("Honza")
            ->setUsername("admin")
            ->setPassword($this->UserPasswordHasher->hashPassword($User, "heslo"))
            ->setEmail("jiri@kubickovi.cz");

        $this->EntityManager->persist($User);
        $this->EntityManager->flush();
    }
}