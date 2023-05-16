<?php

namespace App\Controller;

use App\Entity\Settings as EntitySettings;
use App\Form\Type\SettingsType;
use App\Service\BaseCRM;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Messages;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class SettingsController
{
    private const BASE_ROUTE = "/settings";
    private const LIST_ROUTE_NAME = "settings_list";
    private const ADD_ROUTE_NAME = "settings_add";
    private const EDIT_ROUTE_NAME = "settings_edit";
    private const REMOVE_ROUTE_NAME = "settings_remove";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        BaseCRM $Service,
        private Security $Security,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $Service->setEntityClassName(EntitySettings::class);

        $this->CRMController = $BaseCRMControllerBuilder
            ->setEntity(EntitySettings::class)
            ->setFormTypeName(SettingsType::class)
            ->setFormOptions([
                "isAdmin" => $this->Security->isGranted("ROLE_ADMIN")
                ])
            ->setService($Service)
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "settings.html.twig",
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
            ->addTemplate(
                BaseCRMController::TEMPLATES_REMOVE_KEY_NAME,
                "",
                self::REMOVE_ROUTE_NAME
            )
            ->addMessages([
                [
                    Messages::ACTION_KEY_NAME => "list",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Nastavení"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání hodnoty"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava hodnoty"
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
