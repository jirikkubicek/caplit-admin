<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\Type\SectionType;
use App\Service\Messages;
use App\Service\Section as SectionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")] 
class SectionController extends BaseCRMController {
    const ROUTE_NAME_LIST = "sections_show";
    const ROUTE_NAME_ADD = "sections_add";
    const ROUTE_NAME_EDIT = "sections_edit";
    const ROUTE_NAME_REMOVE = "sections_remove";
    const ROUTE_PATH_BASE = "/sections";

    public function __construct(
        private SectionService $SectionService,
        private ValidatorInterface $ValidatorInterface
    ) { 
        parent::__construct($ValidatorInterface);

        $this
            ->setService($SectionService)
            ->setFormTypeName(SectionType::class)
            ->setEntity(new Section())
            ->addTemplate(PARENT::TEMPLATES_LIST_KEY_NAME, "sections.html.twig", self::ROUTE_NAME_LIST)
            ->addTemplate(PARENT::TEMPLATES_ADD_KEY_NAME, "crm/base_add_edit.html.twig", self::ROUTE_NAME_ADD)
            ->addTemplate(PARENT::TEMPLATES_EDIT_KEY_NAME, "crm/base_add_edit.html.twig", self::ROUTE_NAME_EDIT)
            ->addTemplate(PARENT::TEMPLATES_REMOVE_KEY_NAME, "", self::ROUTE_NAME_REMOVE)
            ->Messages->addMessages([
                [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přehled sekcí"],
                [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přidání sekce"],
                [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Úprava sekce"]
            ]);
    }

    #[Route(self::ROUTE_PATH_BASE . "/list/{orderBy}/{direction}", self::ROUTE_NAME_LIST)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response {
        return $this->list($orderBy, $direction);
    }

    #[Route(self::ROUTE_PATH_BASE . "/add/{id}", self::ROUTE_NAME_ADD, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response {
        return $this->add($Request, $id);
    }

    #[Route(self::ROUTE_PATH_BASE . "/edit/{id}", self::ROUTE_NAME_EDIT, requirements: ["id" => "\d+"])]
    public function showEditPage(Request $Request, ?int $id = null): Response {
        return $this->edit($Request, $id);
    }

    #[Route(self::ROUTE_PATH_BASE . "/remove/{id}", self::ROUTE_NAME_REMOVE, requirements: ["id" => "\d+"])]
    public function showRemovePage(?int $id = null): Response {
        return $this->remove($id);
    }
}