<?php
// classes/Utilisateur.php

class Utilisateur
{
    private ?int $id;
    private string $email;
    private string $motDePasseHache;

    public function __construct(string $email, string $motDePasseHache, ?int $id = null)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Adresse e-mail invalide.");
        }

        $this->email = strtolower(trim($email));
        $this->motDePasseHache = $motDePasseHache;
        $this->id = $id;
    }

    public static function creer(string $email, string $motDePasseClair): self
    {
        if (strlen($motDePasseClair) < 12) {
            throw new InvalidArgumentException("Le mot de passe doit faire au moins 12 caractères.");
        }

        return new self($email, password_hash($motDePasseClair, PASSWORD_DEFAULT));
    }

    public function verifierMotDePasse(string $clair): bool
    {
        return password_verify($clair, $this->motDePasseHache);
    }

    public function getEmail(): string { return $this->email; }
    public function getId(): ?int      { return $this->id; }
    public function getMotDePasseHache(): string { return $this->motDePasseHache; }
}
