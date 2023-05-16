<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, ["label" => "Název sekce"])
            ->add("submit", SubmitType::class, ["label" => $options["submitLabel"]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["submitLabel" => "Potvrdit"]);
        $resolver->setAllowedTypes("submitLabel", "string");
    }
}
