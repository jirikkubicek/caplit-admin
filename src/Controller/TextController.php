<?php

namespace App\Controller;

use App\Entity\Text as EntityText;
use App\Entity\TextSection;
use App\Form\Type\TextsType;
use App\Service\CRMController;
use App\Service\CRMService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
final class TextController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/texts";
    private const LIST_ROUTE_NAME = "texts_list";
    private const ADD_ROUTE_NAME = "texts_add";
    private const EDIT_ROUTE_NAME = "texts_edit";
    private const REMOVE_ROUTE_NAME = "texts_remove";

    /**
     * @param CRMService $service
     * @param CRMController $CRM
     */
    public function __construct(
        CRMService $service,
        private CRMController $CRM
    ) {
        $service->setEntityClassName(EntityText::class);

        $textSectionService = clone $service;
        $textSectionService->setEntityClassName(TextSection::class);

        $this->CRM
            ->setEntityClassName(EntityText::class)
            ->setFormTypeName(TextsType::class)
            ->setFormOptions([
                "choices" => $textSectionService->findAll()
            ])
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "texts.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled textů",
                "add.header" => "Přidání textu",
                "edit.header" => "Úprava textu",
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
