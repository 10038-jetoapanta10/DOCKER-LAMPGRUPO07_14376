<?php

class Usuarios {
    /* Atributos */
    private $IdUsuario;
    private $Nombre;
    private $Password;
    private $Rol;
    private $Foto;
    
    /* Constructor */
    public function __construct($IdUsuario,  $Rol,  $Nombre, $Password, $Foto){
        $this->IdUsuario = $IdUsuario;
        $this->Rol = $Rol;
        $this->Nombre = $Nombre;
        $this->Password = $Password;
        $this->Foto = $Foto;
    }

    public function getIdUsuario(){
        return $this->IdUsuario;
    }
    public function getRol(){
        return $this->Rol;
    } 
    public function getNombre(){
        return $this->Nombre;
    }
    public function getPassword(){
        return $this->Password;
    }
    public function getFoto(){
        return $this->Foto;
    }
}

  
?>