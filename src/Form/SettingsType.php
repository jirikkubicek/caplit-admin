<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
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
                "name",
                TextType::class,
                [
                    "label" => "Název",
                    "disabled" => !$options["isAdmin"]
                ]
            )
            ->add(
                "caption",
                TextType::class,
                [
                    "label" => "Popis",
                    "required" => false
                ]
            )
            ->add(
                "value",
                TextType::class,
                [
                    "label" => "Hodnota",
                    "required" => false
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
                "isAdmin" => false
            ]
        );
        $resolver->setAllowedTypes("submitLabel", "string");
        $resolver->setAllowedTypes("isAdmin", "bool");
    }
}
