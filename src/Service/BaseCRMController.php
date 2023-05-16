<?php

namespace App\Service;

use App\Service\CRMServiceInterface;
use App\Entity\CloneableEntityInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseCRMController extends AbstractController implements BaseCRMControllerInterface
{
 /** Z důvodu builderu není abstract ; ponecháno dědění z AbstractController - nelze injectnout abstraktní třídu */
    public const TEMPLATES_ADD_KEY_NAME = "add";
    public const TEMPLATES_EDIT_KEY_NAME = "edit";
    public const TEMPLATES_REMOVE_KEY_NAME = "remove";
    public const TEMPLATES_LIST_KEY_NAME = "list";

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $ValidatorInterface;
    /**
     * @var CRMServiceInterface
     */
    private CRMServiceInterface $Service;
    /**
     * @var string
     */
    private string $formTypeName;
    /**
     * @var array
     */
    private array $formOptions = [];
    /**
     * @var string
     */
    private string $entityName;
    /**
     * @var array
     */
    private array $templates = [];
    /**
     * @var MessagesInterface
     */
    private MessagesInterface $Messages;

    /**
     * @param ValidatorInterface $ValidatorInterface
     * @param CRMServiceInterface $Service
     * @param MessagesInterface $Messages
     * @param string $entityName
     * @param string $formTypeName
     * @param array $formOptions
     * @param array $templates
     * @return void
     */
    public function set(
        ValidatorInterface $ValidatorInterface,
        CRMServiceInterface $Service,
        MessagesInterface $Messages,
        string $entityName,
        string $formTypeName,
        array $formOptions = [],
        array $templates = []
    ) {
        $this->ValidatorInterface = $ValidatorInterface;
        $this->Service = $Service;
        $this->formTypeName = $formTypeName;
        $this->formOptions = $formOptions;
        $this->entityName = $entityName;
        $this->templates = $templates;
        $this->Messages = $Messages;
    }

    /**
     * @return CRMServiceInterface
     */
    public function getService(): CRMServiceInterface
    {
        return $this->Service;
    }

    /**
     * @return string
     */
    public function getFormTypeName(): string
    {
        return $this->formTypeName;
    }

    /**
     * @return array
     */
    public function getFormOptions(): array
    {
        return $this->formOptions;
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
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entityName;
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param string $templateName
     * @return string|null
     */
    public function getTemplatePath(string $templateName): ?string
    {
        $templates = $this->getTemplates();

        if (isset($templates[$templateName]) && isset($templates[$templateName]["path"])) {
            return $templates[$templateName]["path"];
        } else {
            return null;
        }
    }

    /**
     * @param string $templateName
     * @return string|null
     */
    public function getTemplateRoute(string $templateName): ?string
    {
        $templates = $this->getTemplates();

        if (isset($templates[$templateName]) && isset($templates[$templateName]["route"])) {
            return $templates[$templateName]["route"];
        } else {
            return null;
        }
    }

    /**
     * @param string|null $orderBy
     * @param string|null $direction
     * @return Response
     */
    public function list(?string $orderBy = null, ?string $direction = null): Response
    {
        if ($orderBy !== null) {
            if (
                $this->getService()->checkEntityHasProperty($orderBy) &&
                (strtolower($direction) === "asc" || strtolower($direction) === "desc")
            ) {
                $Entities = $this->getService()->findAllOrdered($orderBy, $direction);
            } else {
                $this->addFlash("error", $this->Messages->get(action: "list", name: "sorting-error"));
                $Entities = $this->getService()->findAll();
            }
        } else {
            $Entities = $this->getService()->findAll();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_LIST_KEY_NAME),
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "list"), Entities: $Entities)
        );
    }

    /**
     * @param Request $Request
     * @param integer|null $id
     * @return Response
     */
    public function add(Request $Request, ?int $id = null): Response
    {
        $Entity = new $this->entityName();

        if ($id !== null) {
            $originalEntity = $this->loadEntity($id);
            if ($originalEntity === null || !$originalEntity instanceof CloneableEntityInterface) {
                $this->addFlash("error", $this->Messages->get(action: "add", name: "copy-error"));
                return $this->redirectToList();
            }

            $originalEntity->resetId();
            $Entity = clone $originalEntity;
        }

        $Form = $this->processAddingAndEditing($Entity, $Request);
        if ($Form === true) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_ADD_KEY_NAME),
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "add"), Form: $Form)
        );
    }

    /**
     * @param Request $Request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $Request, ?int $id): Response
    {
        $Entity = $this->loadEntity($id);
        if ($Entity === null) {
            return $this->redirectToList();
        }

        $Form = $this->processAddingAndEditing($Entity, $Request);
        if ($Form === true) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_EDIT_KEY_NAME),
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "edit"), Form: $Form)
        );
    }

    /**
     * @param integer|null $id
     * @return Response
     */
    public function remove(?int $id): Response
    {
        $Entity = $this->loadEntity($id);
        if ($Entity === null) {
            return $this->redirectToList();
        }

        $queryResult = $this->getService()->remove($Entity);

        if ($queryResult === true) {
            $this->addFlash("success", $this->Messages->get(action: "remove", name: "success"));
        } else {
            $this->addFlash("error", $this->Messages->get(action: "remove", name: "error"));
        }

        return $this->redirectToList();
    }

    /**
     * Redirect to list of entities
     *
     * @return Response
     */
    public function redirectToList(): Response
    {
        return $this->redirectToRoute($this->getTemplateRoute(self::TEMPLATES_LIST_KEY_NAME));
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

        $this->addFlash("error", $this->Messages->get(name: "empty_id"));
        return false;
    }

    /**
     * @param integer|null $id
     * @return object|null
     */
    public function loadEntity(?int $id): ?object
    {
        if ($this->checkIfIDWasFilled($id)) {
            $Entity = $this->getService()->findById($id);

            if ($Entity) {
                return $Entity;
            } else {
                $this->addFlash("error", $this->Messages->get(name: "not_found"));
            }
        }

        return null;
    }

    /**
     * @param object $Entity
     * @param Request $Request
     * @return boolean|FormInterface
     */
    public function processAddingAndEditing(object $Entity, Request $Request): bool|FormInterface
    {
        $isEntityEmpty = $this->isEntityEmpty($Entity);

        $this->addFormOption(
            "submitLabel",
            ($isEntityEmpty) ?
            $this->Messages->get(name: "form_button", action: "add") :
                $this->Messages->get(name: "form_button", action: "edit")
        );
        $formOptions = $this->getFormOptions();
        $Form = $this->createForm($this->getFormTypeName(), $Entity, $formOptions);
        $Form->handleRequest($Request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            $this->getService()->setForm($Form);
            $queryResult = $this->getService()->addOrEdit($Entity);

            if ($queryResult === true) {
                $this->addFlash(
                    "success",
                    ($isEntityEmpty) ?
                        $this->Messages->get(action: "add", name: "success") :
                        $this->Messages->get(action: "edit", name: "success")
                );
                return true;
            } else {
                $this->addFlash(
                    "error",
                    ($isEntityEmpty) ?
                    $this->Messages->get(action: "add", name: "error") :
                    $this->Messages->get(action: "edit", name: "error")
                );
            }
        } elseif ($Form->isSubmitted()) {
            $this->validateEntity($Entity);
        }

        return $Form;
    }

    /**
     * @param object $Entity
     * @return void
     */
    private function validateEntity(object $Entity): void
    {
        $isEntityEmpty = $this->isEntityEmpty($Entity);

        $errors = $this->ValidatorInterface->validate($Entity);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $this->addFlash("error", ($isEntityEmpty)
                    ? $this->Messages->get(action: "add", name: "error") . " - " . $error->getMessage()
                    : $this->Messages->get(action: "edit", name: "error") . " - " . $error->getMessage());
            }
        }
    }

    /**
     * @param object $Entity
     * @return boolean
     */
    private function isEntityEmpty(object $Entity): bool
    {
        return $Entity->getId() === null;
    }

    /**
     * @param string $header
     * @param FormInterface|null $Form
     * @param array $Entities
     * @return array
     */
    protected function getTemplateVariables(string $header, ?FormInterface $Form = null, array $Entities = []): array
    {
        $templateVariables = [
            "add_route_name" => $this->getTemplateRoute(self::TEMPLATES_ADD_KEY_NAME),
            "edit_route_name" => $this->getTemplateRoute(self::TEMPLATES_EDIT_KEY_NAME),
            "remove_route_name" => $this->getTemplateRoute(self::TEMPLATES_REMOVE_KEY_NAME),
            "list_route_name" => $this->getTemplateRoute(self::TEMPLATES_LIST_KEY_NAME),
            "header" => $header
        ];

        if ($Form !== null) {
            $templateVariables["Form"] = $Form;
        }

        if (!empty($Entities)) {
            $templateVariables["Entities"] = $Entities;
        }

        return $templateVariables;
    }
}
