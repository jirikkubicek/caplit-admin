<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Type\MealType;
use App\Service\Course;
use App\Service\Meal as ServiceMeal;
use App\Service\MessagesInterface;
use App\Service\Section;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class MealController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/meal";
    private const LIST_ROUTE_NAME = "meal_list";
    private const ADD_ROUTE_NAME = "meal_add";
    private const EDIT_ROUTE_NAME = "meal_edit";
    private const REMOVE_ROUTE_NAME = "meal_remove";

    /**
     * @param ServiceMeal $service
     * @param Section $sectionService
     * @param Course $courseService
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     */
    public function __construct(
        ServiceMeal $service,
        Section $sectionService,
        Course $courseService,
        ValidatorInterface $validator,
        MessagesInterface $messages
    ) {
        parent::__construct($validator, $messages);

        $this
            ->setEntityClassName(Meal::class)
            ->setFormTypeName(MealType::class)
            ->setFormOptions([
                "sections" => $sectionService->findAll(),
                "courses" => $courseService->findAll()
            ])
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "meals.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled jídel",
                "add.header" => "Přidání jídla",
                "edit.header" => "Úprava jídla",
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
