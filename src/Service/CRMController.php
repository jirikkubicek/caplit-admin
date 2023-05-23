<?php

namespace App\Service;

use App\Entity\CRMEntityInterface;
use App\Service\CRMServiceInterface;
use App\Service\MessagesInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CRMController extends AbstractController
{
    private const TEMPLATES_ADD_KEY_NAME = "add";
    private const TEMPLATES_EDIT_KEY_NAME = "edit";
    private const TEMPLATES_REMOVE_KEY_NAME = "remove";
    private const TEMPLATES_LIST_KEY_NAME = "list";

    /**
     * @var class-string<object>
     */
    private string $entityName;
    /**
     * @var class-string<object>
     */
    private string $formTypeName;
    /**
     * @var array<string,mixed>
     */
    private array $formOptions = [];
    /**
     * @var array<string,array<string,string>>
     */
    private array $templates = [];
    /**
     * @var CRMServiceInterface
     */
    private CRMServiceInterface $service;

    /**
     * @param ValidatorInterface $validator
     * @param MessagesInterface $messages
     */
    public function __construct(
        private ValidatorInterface $validator,
        private MessagesInterface $messages
    ) {
        $this->setDefaultMessages();
    }

    /**
     * @param CRMServiceInterface $service
     * @return self
     */
    public function setService(CRMServiceInterface $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return CRMServiceInterface
     */
    private function getService(): CRMServiceInterface
    {
        return $this->service;
    }

    /**
     * @param class-string<object> $formTypeName
     * @param array<string,mixed> $formOptions
     * @return self
     */
    public function setFormTypeName(string $formTypeName, array $formOptions = []): self
    {
        $this->formTypeName = $formTypeName;
        $this->setFormOptions($formOptions);

        return $this;
    }

    /**
     * @return class-string<object>
     */
    private function getFormTypeName(): string
    {
        return $this->formTypeName;
    }

    /**
     * @param array<string,mixed> $formOptions
     * @return self
     */
    public function setFormOptions(array $formOptions = []): self
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addFormOption(string $key, mixed $value): self
    {
        $this->formOptions[$key] = $value;

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    private function getFormOptions(): array
    {
        return $this->formOptions;
    }

    /**
     * @param class-string<object> $entityName
     * @return self
     */
    public function setEntityClassName(string $entityName): self
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return class-string<object>
     */
    private function getEntityClassName(): string
    {
        return $this->entityName;
    }

    /**
     * @param string $templateKeyName
     * @param string $path
     * @param string $routeName
     * @return self
     */
    public function addTemplate(string $templateKeyName, string $path, string $routeName): self
    {
        $this->templates[$templateKeyName] = ["path" => $path, "route" => $routeName];

        return $this;
    }

    /**
     * @return array<string,array<string,string>>
     */
    private function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param string $templateName
     * @return string
     */
    private function getTemplatePath(string $templateName): string
    {
        $templates = $this->getTemplates();

        if (isset($templates[$templateName]["path"])) {
            return $templates[$templateName]["path"];
        }

        return "";
    }

    /**
     * @param string $templateName
     * @return string
     */
    private function getTemplateRoute(string $templateName): string
    {
        $templates = $this->getTemplates();

        if (isset($templates[$templateName]["route"])) {
            return $templates[$templateName]["route"];
        }

        return "";
    }

    /**
     * @param string $routeName
     * @param string $templatePath
     * @return self
     */
    public function setListAction(string $routeName, string $templatePath): self
    {
        $this->addTemplate(self::TEMPLATES_LIST_KEY_NAME, $templatePath, $routeName);

        return $this;
    }

    /**
     * @param string $routeName
     * @param string $templatePath
     * @return self
     */
    public function setAddAction(string $routeName, string $templatePath = "crm/base_add_edit.html.twig"): self
    {
        $this->addTemplate(self::TEMPLATES_ADD_KEY_NAME, $templatePath, $routeName);

        return $this;
    }

    /**
     * @param string $routeName
     * @param string $templatePath
     * @return self
     */
    public function setEditAction(string $routeName, string $templatePath = "crm/base_add_edit.html.twig"): self
    {
        $this->addTemplate(self::TEMPLATES_EDIT_KEY_NAME, $templatePath, $routeName);

        return $this;
    }

    /**
     * @param string $routeName
     * @return self
     */
    public function setRemoveAction(string $routeName): self
    {
        $this->addTemplate(self::TEMPLATES_REMOVE_KEY_NAME, "", $routeName);

        return $this;
    }

    /**
     * @param string $header
     * @param FormInterface|null $form
     * @param array<int,object> $entities
     * @return array<string,string|object|array<int,object>>
     */
    private function getTemplateVariables(string $header, ?FormInterface $form = null, array $entities = []): array
    {
        $templateVariables = [
            "add_route_name" => $this->getTemplateRoute(self::TEMPLATES_ADD_KEY_NAME),
            "edit_route_name" => $this->getTemplateRoute(self::TEMPLATES_EDIT_KEY_NAME),
            "remove_route_name" => $this->getTemplateRoute(self::TEMPLATES_REMOVE_KEY_NAME),
            "list_route_name" => $this->getTemplateRoute(self::TEMPLATES_LIST_KEY_NAME),
            "header" => $header
        ];

        if ($form !== null) {
            $templateVariables["form"] = $form;
        }

        if (!empty($entities)) {
            $templateVariables["entities"] = $entities;
        }

        return $templateVariables;
    }

    /**
     * @return self
     */
    public function setDefaultMessages(): self
    {
        $this->messages->addMessages([
            "add.success" => "Položka byla úspěšně přidána",
            "edit.success" => "Položka byla úspěšně upravena",
            "remove.success" => "Položka byla úspěšně smazána",
            "add.error" => "Při přidávání se vyskytla chyba",
            "edit.error" => "Při úpravě se vyskytla chyba",
            "remove.error" => "Při mazání se vyskytla chyba",
            "list.sortingError" => "Řazení dle zadaných parametrů se nazdařilo. Výsledky jsou zobrazeny neseřazené.",
            "add.copyError" => "Při kopírování nastala chyba. Přidejte novou položku.",
            "itemNotFound" => "Požadovaná položka nebyla nalezena",
            "emptyId" => "Nebylo vyplněno ID položky",
            "list.header" => "Přehled",
            "add.header" => "Přidání položky",
            "edit.header" => "Úprava položky",
            "add.formButton" => "Přidat",
            "edit.formButton" => "Upravit",
        ]);

        return $this;
    }

    /**
     * @param array<string,string|array<string,string>> $messages
     * @return self
     */
    public function addMessages(array $messages): self
    {
        $this->messages->addMessages($messages);

        return $this;
    }

    /**
     * @param string|null $orderBy
     * @param string|null $direction
     * @return Response
     */
    public function list(?string $orderBy = null, ?string $direction = null): Response
    {
        if ($orderBy !== null) {
            $direction = (string)$direction;
            if (
                $this->getService()->entityHasProperty($orderBy) &&
                (strtolower($direction) === "asc" || strtolower($direction) === "desc")
            ) {
                $entities = $this->getService()->findAllOrdered($orderBy, $direction);
            } else {
                $this->addFlash("error", $this->messages->get("list.sortingError"));
            }
        }

        if (!isset($entities)) {
            $entities = $this->getService()->findAll();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_LIST_KEY_NAME),
            $this->getTemplateVariables($this->messages->get("list.header"), entities: $entities)
        );
    }

    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function add(Request $request, ?int $id = null): Response
    {
        if ($id !== null) {
            $originalEntity = $this->loadEntity($id);
            if ($originalEntity === null || !$originalEntity instanceof CRMEntityInterface) {
                $this->addFlash("error", $this->messages->get("add.copyError"));
                return $this->redirectToList();
            }

            $originalEntity->resetId();
            $entity = clone $originalEntity;
        } else {
            $entityClassName = $this->getEntityClassName();
            $entity = new $entityClassName();   // @todo check if is instance of CRMEntityInterface
        }

        $form = $this->processAddingAndEditing($entity, $request);
        if ($form === null) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_ADD_KEY_NAME),
            $this->getTemplateVariables($this->messages->get("add.header"), form: $form)
        );
    }

    /**
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $request, ?int $id): Response
    {
        $entity = $this->loadEntity($id);
        if ($entity === null) {
            return $this->redirectToList();
        }

        $form = $this->processAddingAndEditing($entity, $request);
        if ($form === null) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_EDIT_KEY_NAME),
            $this->getTemplateVariables($this->messages->get("edit.header"), form: $form)
        );
    }

    /**
     * @param integer|null $id
     * @return Response
     */
    public function remove(?int $id): Response
    {
        $entity = $this->loadEntity($id);
        if ($entity === null) {
            return $this->redirectToList();
        }

        if ($this->getService()->remove($entity) === true) {
            $this->addFlash("success", $this->messages->get("remove.success"));
        } else {
            $this->addFlash("error", $this->messages->get("remove.error"));
        }

        return $this->redirectToList();
    }

    /**
     * @param integer|null $id
     * @return object|null
     */
    private function loadEntity(?int $id): ?object
    {
        if ($this->checkIfIDWasFilled($id)) {
            $entity = $this->getService()->findOneBy(["id" => $id]);

            if ($entity) {
                return $entity;
            } else {
                $this->addFlash("error", $this->messages->get("itemNotFound"));
            }
        }

        return null;
    }

    /**
     * @param integer|null $id
     * @return boolean
     */
    private function checkIfIDWasFilled(?int $id): bool
    {
        if ($id !== null) {
            return true;
        }

        $this->addFlash("error", $this->messages->get("emptyId"));
        return false;
    }

    /**
     * @return Response
     */
    private function redirectToList(): Response
    {
        return $this->redirectToRoute($this->getTemplateRoute(self::TEMPLATES_LIST_KEY_NAME));
    }

    /**
     * @param object $entity
     * @param Request $request
     * @return FormInterface|null
     */
    private function processAddingAndEditing(object $entity, Request $request): ?FormInterface
    {
        $isEntityEmpty = $this->isEntityEmpty($entity);

        $this->addFormOption(
            "submitLabel",
            ($isEntityEmpty === true) ?
            $this->messages->get("add.formButton") :
            $this->messages->get("edit.formButton")
        );
        $formOptions = $this->getFormOptions();

        $form = $this->createForm($this->getFormTypeName(), $entity, $formOptions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getService()->setForm($form);
            $queryResult = $this->getService()->addOrEdit($entity);

            if ($queryResult === true) {
                $this->addFlash(
                    "success",
                    ($isEntityEmpty === true) ?
                        $this->messages->get("add.success") :
                        $this->messages->get("edit.success")
                );
                return null;
            } else {
                $this->addFlash(
                    "error",
                    ($isEntityEmpty === true) ?
                    $this->messages->get("add.error") :
                    $this->messages->get("edit.error")
                );
            }
        } elseif ($form->isSubmitted()) {
            $this->validateEntity($entity);
        }

        return $form;
    }

    /**
     * @param object $entity
     * @return void
     */
    private function validateEntity(object $entity): void
    {
        $isEntityEmpty = $this->isEntityEmpty($entity);

        $errors = $this->validator->validate($entity);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $this->addFlash("error", ($isEntityEmpty === true)
                    ? $this->messages->get("add.error") . " - " . $error->getMessage()
                    : $this->messages->get("edit.error") . " - " . $error->getMessage());
            }
        }
    }

    /**
     * @param object $entity
     * @return boolean
     */
    private function isEntityEmpty(object $entity): bool
    {
        if ($entity instanceof CRMEntityInterface) {
            return $entity->getId() === null;
        } else {
            throw new Exception("Entity has to have method getId()");
        }
    }
}
