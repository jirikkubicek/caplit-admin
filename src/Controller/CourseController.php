<?php

namespace App\Controller;

use App\Entity\Course as CourseEntity;
use App\Form\Type\CourseType;
use App\Service\Course;
use App\Service\MessagesInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class CourseController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/course";
    private const LIST_ROUTE_NAME = "course_list";
    private const ADD_ROUTE_NAME = "course_add";
    private const EDIT_ROUTE_NAME = "course_edit";
    private const REMOVE_ROUTE_NAME = "course_remove";

    /**
     * @param Course $service
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @param Security $security
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(
        Course $service,
        ValidatorInterface $validator,
        MessagesInterface $messages,
        Security $security
    ) {
        parent::__construct($validator, $messages);

        $service->setEntityClassName(CourseEntity::class);

        $this
            ->setEntityClassName(CourseEntity::class)
            ->setFormTypeName(CourseType::class)
            ->setFormOptions(["isAdmin" => $security->isGranted("ROLE_ADMIN")])
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "courses.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled chodů",
                "add.header" => "Přidání chodu",
                "edit.header" => "Úprava chodu",
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
