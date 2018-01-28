<?php

/**
 * Clase Peronas.
 */

/**
 * La clase Personas guarda el contenido de todas las personas.
 * Permite obtener datos y realizar busquedas en las personas.
 *
 * @author xavi
 */
class Personas {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Contiene los datos de todas las personas guardadas en la base de datos.
     * El array tiene como clave el codigo de la persona y como datos:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * <li>2 - Sexo.</li>
     * <li>3 - Codigo de usuario.</li>
     * <li>4 - Correo electronico.</li>
     * <li>5 - Enviar correos.</li>
     * <li>6 - Telefono.</li>
     * <li>7 - Notas</li>
     * </ul>
     * 
     * @var array del tipo array('cod'=>array(0 apellidos,1 nombre,2 sexo,3 codusu,4 correo,5 envios,6 telefono,7 notas)...)
     */
    private $aDatosPersonas;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase Personas.
     * Carga los datos de todas las personas.
     */
    function __construct() {
        $this->cargarPersonas();
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
     * Carga los datos de las personas guardadas en la base de datos.
     * Se guardan en el array 'aDatosPersonas' de la clase.
     */
    private function cargarPersonas() { 
        $rRes = $this->ejecutarSQL("SELECT CODPERS,APELLIDOS,NOMBRE,SEXO,CODUSU,CORREO,ENVIOS,TELEFONO,NOTAS FROM PERSONAS ORDER BY APELLIDOS,NOMBRE,CODPERS");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $this->aDatosPersonas[$aRow['CODPERS']] = array($aRow['APELLIDOS'],$aRow['NOMBRE'],$aRow['SEXO'],$aRow['CODUSU'],$aRow['CORREO'],$aRow['ENVIOS'],$aRow['TELEFONO'],$aRow['NOTAS']);
        }
        $rRes->closeCursor(); 
    }
    
    /**
     * Busca personas en la base de datos.
     * Se devuelve un array cuyas claves son los <b>codigos de personas</b> encontradas y como contenido:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * </ul>
     * 
     * @param string $buscar Datos a buscar.
     * @return array con las personas encontradas del tipo array('codpers'=>array('apellidos','nombre')...)
     */
    private function buscarPersonas($buscar) {
        $aBus = array();
        if($buscar) {
            $dato = $this->codificar($buscar);
            $rRes = $this->ejecutarSQL("SELECT CODPERS,APELLIDOS,NOMBRE FROM PERSONAS WHERE STRCMP(CODPERS,'$dato')=0 OR APELLIDOS LIKE '%$dato%' OR NOMBRE LIKE '%$dato%' ORDER BY APELLIDOS,NOMBRE,CODPERS");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $aBus[$aRow['CODPERS']] = array($aRow['APELLIDOS'],$aRow['NOMBRE']);
            }
            $rRes->closeCursor(); 
        }
        return $aBus;
    }
    
    /**
     * Obtiene un dato concreto de todas las personas.
     * Los numeros de datos pueden ser:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * <li>2 - Sexo.</li>
     * <li>3 - Codigo de usuario.</li>
     * <li>4 - Correo electronico.</li>
     * <li>5 - Enviar correos.</li>
     * <li>6 - Telefono.</li>
     * <li>7 - Notas</li>
     * </ul>
     * 
     * @param int $num Numero de dato.
     * @return array del tipo array('cod'=>'dato'...)
     */
    private function getDatos($num) {
        $aDat = array();
        $aPer = $this->aDatosPersonas;
        foreach ($aPer as $cod => $aDatos) {
            $aDat[$cod] = $aDatos[$num];
        }
        return $aDat;
    }
    
    /**
     * Obtiene los datos de personas filtrando por otro grupo de personas.
     * El array devuelto tiene como clave el codigo de persona y como datos,
     * si $nom es TRUE, el nombre completo, pero si es FALSE los datos completos:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * <li>2 - Sexo.</li>
     * <li>3 - Codigo de usuario.</li>
     * <li>4 - Correo electronico.</li>
     * <li>5 - Enviar correos.</li>
     * <li>6 - Telefono.</li>
     * <li>7 - Notas</li>
     * </ul>
     * 
     * @param array $aPers Personas a incluir o excluir.
     * @param boolean $bExc Si es TRUE se excluyen, si el FALSE se incluyen solo estas personas.
     * @param boolean $bNom Si es FALSE obtiene todos los datos si es TRUE obtiene solo el nombre.
     * @return array con los datos de las personas.
     */
    private function getPersonasIncExc($aPers, $bExc=TRUE, $bNom=FALSE) {
        $aDat = array();
        $aPer = $this->aDatosPersonas;
        foreach ($aPer as $per => $aPersona) {
            if($bExc && !array_key_exists($per, $aPers)) {
                $aDat[$per] = (!$bNom) ? $aPersona : $aPersona[0] . " " . $aPersona[1];
            } elseif (!$bExc && array_key_exists($per, $aPers)) {
                $aDat[$per] = (!$bNom) ? $aPersona : $aPersona[0] . " " . $aPersona[1];
            }
        }
        return $aDat;
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
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene los datos de todas las personas.
     * 
     * @return array del tipo array('cod'=>array(apellidos,nombre,sexo,codusu,correo,envios,telefono,notas)...)
     */
    public function getPersonas() {
        return $this->aDatosPersonas;
    }
    
    /**
     * Obtiene los datos de todas las personas incluidas en un grupo.
     * El array devuelto tiene como clave el codigo de persona y como datos,
     * si $nom es TRUE, el nombre completo, pero si es FALSE los datos completos:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * <li>2 - Sexo.</li>
     * <li>3 - Codigo de usuario.</li>
     * <li>4 - Correo electronico.</li>
     * <li>5 - Enviar correos.</li>
     * <li>6 - Telefono.</li>
     * <li>7 - Notas</li>
     * </ul>
     * 
     * @param array $aInc Grupo de personas a incluir.
     * @param boolean $bNom Si es FALSE obtiene todos los datos si es TRUE obtiene solo el nombre.
     * @return array con los datos de las personas.
     */
    public function getPersonasIncluyendo($aInc, $bNom=FALSE) {
        return $this->getPersonasIncExc($aInc, FALSE, $bNom);
    }
    
    /**
     * Obtiene los datos de todas las personas excluyendo las indicadas.
     * El array devuelto tiene como clave el codigo de persona y como datos,
     * si $nom es TRUE, el nombre completo, pero si es FALSE los datos completos:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * <li>2 - Sexo.</li>
     * <li>3 - Codigo de usuario.</li>
     * <li>4 - Correo electronico.</li>
     * <li>5 - Enviar correos.</li>
     * <li>6 - Telefono.</li>
     * <li>7 - Notas</li>
     * </ul>
     * 
     * @param array $aExc Grupo de personas a excluir.
     * @param boolean $bNom Si es FALSE obtiene todos los datos si es TRUE obtiene solo el nombre.
     * @return array con los datos de las personas.
     */
    public function getPersonasExcluyendo($aExc, $bNom=FALSE) {
        return $this->getPersonasIncExc($aExc, TRUE, $bNom);
    }
    
    /**
     * Obtiene los apellidos de todas las personas.
     * @return array('cod'=>'apellidos'...)
     */
    public function getApellidos() {
        return $this->getDatos(0);
    }
    
    /**
     * Obtiene los apellidos y nombres de las personas.
     * 
     * @param string $sep Separador entre los apellidos y el nombre.
     * @return array del tipo array('cod'=>'apellidos nombre'...)
     */
    public function getNombresCompletos($sep='') {
        $aDat = array();
        $aPer = $this->aDatosPersonas;
        foreach ($aPer as $cod => $aDatos) {
            $aDat[$cod]=$aDatos[0] . "$sep " . $aDatos[1];
        }
        return $aDat;
    }
    
    /**
     * Obtiene las distintas iniciales de todos los apellidos.
     * 
     * @return array del tipo array(ini1, ini2...)
     */
    public function getIniciales() {
        $aIni = array();
        $aApe = $this->getApellidos();
        $sIni = "";
        foreach ($aApe as $Apellido) {
            $ini = substr($Apellido, 0, 1);
            if($ini != $sIni) {
                $aIni[] = $ini;
            }
            $sIni = $ini;
        }
        return $aIni;
    }
    
    /**
     * Obtiene los nombres de las personas que coinciden con el criterio de busqueda.
     * Se devuelve un array cuyas claves son los <b>codigos de personas</b> encontradas y como contenido:
     * <ul>
     * <li>0 - Apellidos.</li>
     * <li>1 - Nombre.</li>
     * </ul>
     * 
     * @param string $busqueda Cadena a buscar.
     * @return array del tipo array('cod'=>array('apellidos','nombre')...)
     */
    public function buscar($busqueda) {
        return $this->buscarPersonas($busqueda);
    }
}
