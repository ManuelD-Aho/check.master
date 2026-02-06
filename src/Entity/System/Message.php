<?php
declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'message_systeme', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_message_systeme_code', columns: ['code_message'])])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_message', type: 'integer')]
    private ?int $idMessage = null;

    #[ORM\Column(name: 'code_message', type: 'string', length: 100)]
    private string $codeMessage;

    #[ORM\Column(name: 'categorie', type: 'string', length: 50)]
    private string $categorie;

    #[ORM\Column(name: 'contenu_message', type: 'text')]
    private string $contenuMessage;

    #[ORM\Column(name: 'type_message', enumType: MessageType::class)]
    private MessageType $typeMessage;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    public function getIdMessage(): ?int
    {
        return $this->idMessage;
    }

    public function getCodeMessage(): string
    {
        return $this->codeMessage;
    }

    public function setCodeMessage(string $codeMessage): self
    {
        $this->codeMessage = $codeMessage;

        return $this;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getContenuMessage(): string
    {
        return $this->contenuMessage;
    }

    public function setContenuMessage(string $contenuMessage): self
    {
        $this->contenuMessage = $contenuMessage;

        return $this;
    }

    public function getTypeMessage(): MessageType
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(MessageType $typeMessage): self
    {
        $this->typeMessage = $typeMessage;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
