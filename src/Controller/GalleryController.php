<?php

namespace App\Controller;

use App\Entity\Gallery as EntityGallery;
use App\Form\Type\GalleryType;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Gallery;
use App\Service\Messages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class GalleryController
{
    private const BASE_ROUTE = "/gallery";
    private const LIST_ROUTE_NAME = "gallery_list";
    private const ADD_ROUTE_NAME = "gallery_add";
    private const EDIT_ROUTE_NAME = "gallery_edit";
    private const REMOVE_ROUTE_NAME = "gallery_remove";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        Gallery $GalleryService,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $this->CRMController = $BaseCRMControllerBuilder
            ->setEntity(EntityGallery::class)
            ->setFormTypeName(GalleryType::class)
            ->setService($GalleryService)
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "gallery.html.twig",
                self::LIST_ROUTE_NAME
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_ADD_KEY_NAME,
                "gallery_add_edit.html.twig",
                self::ADD_ROUTE_NAME
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_EDIT_KEY_NAME,
                "gallery_add_edit.html.twig",
                self::EDIT_ROUTE_NAME
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_REMOVE_KEY_NAME,
                "",
                self::REMOVE_ROUTE_NAME
            )
            ->addMessages([
                [
                    Messages::ACTION_KEY_NAME => "list",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přehled obrázků"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání obrázku"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava obrázku"
                    ]
            ])
            ->build();
    }

    #[Route(self::BASE_ROUTE . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response
    {
        return $this->CRMController->list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->add($Request, $id);
    }

    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function editPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->edit($Request, $id);
    }

    #[Route(self::BASE_ROUTE . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function removePage(?int $id = null): Response
    {
        return $this->CRMController->remove($id);
    }
}
