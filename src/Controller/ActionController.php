<?php

namespace App\Controller;

use App\Entity\Actions;
use App\Form\Type\ActionType;
use App\Service\BaseCRM;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Messages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class ActionController
{
    private const BASE_ROUTE = "/actions";
    private const LIST_ROUTE_NAME = "actions_list";
    private const ADD_ROUTE_NAME = "actions_add";
    private const EDIT_ROUTE_NAME = "actions_edit";
    private const REMOVE_ROUTE_NAME = "actions_remove";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        BaseCRM $Service,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $Service->setEntityClassName(Actions::class);

        $this->CRMController = $BaseCRMControllerBuilder
            ->setEntity(Actions::class)
            ->setFormTypeName(ActionType::class)
            ->setService($Service)
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "actions.html.twig",
                self::LIST_ROUTE_NAME
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_ADD_KEY_NAME,
                "crm/base_add_edit.html.twig",
                self::ADD_ROUTE_NAME
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_EDIT_KEY_NAME,
                "crm/base_add_edit.html.twig",
                self::EDIT_ROUTE_NAME
            )
            ->addTemplate(BaseCRMController::TEMPLATES_REMOVE_KEY_NAME, "", self::REMOVE_ROUTE_NAME)
            ->addMessages([
                [
                    Messages::ACTION_KEY_NAME => "list",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přehled akcí"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání akce"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava akce"
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