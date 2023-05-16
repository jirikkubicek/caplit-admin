<?php

namespace App\Service;

use App\Service\CRMServiceInterface;
use App\Service\Messages;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseCRMControllerBuilder
{
    /**
     * @var CRMServiceInterface
     */
    private CRMServiceInterface $Service;
    /**
     * @var string
     */
    private string $formTypeName = "";
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
     * @param ValidatorInterface $Validator
     * @param MessagesInterface $Messages
     * @param BaseCRMController $BaseCRM
     */
    public function __construct(
        private ValidatorInterface $Validator,
        private MessagesInterface $Messages,
        private BaseCRMController $BaseCRM
    ) {
 /** Pokud byla vytvořena instance BaseCRMController jinak než skrze DI, nefunguje */
        $this->setDefaultMessages();
    }

    /**
     * @param CRMServiceInterface $Service
     * @return self
     */
    public function setService(CRMServiceInterface $Service): self
    {
        $this->Service = $Service;

        return $this;
    }

    /**
     * @param string $formTypeName
     * @param array $formOptions
     * @return self
     */
    public function setFormTypeName(string $formTypeName, array $formOptions = []): self
    {
        $this->formTypeName = $formTypeName;
        $this->setFormOptions($formOptions);

        return $this;
    }

    /**
     * @param array $formOptions
     * @return self
     */
    public function setFormOptions(array $formOptions = []): self
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    /**
     * @param string $entityName
     * @return self
     */
    public function setEntity(string $entityName): self
    {
        $this->entityName = $entityName;

        return $this;
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
     * @return self
     */
    public function setDefaultMessages(): self
    {
        $this->Messages->addMessages([
            [
                Messages::ACTION_KEY_NAME => "add",
                Messages::NAME_KEY_NAME => "success",
                Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně přidána"
            ],
            [
                Messages::ACTION_KEY_NAME => "edit",
                Messages::NAME_KEY_NAME => "success",
                Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně upravena"
            ],
            [
                Messages::ACTION_KEY_NAME => "remove",
                Messages::NAME_KEY_NAME => "success",
                Messages::MESSAGE_KEY_NAME => "Položka byla úspěšně smazána"
            ],
            [
                Messages::ACTION_KEY_NAME => "add",
                Messages::NAME_KEY_NAME => "error",
                Messages::MESSAGE_KEY_NAME => "Při přidávání se vyskytla chyba"
            ],
            [
                Messages::ACTION_KEY_NAME => "edit",
                Messages::NAME_KEY_NAME => "error",
                Messages::MESSAGE_KEY_NAME => "Při úpravě se vyskytla chyba"
            ],
            [
                Messages::ACTION_KEY_NAME => "remove",
                Messages::NAME_KEY_NAME => "error",
                Messages::MESSAGE_KEY_NAME => "Při mazání se vyskytla chyba"
            ],
            [
                Messages::ACTION_KEY_NAME => "list",
                Messages::NAME_KEY_NAME => "sorting-error",
                Messages::MESSAGE_KEY_NAME => "Řazení dle zadaných parametrů se nazdařilo. Výsledky jsou zobrazeny neseřazené."
            ],
            [
                Messages::ACTION_KEY_NAME => "add",
                Messages::NAME_KEY_NAME => "copy-error",
                Messages::MESSAGE_KEY_NAME => "Při kopírování nastala chyba. Přidejte novou položku."
            ],
            [
                Messages::NAME_KEY_NAME => "not_found",
                Messages::MESSAGE_KEY_NAME => "Požadovaná položka nebyla nalezena"
            ],
            [
                Messages::NAME_KEY_NAME => "empty_id",
                Messages::MESSAGE_KEY_NAME => "Nebylo vyplněno ID položky"
            ],
            [
                Messages::ACTION_KEY_NAME => "list",
                Messages::NAME_KEY_NAME => "header",
                Messages::MESSAGE_KEY_NAME => "Přehled"
            ],
            [
                Messages::ACTION_KEY_NAME => "add",
                Messages::NAME_KEY_NAME => "header",
                Messages::MESSAGE_KEY_NAME => "Přidání položky"
            ],
            [
                Messages::ACTION_KEY_NAME => "edit",
                Messages::NAME_KEY_NAME => "header",
                Messages::MESSAGE_KEY_NAME => "Úprava položky"
            ],
            [
                Messages::ACTION_KEY_NAME => "add",
                Messages::NAME_KEY_NAME => "form_button",
                Messages::MESSAGE_KEY_NAME => "Přidat"
            ],
            [
                Messages::ACTION_KEY_NAME => "edit",
                Messages::NAME_KEY_NAME => "form_button",
                Messages::MESSAGE_KEY_NAME => "Upravit"
            ],
        ]);

        return $this;
    }

    /**
     * @param array $messages
     * @return self
     */
    public function addMessages(array $messages): self
    {
        $this->Messages->addMessages($messages);

        return $this;
    }

    /**
     * @return BaseCRMController
     */
    public function build(): BaseCRMController
    {
        $this->BaseCRM->set(
            $this->Validator,
            $this->Service,
            $this->Messages,
            $this->entityName,
            $this->formTypeName,
            $this->formOptions,
            $this->templates
        );

        return $this->BaseCRM;
    }
}
