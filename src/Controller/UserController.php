<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\Type\UserType;
use App\Service\MessagesInterface;
use App\Service\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class UserController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/users";
    private const LIST_ROUTE_NAME = "users_list";
    private const ADD_ROUTE_NAME = "users_add";
    private const EDIT_ROUTE_NAME = "users_edit";
    private const REMOVE_ROUTE_NAME = "users_remove";

    /**
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @param User $service
     */
    public function __construct(
        ValidatorInterface $validator,
        MessagesInterface $messages,
        User $service
    ) {
        parent::__construct($validator, $messages);

        $this
            ->setEntityClassName(Users::class)
            ->setService($service)
            ->setFormTypeName(UserType::class)
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->setListAction(self::LIST_ROUTE_NAME, "users.html.twig")
            ->addMessages([
                "list.header" => "Přehled uživatelů",
                "add.header" => "Přidání uživatele",
                "edit.header" => "Úprava uživatele",
            ]);
    }

    /**
     * @param string|null $orderBy
     * @param string|null $direction
     * @return Response
     */
    #[Route(self::BASE_ROUTE . "/list/{orderBy}/{direction}", self::LIST_ROUTE_NAME)]
    public function list(?string $orderBy = null, ?string $direction = null): Response
    {
        return parent::list($orderBy, $direction);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    #[Route(self::BASE_ROUTE . "/add/{id}", self::ADD_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function add(Request $request, ?int $id = null): Response
    {
        $this->setFormOptions(["mode" => "add"]);

        return parent::add($request, $id);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    #[Route(self::BASE_ROUTE . "/edit/{id}", self::EDIT_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function edit(Request $request, ?int $id): Response
    {
        $this->setFormOptions(["mode" => "edit"]);

        $service = $this->getService();
        if ($service instanceof User) {
            $originPassword = (string) $this->loadEntity($id)?->getPassword();
            $service->setOriginPassword($originPassword);
        } else {
            throw new \Exception(sprintf("%s needs to have service of type %s", self::class, User::class));
        }

        return parent::edit($request, $id);
    }

    /**
     * @param int|null $id
     * @return Response
     */
    #[Route(self::BASE_ROUTE . "/remove/{id}", self::REMOVE_ROUTE_NAME, requirements: ["id" => "\d+"])]
    public function remove(?int $id): Response
    {
        return parent::remove($id);
    }
}
