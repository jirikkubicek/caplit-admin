<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add("header", TextType::class, ["label" => "Nadpis"])
            ->add("date_from", DateTimeType::class, ["label" => "Platné od", "required" => false, "invalid_message" => "Platné od musí být ve formátu DD.MM.RRRR HH:MM", "widget" => "single_text"])
            ->add("date_to", DateTimeType::class, ["label" => "Platné do", "required" => false, "invalid_message" => "Platné do musí být ve formátu DD.MM.RRRR HH:MM", "widget" => "single_text"])
            ->add("text", TextareaType::class, ["label" => "Text", "required" => false])
            ->add("submit", SubmitType::class, ["label" => $options["submitLabel"]]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(["submitLabel" => "Potvrdit"]);
        $resolver->setAllowedTypes("submitLabel", "string");
    }
}