<?php 


namespace Model;


class Usuario extends ActiveRecord {
    //Base de datos
    
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email',
     'telefono', 'admin', 'confirmado', 'token', 'password']; 

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;
    public $password;


    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';  
        $this->email = $args['email'] ?? '';   
        $this->telefono = $args['telefono'] ?? '';   
        $this->admin = $args['admin'] ?? '0';   
        $this->confirmado = $args['confirmado'] ?? '0';  
        $this->token = $args['token'] ?? '';   
        $this->password = $args['password'] ?? '';    
       
    }

    //Mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta() {
        if(!$this->nombre){ 
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido){ 
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        
        if(!$this->email){ 
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->password){ 
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }


        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    //revisa si el usuario ya existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        
        $resultado = self::$db->query($query);  
        
        if($resultado->num_rows){
            self::$alertas['error'][] = 'El usuario ya esta registrado';
        }

        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password){
       
        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado){
            self::$alertas['error'][] = 'Password Incorrecto o tu cuenta no ha sido confirmada';
        }else{
            return true;    
        }
    }

}