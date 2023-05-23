<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\Type\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Security $security
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private Security $security
    ) {
    }

    #[Route("/login", "login")]
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->security->isGranted("IS_AUTHENTICATED")) {
            return $this->redirectToRoute("dashboard");
        }

        $loginForm = $this->createForm(LoginType::class);
        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $loginFormData = $loginForm->getData();

            if (is_array($loginFormData) && isset($loginFormData["username"]) && isset($loginFormData["password"])) {
                $username = $loginFormData["username"];
                $password = $loginFormData["password"];

                $user = $this->entityManager->getRepository(Users::class)->findOneBy(["username" => $username]);

                if ($user !== null && $this->userPasswordHasher->isPasswordValid($user, $password)) {
                    $this->security->login($user, "form_login");

                    $this->addFlash("success", "Byl jste úspěšně přihlášen");

                    return $this->redirectToRoute("dashboard");
                } else {
                    $this->addFlash("error", "Zadali jste chybné jméno nebo heslo");
                }
            } else {
                throw new Exception("An error occurred during login");
            }
        }

        return $this->render(
            "login.html.twig",
            [
                "loginForm" => $loginForm,
                "header" => "Přihlášení"
            ]
        );
    }

    #[Route("/logout", "logout")]
    /**
     * @return Response
     */
    public function logout(): Response
    {
        $this->addFlash("success", "Byl jste úspěšně odhlášen");

        $this->security->logout(false);

        return $this->redirectToRoute("login");
    }

    /**
     * @param Users $user
     * @return Response
     */
    private function registerUser(Users $user): Response
    {
        $userAlreadyExists = $this->entityManager
            ->getRepository(Users::class)
            ->findOneBy(["username" => $user->getUsername()]) !== null;
        if ($userAlreadyExists === true) {
            $this->addFlash("error", "Uživatel s tímto jménem již existuje");
            return $this->redirectToRoute("login");
        }

        $password = (string) $user->getPassword();
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $this->addFlash("error", "Registrace se nezdařila");
            return $this->redirectToRoute("login");
        }

        $this->addFlash("success", "Byl jste úspěšně zaregistrován");
        return $this->redirectToRoute("login");
    }
}
