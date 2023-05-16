<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\Type\SectionType;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Messages;
use App\Service\Section as SectionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class SectionController
{
    private const ROUTE_NAME_LIST = "sections_show";
    private const ROUTE_NAME_ADD = "sections_add";
    private const ROUTE_NAME_EDIT = "sections_edit";
    private const ROUTE_NAME_REMOVE = "sections_remove";
    private const ROUTE_PATH_BASE = "/sections";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        private SectionService $SectionService,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $this->CRMController = $BaseCRMControllerBuilder
            ->setService($SectionService)
            ->setFormTypeName(SectionType::class)
            ->setEntity(Section::class)
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "sections.html.twig",
                self::ROUTE_NAME_LIST
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_ADD_KEY_NAME,
                "crm/base_add_edit.html.twig",
                self::ROUTE_NAME_ADD
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_EDIT_KEY_NAME,
                "crm/base_add_edit.html.twig",
                self::ROUTE_NAME_EDIT
            )
            ->addTemplate(
                BaseCRMController::TEMPLATES_REMOVE_KEY_NAME,
                "",
                self::ROUTE_NAME_REMOVE
            )
            ->addMessages([
                [
                    Messages::ACTION_KEY_NAME => "list",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přehled sekcí"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání sekce"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava sekce"
                    ]
            ])
            ->build();
    }

    #[Route(self::ROUTE_PATH_BASE . "/list/{orderBy}/{direction}", self::ROUTE_NAME_LIST)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response
    {
        return $this->CRMController->list($orderBy, $direction);
    }

    #[Route(self::ROUTE_PATH_BASE . "/add/{id}", self::ROUTE_NAME_ADD, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->add($Request, $id);
    }

    #[Route(self::ROUTE_PATH_BASE . "/edit/{id}", self::ROUTE_NAME_EDIT, requirements: ["id" => "\d+"])]
    public function showEditPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->edit($Request, $id);
    }

    #[Route(self::ROUTE_PATH_BASE . "/remove/{id}", self::ROUTE_NAME_REMOVE, requirements: ["id" => "\d+"])]
    public function showRemovePage(?int $id = null): Response
    {
        return $this->CRMController->remove($id);
    }
}
