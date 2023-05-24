<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\Type\SectionType;
use App\Service\MessagesInterface;
use App\Service\Section as SectionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class SectionController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/sections";
    private const LIST_ROUTE_NAME = "sections_show";
    private const ADD_ROUTE_NAME = "sections_add";
    private const EDIT_ROUTE_NAME = "sections_edit";
    private const REMOVE_ROUTE_NAME = "sections_remove";

    /**
     * @param SectionService $service
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(
        SectionService $service,
        ValidatorInterface $validator,
        MessagesInterface $messages,
        Security $security
    ) {
        parent::__construct($validator, $messages);

        $service->setEntityClassName(Section::class);

        $this
            ->setEntityClassName(Section::class)
            ->setFormTypeName(SectionType::class)
            ->setFormOptions(["isAdmin" => $security->isGranted("ROLE_ADMIN")])
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "sections.html.twig")
            ->setAddAction(self::ADD_ROUTE_NAME)
            ->setEditAction(self::EDIT_ROUTE_NAME)
            ->setRemoveAction(self::REMOVE_ROUTE_NAME)
            ->addMessages([
                "list.header" => "Přehled sekcí",
                "add.header" => "Přidání sekce",
                "edit.header" => "Úprava sekce",
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
