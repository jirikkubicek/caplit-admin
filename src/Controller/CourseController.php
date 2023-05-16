<?php

namespace App\Controller;

use App\Entity\Course as CourseEntity;
use App\Form\Type\CourseType;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Course;
use App\Service\Messages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class CourseController
{
    private const ROUTE_BASE = "/course";
    private const LIST_ROUTE_NAME = "course_list";
    private const ADD_ROUTE_NAME = "course_add";
    private const EDIT_ROUTE_NAME = "course_edit";
    private const REMOVE_ROUTE_NAME = "course_remove";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        Course $Course,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $this->CRMController = $BaseCRMControllerBuilder
            ->setService($Course)
            ->setFormTypeName(CourseType::class)
            ->setEntity(CourseEntity::class)
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "courses.html.twig",
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
                    Messages::MESSAGE_KEY_NAME => "Přehled chodů"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání chodu"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava chodu"
                ]
            ])
            ->build();
    }

    #[Route(self::ROUTE_BASE . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response
    {
        return $this->CRMController->list($orderBy, $direction);
    }

    #[Route(self::ROUTE_BASE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->add($Request, $id);
    }

    #[Route(self::ROUTE_BASE . "/edit/{id}", self::EDIT_ROUTE_NAME)]
    public function editPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->edit($Request, $id);
    }

    #[Route(self::ROUTE_BASE . "/remove/{id}", self::REMOVE_ROUTE_NAME)]
    public function removePage(?int $id = null): Response
    {
        return $this->CRMController->remove($id);
    }
}
