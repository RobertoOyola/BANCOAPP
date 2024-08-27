<?php

namespace App\DTO;

class LoginDTO
{
    public ?string $correo;
    public ?string $contrasenia;

    public function __construct(?string $correo, ?string $contrasenia)
    {
        $this->correo = $correo;
        $this->contrasenia = $contrasenia;
    }
}
