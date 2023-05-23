<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
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
                ["label" => "Název: "]
            )
            ->add(
                "description",
                TextareaType::class,
                [
                    "label" => "Popis: ",
                    "required" => false
                ]
            )
            ->add(
                "show_courses",
                CheckboxType::class,
                [
                    "label" => "Zobrazovat chody",
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
        $resolver->setDefault("submitLabel", "Potvrdit");
        $resolver->setAllowedTypes("submitLabel", "string");
    }
}
