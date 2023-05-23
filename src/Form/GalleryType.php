<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GalleryType extends AbstractType
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
                "title",
                TextType::class,
                [
                    "label" => "Popisek",
                    "required" => false
                ]
            )
            ->add(
                "photo_order",
                NumberType::class,
                [
                    "label" => "Pořadí",
                    "required" => false
                ]
            )
            ->add(
                "filename",
                FileType::class,
                [
                    "label" => "Obrázek",
                    "mapped" => false,
                    "constraints" => [
                        new File(
                            [
                                "mimeTypes" =>
                                    [
                                        "image/jpeg",
                                        "image/png"
                                    ],
                                "mimeTypesMessage" => "Podporované jsou soubory JPG nebo PNG"
                            ]
                        )
                    ]
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
        $resolver->setDefaults(["submitLabel" => "Potvrdit"]);
        $resolver->setAllowedTypes("submitLabel", "string");
    }
}
