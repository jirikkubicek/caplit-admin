<?php

namespace App\Controller;

use App\Entity\Settings as EntitySettings;
use App\Form\Type\SettingsType;
use App\Service\Messages;
use App\Service\Settings;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
class SettingsController extends BaseCRMController {
    const BASE_ROUTE = "/settings";
    const LIST_ROUTE_NAME = "settings_list";
    const ADD_ROUTE_NAME = "settings_add";
    const EDIT_ROUTE_NAME = "settings_edit";
    const REMOVE_ROUTE_NAME = "settings_remove";

    public function __construct(ValidatorInterface $Validator, Settings $SettingsService, private Security $Security) {
        parent::__construct($Validator);

        $this
            ->setEntity(new EntitySettings)
            ->setFormTypeName(SettingsType::class)
            ->setFormOptions(["isAdmin" => $this->Security->isGranted("ROLE_ADMIN")])
            ->setService($SettingsService)
            ->addTemplate(self::TEMPLATES_LIST_KEY_NAME, "settings.html.twig", self::LIST_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_ADD_KEY_NAME, "crm/base_add_edit.html.twig", self::ADD_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_EDIT_KEY_NAME, "crm/base_add_edit.html.twig", self::EDIT_ROUTE_NAME)
            ->addTemplate(self::TEMPLATES_REMOVE_KEY_NAME, "", self::REMOVE_ROUTE_NAME)
            ->Messages->addMessages([
                [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Nastavení"],
                [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přidání hodnoty"],
                [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Úprava hodnoty"]
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