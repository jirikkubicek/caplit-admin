<?php

namespace App\Controller;

use App\Entity\Gallery as EntityGallery;
use App\Form\Type\GalleryType;
use App\Service\Gallery;
use App\Service\Messages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
class GalleryController extends BaseCRMController {
    const BASE_ROUTE = "/gallery";
    const LIST_ROUTE_NAME = "gallery_list";
    const ADD_ROUTE_NAME = "gallery_add";
    const EDIT_ROUTE_NAME = "gallery_edit";
    const REMOVE_ROUTE_NAME = "gallery_remove";

    public function __construct(ValidatorInterface $Validator, Gallery $GalleryService) {
        parent::__construct($Validator);

        $this
            ->setEntity(new EntityGallery)
            ->setFormTypeName(GalleryType::class)
            ->setService($GalleryService)
            ->addTemplate(self::TEMPLATES_LIST_KEY_NAME, "gallery.html.twig", self::LIST_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_ADD_KEY_NAME, "gallery_add_edit.html.twig", self::ADD_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_EDIT_KEY_NAME, "gallery_add_edit.html.twig", self::EDIT_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_REMOVE_KEY_NAME, "", self::REMOVE_ROUTE_NAME)
            ->Messages->addMessages([
                [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přehled obrázků"],
                [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přidání obrázku"],
                [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Úprava obrázku"]
            ]); 
    }

    #[Route(self::BASE_ROUTE . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response {
        return $this->list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response {
        return $this->add($Request, $id);
    }

    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function editPage(Request $Request, ?int $id = null): Response {
        return $this->edit($Request, $id);
    }

    #[Route(self::BASE_ROUTE . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function removePage(?int $id = null): Response {
        return $this->remove($id);
    }
}