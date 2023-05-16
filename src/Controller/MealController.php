<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Type\MealType;
use App\Service\BaseCRMController;
use App\Service\BaseCRMControllerBuilder;
use App\Service\BaseCRMControllerInterface;
use App\Service\Course;
use App\Service\Meal as ServiceMeal;
use App\Service\Messages;
use App\Service\Section;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
class MealController
{
    private const BASE_ROUTE_NAME = "/meal";
    private const LIST_ROUTE_NAME = "meal_list";
    private const ADD_ROUTE_NAME = "meal_add";
    private const EDIT_ROUTE_NAME = "meal_edit";
    private const REMOVE_ROUTE_NAME = "meal_remove";

    private BaseCRMControllerInterface $CRMController;

    public function __construct(
        ServiceMeal $MealService,
        Section $Section,
        Course $Course,
        BaseCRMControllerBuilder $BaseCRMControllerBuilder
    ) {
        $this->CRMController = $BaseCRMControllerBuilder
            ->setEntity(Meal::class)
            ->setService($MealService)
            ->setFormTypeName(MealType::class)
            ->setFormOptions([
                "sections" => $Section->findAll(),
                "courses" => $Course->findAll()
                ])
            ->addTemplate(
                BaseCRMController::TEMPLATES_LIST_KEY_NAME,
                "meals.html.twig",
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
                    Messages::MESSAGE_KEY_NAME => "Přehled jídel"
                ],
                [
                    Messages::ACTION_KEY_NAME => "add",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Přidání jídla"
                ],
                [
                    Messages::ACTION_KEY_NAME => "edit",
                    Messages::NAME_KEY_NAME => "header",
                    Messages::MESSAGE_KEY_NAME => "Úprava jídla"
                    ]
            ])
            ->build();
    }

    #[Route(self::BASE_ROUTE_NAME . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response
    {
        return $this->CRMController->list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE_NAME . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->add($Request, $id);
    }

    #[Route(self::BASE_ROUTE_NAME . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function editPage(Request $Request, ?int $id = null): Response
    {
        return $this->CRMController->edit($Request, $id);
    }

    #[Route(self::BASE_ROUTE_NAME . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function removePage(?int $id = null): Response
    {
        return $this->CRMController->remove($id);
    }
}
