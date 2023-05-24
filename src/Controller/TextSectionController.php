<?php

namespace App\Controller;

use App\Entity\TextSection;
use App\Form\Type\TextSectionType;
use App\Service\MessagesInterface;
use App\Service\TextSection as TextSectionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted("IS_AUTHENTICATED")]
final class TextSectionController extends CRMController implements CRMControllerInterface
{
    private const BASE_ROUTE = "/text_section";
    private const LIST_ROUTE_NAME = "text_section_list";
    private const ADD_ROUTE_NAME = "text_section_add";
    private const EDIT_ROUTE_NAME = "text_section_edit";
    private const REMOVE_ROUTE_NAME = "text_section_remove";

    /**
     * @param TextSectionService $service
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     * @param Security $security
     */
    public function __construct(
        TextSectionService $service,
        ValidatorInterface $validator,
        MessagesInterface $messages,
        private Security $security
    ) {
        parent::__construct($validator, $messages);

        $this
            ->setEntityClassName(TextSection::class)
            ->setFormTypeName(TextSectionType::class)
            ->setFormOptions(["isAdmin" => $security->isGranted("ROLE_ADMIN")])
            ->setService($service)
            ->setListAction(self::LIST_ROUTE_NAME, "text_sections.html.twig")
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
     * @throws \Exception
     */
    public function remove(?int $id = null): Response
    {
        $entity = $this->loadEntity($id);

        if ($entity === null) {
            return $this->redirectToList();
        }

        if ($entity instanceof TextSection) {
            if ($entity->isDefault() === true && !$this->security->isGranted("ROLE_ADMIN")) {
                throw new \Exception("Default section may remove only administrator.");
            }
        }

        return parent::remove($id);
    }
}
