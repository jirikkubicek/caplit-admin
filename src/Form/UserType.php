<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                "username",
                TextType::class,
                ["label" => "Uživatelské jméno"]
            )
            ->add(
                "password",
                RepeatedType::class,
                [
                    "type" => PasswordType::class,
                    "invalid_message" => "Hesla se neshodují",
                    "required" => $options["mode"] === "add",
                    "first_options" => [
                        "label" => "Heslo"
                    ],
                    "second_options" => [
                        "label" => "Potvrzení hesla"
                    ]
                ]
            )
            ->add(
                "name",
                TextType::class,
                [
                    "label" => "Jméno"
                ]
            )
            ->add(
                "email",
                EmailType::class,
                [
                    "label" => "E-mail"
                ]
            )
            ->add(
                "admin",
                CheckboxType::class,
                [
                    "label" => "Administrátor",
                    "required" => false,
                    "value" => 1
                ]
            )
            ->add(
                "submit",
                SubmitType::class,
                ["label" => $options["submitLabel"]]
            );
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                "submitLabel" => "Potvrdit",
                "mode" => "add"
            ]
        );

        $resolver->setAllowedTypes("submitLabel", "string");
        $resolver->setAllowedTypes("mode", "string");
    }
}
