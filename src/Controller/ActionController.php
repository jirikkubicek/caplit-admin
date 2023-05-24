<?php

namespace App\Controller;

use App\Entity\Actions;
use App\Form\Type\ActionType;
use App\Service\CRMService;
use App\Service\MessagesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class ActionController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/actions";
    private const LIST_ROUTE_NAME = "actions_list";
    private const ADD_ROUTE_NAME = "actions_add";
    private const EDIT_ROUTE_NAME = "actions_edit";
    private const REMOVE_ROUTE_NAME = "actions_remove";

    /**
     * @param CRMService $service
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(
        CRMService $service,
        ValidatorInterface $validator,
        MessagesInterface $messages
    ) {
        parent::__construct($validator, $messages);

        $service->setEntityClassName(Actions::class);

        $this
            ->setEntityClassName(Actions::class)
            ->setFormTypeName(ActionType::class)
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "actions.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled akcí",
                "add.header" => "Přidání akce",
                "edit.header" => "Úprava akce",
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

    #[Route(self::BASE_ROUTE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function add(Request $request, ?int $id = null): Response
    {
        return parent::add($request, $id);
    }

    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $request, ?int $id = null): Response
    {
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
