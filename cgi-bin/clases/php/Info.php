<?php

/**
 * Clase Info.
 */

/**
 * La clase Asistentes obtiene informacion general.
 *
 * @author xavi
 */
class Info {

    //--- INSTANCIACION ------------------------------------------------------//
    
    public function __construct() {

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
    
    //--- METODOS PUBLICOS ---------------------------------------------------//

    /**
     * Obtiene el nombre de la base de datos que se esta usando.
     * 
     * @return string Nombre de la base de datos.
     */
    public function getNombreBD() {
        $nom = "";
        $rRes = $this->ejecutarSQL("SELECT DATABASE()");
        while($aRow = $rRes->fetch(PDO::FETCH_NUM)) {
            $nom = $aRow[0];
        }
        $rRes->closeCursor(); 
        return $nom;
    }
    
    /**
     * Obtiene los nombre de las tablas de la base de datos actual.
     * 
     * @return array del tipo array('tabla1','tabla2'...)
     */
    public function getNombreTablasBD() {
        $aNom = array();
        $rRes = $this->ejecutarSQL("SHOW TABLES");
        while($aRow = $rRes->fetch(PDO::FETCH_NUM)) {
            $aNom[] = $aRow[0];
        }
        $rRes->closeCursor(); 
        return $aNom;
    }
}