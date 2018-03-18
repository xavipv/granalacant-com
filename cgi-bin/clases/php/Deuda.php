<?php

/**
 * Clase Deuda.
 */

/**
 * La clase Deuda gestiona la deuda de un apartamento en una fecha determinada.
 *
 * @author xavi
 */
class Deuda {
    
    /**
     * Fecha de la deuda.
     * 
     * @var date en formato YYYY-MM-DD. 
     */
    private $fecha;
    
    /**
     * Codigo del apartamento.
     * 
     * @var int Codigo de apartamento. 
     */
    private $codapar;
    
    /**
     * Valor de la deuda ordinaria.
     * 
     * @var int Deuda ordinaria. 
     */
    private $ordinaria;
    
    /**
     * Valor de la deuda extraordinaria.
     * 
     * @var int Deuda extraordinaria. 
     */
    private $extraordinaria;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @param int $codapar Codigo de apartamento.
     */
    public function __construct($fecha, $codapar) {
        $this->cargar($this->fechaIso_Base($fecha), $codapar);
    }
    
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
     * Carga los datos de la deuda.
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD.
     * @param int $codapar Codigo de apartamento.
     */
    private function cargar($fecha, $codapar) { 
        $this->cargarOmision($fecha, $codapar);
        if($fecha) {
            $rRes = $this->ejecutarSQL("SELECT FECHA,CODAPAR,ORDINARIA,EXTRAORDINARIA FROM DEUDAS WHERE FECHA='$fecha' AND CODAPAR='$codapar'");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $this->fecha = $aRow['FECHA'];
                $this->codapar = $aRow['CODAPAR'];
                $this->ordinaria = $aRow['ORDINARIA'];
                $this->extraordinaria = $aRow['EXTRAORDINARIA'];
            }
            $rRes->closeCursor(); 
        }
    }
    
    /**
     * Carga los datos por omision de una deuda.
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD.
     * @param int $codapar Codigo de apartamento.
     */
    private function cargarOmision($fecha, $codapar) {
        $this->fecha = $fecha;
        $this->codapar = $codapar;
        $this->ordinaria = 0;
        $this->extraordinaria = 0;
    }
    
    /**
     * Convierte una fecha, si hace falta, del formato DD-MM-YYYY a YYYY-MM-DD.
     * 
     * @param date $fecha Fecha en cualquier formato, DD-MM-YYYY a YYYY-MM-DD.
     * @return date $fecha en formato YYYY-MM-DD.
     */
    private function fechaIso_Base($fecha) {
        $date = $fecha;
        if ($fecha && substr($fecha, 2, 1) == '-' && substr($fecha, 5 , 1) == '-') {
            // Fecha del tipo DD-MM-YYYY
            $res = $this->ejecutarSQL("SELECT STR_TO_DATE('$fecha','%d-%m-%Y') AS FECHA");
            while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
                $date = $aRow['FECHA'];
            }
            $res->closeCursor();    
        }
        return $date;
    }
    
    /**
     * Convierte una fecha, si hace falta, del formato YYYY-MM-DD a DD-MM-YYYY.
     * 
     * @param date $date Fecha en cualquier formato, DD-MM-YYYY a YYYY-MM-DD.
     * @return date Fecha en formato DD-MM-YYYY.
     */
    private function fechaBase_Iso($date) {
        $fecha = $date;
        if ($date && substr($date, 4, 1) == '-' && substr($date, 7 , 1) == '-') {
            // Fecha del tipo YYYY-MM-DD
            $res = $this->ejecutarSQL("SELECT DATE_FORMAT('$date','%d-%m-%Y') AS FECHA");
            while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
                $fecha = $aRow['FECHA'];
            }
            $res->closeCursor();    
        }
        return $fecha;
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene la fecha de la deuda.
     * 
     * @return date Fecha en formato YYYY-MM-DD.
     */
    public function getFecha() {
        return $this->fecha;
    }
    
    /**
     * Obtiene la fecha de la deuda.
     * 
     * @return date Fecha en formato DD-MM-YYYY.
     */
    public function getFechaISO() {
        return $this->fechaBase_Iso($this->fecha);
    }
    
    public function getApartamento() {
        return $this->codapar;
    }
    
    /**
     * Asigna la deuda ordinaria.
     * 
     * @param int $eur Deuda ordinaria.
     */
    public function setOrdinaria($eur) {
        $this->ordinaria = $eur;
    }
    
    /**
     * Obtiene el valor de la deuda ordinaria.
     * 
     * @return int Deuda ordinaria.
     */
    public function getOrdinaria() {
        return $this->ordinaria;
    }
    
    /**
     * Asigna la deuda extraordinaria.
     * 
     * @param int $eur Deuda extraordinaria.
     */
    public function setExtraordinaria($eur) {
        $this->extraordinaria = $eur;
    }
    
    /**
     * Obtiene el valor de la deuda extraordinaria.
     * 
     * @return int Deuda extraordinaria.
     */
    public function getExtraordinaria() {
        return $this->extraordinaria;
    }
    
    /**
     * Obtiene el total de la deuda.
     * 
     * @return int Total de la deuda.
     */
    public function getDeuda() {
        return $this->ordinaria + $this->extraordinaria;
    }
    
    /**
     * Guarda los datos de la deuda en la base de datos.
     * 
     * @return boolean Devuelve TRUE si todo ha ido bien o FALSE si algo ha fallado.
     */
    public function grabar() {
        $fecha = $this->fecha;
        $apart = $this->codapar;
        $ordin = $this->ordinaria;
        $extra = $this->extraordinaria;
        
        return $this->ejecutarSQL("REPLACE INTO DEUDAS (FECHA,CODAPAR,ORDINARIA,EXTRAORDINARIA) VALUES ('$fecha','$apart','$ordin','$extra')");
    }
    
    /**
     * Elimina los datos de la deuda de la base de datos.
     * 
     * @return boolean Devuelve TRUE si todo ha ido bien o FALSE si algo ha fallado.
     */
    public function eliminar() {
        $fecha = $this->fecha;
        $apart = $this->codapar;
        
        return $this->ejecutarSQL("DELETE FROM DEUDAS WHERE FECHA='$fecha' AND CODAPAR='$apart'");
    }
}
