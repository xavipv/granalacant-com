<?php

/**
 * Clase Persona.
 */

/**
 * La clase Persona permite interactuar con los datos de una persona.
 * Permite agregar y obtener los datos de una persona determinada.
 *
 * @author xavi
 */
class Persona {
    
    //--- VARIABLES ---------------------------------------------------//
    
    /**
     * Codigo de la persona.
     * 
     * @var int Codigo de persona. 
     */
    private $codigo;
    
    /**
     * Apellidos de la persona.
     * 
     * @var string Apellidos. 
     */
    private $apellidos;
    
    /**
     * Nombre de la persona.
     * 
     * @var string Nombre. 
     */
    private $nombre;
    
    /**
     * Sexo de la persona.
     * <ul>
     * <li>(vacio) - No es una persona.</li>
     * <li>H - Hombre.</li>
     * <li>M - Mujer.</li>
     * </ul>
     * 
     * @var string Sexos H/M. 
     */
    private $sexo;
    
    /**
     * Codigo de usuario.
     * 
     * @var string Usuario. 
     */
    private $codusu;
    
    /**
     * Correo electronico de la persona.
     * 
     * @var string Correo electronico. 
     */
    private $correo;
    
    /**
     * Realizar envios de correos electronicos.
     * <ul>
     * <li>S - Realizar envios.</li>
     * <li>N - No realizar envios.</li>
     * </ul>
     * 
     * @var string Envios S/N. 
     */
    private $envios;
    
    /**
     * Numero de telefono de la persona.
     * 
     * @var string Telefono. 
     */
    private $telefono;
    
    /**
     * Notas sobre la persona.
     * 
     * @var string Notas. 
     */
    private $notas;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     * 
     * @param int $cod Codigo de persona.
     */
    public function __construct($cod=0) {
        $this->cargarPersona($cod);
    }
    
    //--- METODOS PRIVADOS Y PROTEGIDOS --------------------------------------//
    
    /**
     * Ejecuta una sentencia SQL y devuelve los resultados.
     * 
     * @param string $sql Sentencia a ejecutar.
     * @return result Resultado de la ejecución.
     */
    protected function ejecutarSQL($sql) {
        try { 
            $rRes = Base::prepare($sql);
            $rRes->execute();
        } catch (Exception $exc) {
            die("ERROR en ejecutarSQL:\n\n$sql\n\n" . $exc->getTraceAsString());
            exit();
        }
        return $rRes;
    }
    
    /**
     * Obtiene el siguiente codigo de persona disponible en la base de datos.
     * 
     * @return int Codigo de persona.
     */
    private function siguienteCodigo() {
        $cod  = 1;
        $rRes = $this->ejecutarSQL("SELECT MAX(CODPERS)+1 AS NUM FROM PERSONAS;");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $cod = $aRow['NUM'];
        }
        return $cod;
    }

    /**
     * Carga los datos de la persona.
     * 
     * @param int Codigo de persona.
     */
    private function cargarPersona($cod) { 
        $this->cargarPersonaOmision();
        if($cod) {
            $rRes = $this->ejecutarSQL("SELECT CODPERS,APELLIDOS,NOMBRE,SEXO,CODUSU,CORREO,ENVIOS,TELEFONO,NOTAS FROM PERSONAS WHERE CODPERS='$cod'");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $this->codigo = $aRow['CODPERS'];
                $this->apellidos = $aRow['APELLIDOS'];
                $this->nombre = $aRow['NOMBRE'];
                $this->sexo = $aRow['SEXO'];
                $this->codusu = $aRow['CODUSU'];
                $this->correo = $aRow['CORREO'];
                $this->envios = $aRow['ENVIOS'];
                $this->telefono = $aRow['TELEFONO'];
                $this->notas = $aRow['NOTAS'];
            }
            $rRes->closeCursor(); 
        }
    }
    
    /**
     * Carga los datos por omision de una persona.
     */
    private function cargarPersonaOmision() {
        $this->codigo = 0;
        $this->apellidos = '';
        $this->nombre = '';
        $this->sexo = '';
        $this->codusu = '';
        $this->correo = '';
        $this->envios = '';
        $this->telefono = '';
        $this->notas = '';
    }
    
    /**
     * Codifica caracteres especiales en entidades HTML.
     * 
     * @param string $txt Texto a codificar.
     * @return string Texto codificado.
     */
    private function codificar($txt) {
        return htmlspecialchars(trim($txt), ENT_QUOTES, 'UTF-8', FALSE);
    }
    
    /**
     * Decodifica entidades HTML a caracteres especiales. A las comillas les añade una barra.
     * 
     * @param string $txt Texto a decodificar.
     * @return string Texto decodificado.
     */
    private function decodificar($txt) {
        return htmlspecialchars_decode($txt, ENT_QUOTES);
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene el codigo de la persona.
     * 
     * @return int Codigo de persona.
     */
    public function getCodigo() {
        return $this->codigo;
    }
    
    /**
     * Asigna los apellidos de la persona.
     * 
     * @param string $txt Apellidos. No se permite que este vacio.
     */
    public function setApellidos($txt) {
        // Los apellidos no pueden estar vacios.
        $this->apellidos = (trim($txt)) ? $this->codificar($txt) : $this->apellidos;
    }
    
    /**
     * Obtiene los apellidos de la persona.
     * 
     * @return string Apellidos.
     */
    public function getApellidos() {
        return $this->decodificar($this->apellidos);
    }
    
    /**
     * Asigna el nombre de la persona.
     * 
     * @param string $txt Nombre.
     */
    public function setNombre($txt) {
        $this->nombre = $this->codificar($txt);
    }
    
    /**
     * Obtiene el nombre de la persona.
     * 
     * @return string Nombre.
     */
    public function getNombre() {
        return $this->decodificar($this->nombre);
    }
    
    /**
     * Obtiene los apellidos y nombre de la persona.
     * Formato: Apellidos Nombre
     * 
     * @return string Apellidos y nombre.
     */
    public function getNombreCompleto() {
        return $this->getApellidos() . " " . $this->getNombre();
    }
    
    /**
     * Asigna el sexo de la persona.
     * <ul>
     * <li>(vacio) - No es una persona.</li>
     * <li>H - Hombre.</li>
     * <li>M - Mujer.</li>
     * </ul>
     * 
     * @param string $txt Sexo H/M.
     */
    public function setSexo($txt) {
        if($txt=='H' || $txt=='h') {
            $txt = 'H';
        } elseif($txt=='M' || $txt=='m') {
            $txt = 'M';
        } else {
            $txt = '';
        }
        $this->sexo = $txt;
    }
    
    /**
     * Obtiene el sexo de la persona.
     * <ul>
     * <li>(vacio) - No es una persona.</li>
     * <li>H - Hombre.</li>
     * <li>M - Mujer.</li>
     * </ul>
     * 
     * @return string Sexo H/M.
     */
    public function getSexo() {
        return $this->sexo;
    }
    
    /**
     * Asigna un usuario para la persona.
     * 
     * @param string $txt Usuario.
     */
    public function setUsuario($txt) {
        $this->codusu = trim($txt);
    }
    
    /**
     * Obtiene el usuario asignado a la persona.
     * 
     * @return string Usuario.
     */
    public function getUsuario() {
        return $this->codusu;
    }
    
    /**
     * Asigna el correo electronico de la persona.
     * 
     * @param string $txt Correo electronico.
     */
    public function setCorreo($txt) {
        $this->correo = trim($txt);
    }
    
    /**
     * Obtiene el correo electronico de la persona.
     * 
     * @return string Correo electronico.
     */
    public function getCorreo() {
        return $this->correo;
    }
    
    /**
     * Asigna si se le enviaran correos electronicos.
     * <ul>
     * <li>S - Realizar envios.</li>
     * <li>N - No realizar envios.</li>
     * </ul>
     * 
     * @param string $txt Enviar correos electronicos S/N.
     */
    public function setEnvios($txt='N') {
        if($txt=='S' || $txt=='s') {
            $txt = 'S';
        } else {
            $txt = 'N';
        }
        $this->envios = $txt;
    }
    
    /**
     * Obtiene si se le enviaran correos electronicos.
     * <ul>
     * <li>S - Realizar envios.</li>
     * <li>N - No realizar envios.</li>
     * </ul>
     * 
     * @return string Enviar correos electronicos S/N.
     */
    public function getEnvios() {
        return $this->envios;
    }
    
    /**
     * Asigna el telefono de la persona.
     * 
     * @param string $txt Numero de telefono.
     */
    public function setTelefono($txt) {
        $this->telefono = trim($txt);
    }
    
    /**
     * Obtiene el telefono de la persona.
     * 
     * @return string Numero de telefono.
     */
    public function getTelefono() {
        return $this->telefono;
    }
    
    /**
     * Asigna las notas sobre la persona.
     * 
     * @param string $txt Notas.
     */
    public function setNotas($txt) {
        $this->notas = $this->codificar($txt);
    }
    
    /**
     * Obtiene las notas sobre la persona.
     * 
     * @return string Notas.
     */
    public function getNotas() {
        return $this->decodificar($this->notas);
    }
    
    /**
     * Graba los datos de la persona.
     * 
     * @return boolean Devuelve TRUE si todo ha ido bien o FALSE si ha habido algun fallo.
     */
    public function grabar() {
        $cod = $this->codigo;
        $ape = $this->apellidos;
        $nom = $this->nombre;
        $sex = $this->sexo;
        $usu = $this->codusu;
        $cor = $this->correo;
        $env = $this->envios;
        $tel = $this->telefono;
        $not = $this->notas;
        if($cod) {
            // UPDATE.
            $sql = "UPDATE PERSONAS SET APELLIDOS='$ape',NOMBRE='$nom',SEXO='$sex',CODUSU='$usu',CORREO='$cor',ENVIOS='$env',TELEFONO='$tel',NOTAS='$not' WHERE CODPERS='$cod'";
        } else {
            // INSERT.
            $cod = $this->siguienteCodigo();
            $sql = "INSERT INTO PERSONAS (CODPERS,APELLIDOS,NOMBRE,SEXO,CODUSU,CORREO,ENVIOS,TELEFONO,NOTAS) VALUES ('$cod','$ape','$nom','$sex','$usu','$cor','$env','$tel','$not')";
        }
        return $this->ejecutarSQL($sql);
    }
}
