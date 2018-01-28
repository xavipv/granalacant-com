<?php

/**
 * Clase Administraciones.
 */

/**
 * La clase Administraciones contiene los datos basicos de todas las administraciones guardadas.
 * Permite obtener los datos basicos de las administraciones.
 * 
 * @author xavi
 */
class Administraciones {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Contiene los datos basicos de todas las administraciones.
     * Es un array cuyo indice es el <b>codigo de administracion</b> y como datos tiene:
     * <ul>
     * <li>0 - Nombre de la administracion.</li>
     * <li>1 - Codigo del administrador.</li>
     * <li>2 - Activa S/N.</li>
     * </ul>
     * 
     * @var array del tipo array('codadm'=>array('nombre', 'administrador')...) 
     */
    private $aAdministraciones;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     */
    public function __construct() {
        $this->cargarAdministraciones();
    }
    
    //--- METODOS PRIVADOS ---------------------------------------------------//
    
    /**
     * Ejecuta una sentencia SQL y devuelve los resultados.
     * 
     * @param string $sql Sentencia a ejecutar.
     * @return result Resultado de la ejecución.
     */
    private function ejecutarSQL($sql) {
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
     * Carga los datos de las administraciones.
     */
    private function cargarAdministraciones() {
        $res = $this->ejecutarSQL("SELECT CODADM,NOMBRE,ADMINISTRADOR,ACTIVA FROM ADMINISTRACIONES ORDER BY NOMBRE,CODADM");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->aAdministraciones[$aRow['CODADM']] = array($this->decodificar($aRow['NOMBRE']), $aRow['ADMINISTRADOR'], $aRow['ACTIVA']);
        }
        $res->closeCursor(); 
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
     * Obtiene los datos basicos de todas las administraciones.
     * Es un array cuyo indice es el <b>codigo de administracion</b> y como datos tiene:
     * <ul>
     * <li>0 - Nombre de la administracion.</li>
     * <li>1 - Codigo del administrador.</li>
     * <li>2 - Activa S/N.</li>
     * </ul>
     * 
     * @return array del tipo array('codadm'=>array('nombre', 'administrador')...) 
     */
    public function getAdministraciones() {
        return $this->aAdministraciones;
    }
    
    /**
     * Obtiene los nombres de todas las administraciones.
     * 
     * @return array del tipo array('nombre1', 'nombre2'...)
     */
    public function getNombresAdministraciones() {
        $aNoms = array();
        foreach ($this->aAdministraciones as $adm => $aAdministracion) {
            $aNoms[$adm] = $aAdministracion[0];
        }
        return $aNoms;
    }
    
    /**
     * Dando el codigo de una administracion obtiene su nombre.
     * 
     * @param int $adm Codigo de administracion.
     * @return string Nombre de la administracion.
     */
    public function getNombreAdministracion($adm) {
        return $this->aAdministraciones[$adm][0];
    }
    
    /**
     * Dando el codigo de una administracion obtiene el codigo de su administrador.
     * 
     * @param int $adm Codigo de administracion.
     * @return int Codigo del administrador.
     */
    public function getAdministrador($adm) {
        return $this->aAdministraciones[$adm][1];
    }
    
    /**
     * Dando el codigo de una administracion nos indica si esta activa o no.
     * 
     * @param int $adm Codigo de administracion.
     * @return boolean Devuelve TRUE si esta activa o FALSE si no lo esta.
     */
    public function estaActiva($adm) {
        return ($this->aAdministraciones[$adm][2] == 'S') ? TRUE : FALSE;
    }        
}
