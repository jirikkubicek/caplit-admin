<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<string,mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                "username",
                TextType::class,
                ["label" => "Uživatelské jméno: "]
            )
            ->add(
                "password",
                PasswordType::class,
                ["label" => "Heslo: "]
            )
            ->add(
                "login",
                SubmitType::class,
                ["label" => "Přihlásit"]
            );
    }
}
