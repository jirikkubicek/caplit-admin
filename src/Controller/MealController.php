<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Type\MealType;
use App\Service\Course;
use App\Service\Meal as ServiceMeal;
use App\Service\Messages;
use App\Service\Section;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
class MealController extends BaseCRMController {
    const BASE_ROUTE_NAME = "/meal";
    const LIST_ROUTE_NAME = "meal_list";
    const ADD_ROUTE_NAME = "meal_add";
    const EDIT_ROUTE_NAME = "meal_edit";
    const REMOVE_ROUTE_NAME = "meal_remove";

    public function __construct(ValidatorInterface $Validator, ServiceMeal $MealService, Section $Section, Course $Course) {
        parent::__construct($Validator);

        $this
            ->setEntity(new Meal)
            ->setService($MealService)
            ->setFormTypeName(MealType::class)
            ->setFormOptions(["sections" => $Section->findAll(), "courses" => $Course->findAll()])
            ->addTemplate(PARENT::TEMPLATES_LIST_KEY_NAME, "meals.html.twig", self::LIST_ROUTE_NAME)
            ->addTemplate(PARENT::TEMPLATES_ADD_KEY_NAME, "crm/base_add_edit.html.twig", self::ADD_ROUTE_NAME)
            ->addTemplate(PARENT::TEMPLATES_EDIT_KEY_NAME, "crm/base_add_edit.html.twig", self::EDIT_ROUTE_NAME)
            ->addTemplate(PARENT::TEMPLATES_REMOVE_KEY_NAME, "", self::REMOVE_ROUTE_NAME)
            ->Messages->addMessages([
                [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přehled jídel"],
                [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přidání jídla"],
                [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Úprava jídla"]
            ]); 
    }
    
    #[Route(self::BASE_ROUTE_NAME . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function listPage(?string $orderBy = null, ?string $direction = null): Response {
        return $this->list($orderBy, $direction);
    }

    #[Route(self::BASE_ROUTE_NAME . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function addPage(Request $Request, ?int $id = null): Response {
        return $this->add($Request, $id);
    }

    #[Route(self::BASE_ROUTE_NAME . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function editPage(Request $Request, ?int $id = null): Response {
        return $this->edit($Request, $id);
    }

    #[Route(self::BASE_ROUTE_NAME . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function removePage(?int $id = null): Response {
        return $this->remove($id);
    }
}