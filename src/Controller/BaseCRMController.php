<?php

namespace App\Controller;

use App\Service\CRMServiceInterface;
use App\Service\Messages;
use App\Entity\CloneableEntityInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseCRMController extends AbstractController {
    const TEMPLATES_ADD_KEY_NAME = "add";
    const TEMPLATES_EDIT_KEY_NAME = "edit";
    const TEMPLATES_REMOVE_KEY_NAME = "remove";
    const TEMPLATES_LIST_KEY_NAME = "list";

    private CRMServiceInterface $Service;
    private string $formTypeName;
    private array $formOptions = [];
    private object $Entity;
    private array $templates = [];
    protected Messages $Messages;

    public function __construct(private ValidatorInterface $ValidatorInterface) {
        $this->setDefaultMessages();
    }

    protected function setService(CRMServiceInterface $Service): self {
        $this->Service = $Service;

        return $this;
    }

    protected function getService(): CRMServiceInterface {
        return $this->Service;
    }

    protected function setFormTypeName(string $formTypeName, array $formOptions = []): self {
        $this->formTypeName = $formTypeName;

        return $this;
    }

    protected function getFormTypeName(): string {
        return $this->formTypeName;
    }

    protected function setFormOptions(array $formOptions = []): self {
        $this->formOptions = $formOptions;

        return $this;
    }

    protected function getFormOptions(): array {
        return $this->formOptions;
    }

    protected function addFormOption(string $key, mixed $value): self {
        $this->formOptions[$key] = $value;

        return $this;
    }

    protected function setEntity(object $Entity): self {
        $this->Entity = $Entity;

        return $this;
    }

    protected function getEntity(): object {
        return $this->Entity;
    }

    protected function addTemplate(string $templateKeyName, string $path, string $routeName): self {
        $this->templates[$templateKeyName] = ["path" => $path, "route" => $routeName];

        return $this;
    }

    protected function getTemplates(): array {
        return $this->templates;
    }

    protected function getTemplatePath(string $templateName): ?string {
        $templates = $this->getTemplates();

        if(isset($templates[$templateName]) && isset($templates[$templateName]["path"])) {
            return $templates[$templateName]["path"];
        } else {
            return null;
        }
    }

    protected function getTemplateRoute(string $templateName): ?string {
        $templates = $this->getTemplates();

        if(isset($templates[$templateName]) && isset($templates[$templateName]["route"])) {
            return $templates[$templateName]["route"];
        } else {
            return null;
        }
    }

    private function setDefaultMessages(): self {
        $this->Messages = new Messages;
        $this->Messages->addMessages([
            [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "success", Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně přidána"],
            [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "success", Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně upravena"],
            [Messages::ACTION_KEY_NAME => "remove", Messages::NAME_KEY_NAME => "success", Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně smazána"],
            [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "error", Messages::MESSAGE_KEY_NAME => "Při přidávání se vyskytla chyba"],
            [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "error", Messages::MESSAGE_KEY_NAME => "Při úpravě se vyskytla chyba"],
            [Messages::ACTION_KEY_NAME => "remove", Messages::NAME_KEY_NAME => "error", Messages::MESSAGE_KEY_NAME => "Při mazání se vyskytla chyba"],
            [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "sorting-error", Messages::MESSAGE_KEY_NAME => "Řazení dle zadaných parametrů se nazdařilo. Výsledky jsou zobrazeny neseřazené."],
            [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "copy-error", Messages::MESSAGE_KEY_NAME => "Při kopírování nastala chyba. Přidejte novou položku."],
            [Messages::NAME_KEY_NAME => "not_found", Messages::MESSAGE_KEY_NAME => "Požadovaná položka nebyla nalezena"],
            [Messages::NAME_KEY_NAME => "empty_id", Messages::MESSAGE_KEY_NAME => "Nebylo vyplněno ID položky"],
            [Messages::ACTION_KEY_NAME => "list", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přehled"],
            [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Přidání položky"],
            [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "header", Messages::MESSAGE_KEY_NAME => "Úprava položky"],
            [Messages::ACTION_KEY_NAME => "add", Messages::NAME_KEY_NAME => "form_button", Messages::MESSAGE_KEY_NAME => "Přidat"],
            [Messages::ACTION_KEY_NAME => "edit", Messages::NAME_KEY_NAME => "form_button", Messages::MESSAGE_KEY_NAME => "Upravit"],
        ]);

        return $this;
    }

    protected function list(?string $orderBy = null, ?string $direction = null): Response {
        if($orderBy !== null) {
            if($this->getService()->checkEntityHasProperty($orderBy) && (strtolower($direction) === "asc" || strtolower($direction) === "desc")) {
                $Entities = $this->getService()->findAllOrdered($orderBy, $direction);
            } else {
                $this->addFlash("error", $this->Messages->get(action: "list", name: "sorting-error"));
                $Entities = $this->getService()->findAll();
            }
        } else {
            $Entities = $this->getService()->findAll();
        }

        return $this->render(
            $this->getTemplatePath(SELF::TEMPLATES_LIST_KEY_NAME),
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "list"), Entities: $Entities)
        );
    }

    protected function add(Request $Request, ?int $id = null): Response {
        $Entity = $this->getEntity();

        if($id !== null) {
            $originalEntity = $this->loadEntity($id);
            if($originalEntity === null || !$originalEntity instanceof CloneableEntityInterface) {
                $this->addFlash("error", $this->Messages->get(action: "add", name: "copy-error"));
                return $this->redirectToList();
            }

            $originalEntity->resetId();
            $Entity = clone $originalEntity;
        }
        
        $Form = $this->processAddingAndEditing($Entity, $Request);
        if($Form === true) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(SELF::TEMPLATES_ADD_KEY_NAME),
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "add"), Form: $Form)
        );
    }

    protected function edit(Request $Request, ?int $id): Response {
        $Entity = $this->loadEntity($id);
        if($Entity === null) {
            return $this->redirectToList();
        }

        $Form = $this->processAddingAndEditing($Entity, $Request);
        if($Form === true) {
            return $this->redirectToList();
        }

        return $this->render(
            $this->getTemplatePath(self::TEMPLATES_EDIT_KEY_NAME), 
            $this->getTemplateVariables($this->Messages->get(name: "header", action: "edit"), Form: $Form)
        );
    }

    protected function remove(?int $id): Response {
        $Entity = $this->loadEntity($id);
        if($Entity === null) {
            return $this->redirectToList();
        }

        $queryResult = $this->getService()->remove($Entity);

        if($queryResult === true) { 
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
    protected function redirectToList(): Response {
        return $this->redirectToRoute($this->getTemplateRoute(SELF::TEMPLATES_LIST_KEY_NAME));
    }

    private function checkIfIDWasFilled(?int $id): bool {
        if($id !== null) {
            return true;
        }

        $this->addFlash("error", $this->Messages->get(name: "empty_id"));
        return false;
    }

    protected function loadEntity(?int $id): ?object {
        if($this->checkIfIDWasFilled($id)) {
            $Entity = $this->getService()->findById($id);

            if($Entity) {
                return $Entity;
            } else {
                $this->addFlash("error", $this->Messages->get(name: "not_found"));
            }
        }

        return null;
    }

    protected function processAddingAndEditing(object $Entity, Request $Request): bool|FormInterface {
        $isEntityEmpty = $this->isEntityEmpty($Entity);

        $this->addFormOption("submitLabel", ($isEntityEmpty) ? $this->Messages->get(name: "form_button", action: "add") : $this->Messages->get(name: "form_button", action: "edit"));
        $formOptions = $this->getFormOptions();
        $Form = $this->createForm($this->getFormTypeName(), $Entity, $formOptions);
        $Form->handleRequest($Request);
       
        if($Form->isSubmitted() && $Form->isValid()) {
            $this->getService()->setForm($Form);
            $queryResult = $this->getService()->addOrEdit($Entity);

            if($queryResult === true) { 
                $this->addFlash("success", ($isEntityEmpty) ? $this->Messages->get(action: "add", name: "success") : $this->Messages->get(action: "edit", name: "success"));
                return true;
            } else {
                $this->addFlash("error", ($isEntityEmpty) ? $this->Messages->get(action: "add", name: "error") : $this->Messages->get(action: "edit", name: "error"));
            }
        } elseif($Form->isSubmitted()) {
            $this->validateEntity($Entity);
        }

        return $Form;
    }

    private function validateEntity(object $Entity): void {
        $isEntityEmpty = $this->isEntityEmpty($Entity);

        $errors = $this->ValidatorInterface->validate($Entity);
        if($errors->count() > 0) {
            foreach($errors as $error) {
                $this->addFlash("error", ($isEntityEmpty) 
                    ? $this->Messages->get(action: "add", name: "error") . " - " . $error->getMessage() 
                    : $this->Messages->get(action: "edit", name: "error") . " - " . $error->getMessage());
            }
        }
    }

    private function isEntityEmpty(object $Entity): bool {
        return $Entity->getId() === null;
    }

    protected function getTemplateVariables(string $header, ?FormInterface $Form = null, array $Entities = []): array {
        $templateVariables = [
            "add_route_name" => $this->getTemplateRoute(self::TEMPLATES_ADD_KEY_NAME),
            "edit_route_name" => $this->getTemplateRoute(self::TEMPLATES_EDIT_KEY_NAME),
            "remove_route_name" => $this->getTemplateRoute(self::TEMPLATES_REMOVE_KEY_NAME),
            "list_route_name" => $this->getTemplateRoute(self::TEMPLATES_LIST_KEY_NAME),
            "header" => $header
        ];

        if($Form !== null) {
            $templateVariables["Form"] = $Form;
        }

        if(!empty($Entities)) {
            $templateVariables["Entities"] = $Entities;
        }

        return $templateVariables;
    }
}