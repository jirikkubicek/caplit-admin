<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("name", TextType::class, ["label" => "Název"])
            ->add("price", TextType::class, ["label" => "Cena", "required" => false])
            ->add(
                "section",
                ChoiceType::class,
                [
                    "label" => "Sekce",
                    "choices" => $options["sections"],
                    "choice_value" => "id",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "course",
                ChoiceType::class,
                [
                    "label" => "Chod",
                    "choices" => $options["courses"],
                    "choice_value" => "id",
                    "choice_label" => "name"
                ]
            )
            ->add("invisible", CheckboxType::class, ["label" => "Skrýt", "required" => false, "value" => 1])
            ->add("submit", SubmitType::class, ["label" => $options["submitLabel"]]);

        $builder->get("price")->addModelTransformer(new CallbackTransformer(
            function ($price): string {
            // Transform saved data
                return str_replace(".", ",", $price);
            },
            function ($price): float {
            // Transform data before processed
                return (float)str_replace(",", ".", $price);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "submitLabel" => "Potvrdit",
            "sections" => [],
            "courses" => []
        ]);

        $resolver->setAllowedTypes("submitLabel", "string");
        $resolver->setAllowedTypes("sections", "array");
        $resolver->setAllowedTypes("courses", "array");
    }
}
