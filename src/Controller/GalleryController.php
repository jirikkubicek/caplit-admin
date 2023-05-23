<?php

namespace App\Controller;

use App\Entity\Gallery as EntityGallery;
use App\Form\Type\GalleryType;
use App\Service\CRMController;
use App\Service\Gallery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
final class GalleryController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/gallery";
    private const LIST_ROUTE_NAME = "gallery_list";
    private const ADD_ROUTE_NAME = "gallery_add";
    private const EDIT_ROUTE_NAME = "gallery_edit";
    private const REMOVE_ROUTE_NAME = "gallery_remove";

    /**
     * @param Gallery $service
     * @param CRMController $CRM
     */
    public function __construct(
        Gallery $service,
        private CRMController $CRM
    ) {
        $service->setEntityClassName(EntityGallery::class);

        $this->CRM
            ->setEntityClassName(EntityGallery::class)
            ->setFormTypeName(GalleryType::class)
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "gallery.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME, "gallery_add_edit.html.twig")
            ->setEditAction(self::EDIT_ROUTE_NAME, "gallery_add_edit.html.twig")
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled obrázků",
                "add.header" => "Přidání obrázku",
                "edit.header" => "Úprava obrázku",
            ]);
    }

    #[Route(self::BASE_ROUTE . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    /**
     * @param string|null $orderBy
     * @param string|null $direction
     * @return Response
     */
    public function list(?string $orderBy = null, ?string $direction = null): Response
    {
        return $this->CRM->list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function add(Request $request, ?int $id = null): Response
    {
        return $this->CRM->add($request, $id);
    }

    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $request, ?int $id = null): Response
    {
        return $this->CRM->edit($request, $id);
    }

    #[Route(self::BASE_ROUTE . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param integer|null $id
     * @return Response
     */
    public function remove(?int $id = null): Response
    {
        return $this->CRM->remove($id);
    }
}
