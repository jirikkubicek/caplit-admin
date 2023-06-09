<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextsType extends AbstractType
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
                "header",
                TextType::class,
                [
                    "label" => "Nadpis",
                    "required" => false
                ]
            )
            ->add(
                "text",
                TextareaType::class,
                [
                    "label" => "Text",
                    "required" => false
                ]
            )
            ->add(
                "textSection",
                ChoiceType::class,
                [
                    "label" => "Sekce",
                    "choices" => $options["choices"],
                    "choice_label" => "name",
                    "choice_value" => "id",
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
                "choices" => []
            ]
        );

        $resolver->setAllowedTypes("submitLabel", "string");
        $resolver->setAllowedTypes("choices", "array");
    }
}
