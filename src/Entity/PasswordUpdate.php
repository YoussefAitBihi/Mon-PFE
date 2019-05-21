<?php

namespace App\Entity;

// Pour la validation
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    /**
     * @Assert\NotBlank(message="Veuillez renseigner ce champ correctement")
    */
    private $oldPassword;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner ce champ")
     * @Assert\Length(
     *  min=8,
     *  minMessage="Le mot de passe doit faire au moins 8 caractères"
     * )
    */
    private $newPassword;

    /**
     * @Assert\NotBlank(message="Veuillez confirmer le mot de passe")
     * @Assert\EqualTo(
     *  propertyPath="newPassword",
     *  message="Vous n'avez pas correctement confirmé le mot de passe, réessayer" 
     * )
    */
    private $passConfirm;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getPassConfirm(): ?string
    {
        return $this->passConfirm;
    }

    public function setPassConfirm(string $passConfirm): self
    {
        $this->passConfirm = $passConfirm;

        return $this;
    }
}