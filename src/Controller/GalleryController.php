<?php

namespace App\Controller;

use App\Entity\Gallery as EntityGallery;
use App\Form\Type\GalleryType;
use App\Service\Gallery;
use App\Service\MessagesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class GalleryController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/gallery";
    private const LIST_ROUTE_NAME = "gallery_list";
    private const ADD_ROUTE_NAME = "gallery_add";
    private const EDIT_ROUTE_NAME = "gallery_edit";
    private const REMOVE_ROUTE_NAME = "gallery_remove";

    /**
     * @param Gallery $service
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(
        Gallery $service,
        ValidatorInterface $validator,
        MessagesInterface $messages
    ) {
        parent::__construct($validator, $messages);

        $service->setEntityClassName(EntityGallery::class);

        $this
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
        return parent::list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE . "/add", self::ADD_ROUTE_NAME)]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function addPage(Request $request): Response
    {
        $this->setFormOptions(["mode" => "add"]);
        return parent::add($request, null);
    }

    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $request, ?int $id = null): Response
    {
        $this->setFormOptions(["mode" => "edit"]);
        return parent::edit($request, $id);
    }

    #[Route(self::BASE_ROUTE . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param integer|null $id
     * @return Response
     */
    public function remove(?int $id = null): Response
    {
        return parent::remove($id);
    }
}
