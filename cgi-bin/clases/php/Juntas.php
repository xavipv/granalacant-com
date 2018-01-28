<?php

/**
 * Clase Juntas.
 */

/**
 * La clase Juntas guarda el contenido basico de todas la Juntas Generales.
 * Permite obtener datos basicos y realizar busquedas de Juntas.
 *
 * @author xavi
 */
class Juntas {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Contiene los datos de todas las juntas.
     * 
     * @var array del tipo array('fecha'=>array('fechaISO','tipo')...) 
     */
    private $aJuntas;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase Juntas.
     */
    public function __construct() {
        $this->cargarJuntas();
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
     * Carga los datos de las juntas.
     */
    private function cargarJuntas() {
        $this->aJuntas = array();
        $res = $this->ejecutarSQL("SELECT FECHA,DATE_FORMAT(FECHA,'%d-%m-%Y') AS FECHAISO,TIPO FROM JUNTAS ORDER BY FECHA DESC");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->aJuntas[$aRow['FECHA']] = array($aRow['FECHAISO'], $aRow['TIPO']);
        }
        $res->closeCursor(); 
    }
    
    /**
     * Convierte una fecha, si hace falta, del formato DD-MM-YYYY a YYYY-MM-DD.
     * 
     * @param date $fecha Fecha en cualquier formato, DD-MM-YYYY a YYYY-MM-DD.
     * @return date Fecha en formato YYYY-MM-DD.
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
     * Recarga los datos de las Juntas.
     */
    public function recargar() {
        $this->cargarJuntas();
    }

    /**
     * Obtiene los datos de todas las juntas.
     * 
     * @return array del tipo array('fecha'=>array('fechaISO','tipo')...) 
     */
    public function getJuntas() {
        return $this->aJuntas;
    }
    
    /**
     * Obtiene los años de las diferentes juntas.
     * 
     * @return array del tipo array('año1','año2','año3'...)
     */
    public function getJuntasAnyos() {
        $aAny = array();
        $sAny = "";
        foreach (array_keys($this->aJuntas) as $date) {
            $any = substr($date, 0, 4);
            if ($any != $sAny) {
                $aAny[] = $any;
                $sAny = $any;
            }
        }
        return $aAny;
    }
    
    /**
     * Obtiene la ultima Junta.
     * 
     * @return date Fecha de la ultima Junta.
     */
    public function getUltimaJunta() {
        $aFec = array_keys($this->aJuntas);
        reset($aFec);
        return current($aFec);
    }
    
    /**
     * Obtiene el tipo de Junta indicando una fecha.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @return string Tipo de Junta.
     */
    public function getTipoJunta($fecha) {
        $sTip = "";
        $date = $this->convertirFechaISOaBD($fecha);
        $aJun = $this->aJuntas; //array('fecha'=>array('fechaISO','tipo')...)
        foreach ($aJun as $fec => $aJunta) {
            if ($fec == $date) {
                $sTip = $aJunta[1];
            }
        }
        return $sTip;
    }
    
    /**
     * Comprueba si una Junta existe.
     * 
     * @param date $fecha Fecha de la Junta en cualquier formato.
     * @return boolean TRUE si existe o FALSE si no existe.
     */
    public function existeJunta($fecha) {
        $date = $this->fechaIso_Base($fecha);
        return array_key_exists($date, $this->aJuntas);
    }
    
    /**
     * Convierte una fecha del formato YYYY-MM-DD a DD-MM-YYYY.
     * 
     * @param date $date Fecha en cualquier formato.
     * @return date Fecha en formato DD-MM-YYYY.
     */
    public function convertirFechaBDaISO($date) {
        return $this->fechaBase_Iso($date);
    }
    
    /**
     * Convierte una fecha del formato DD-MM-YYYY a YYYY-MM-DD.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @return date Fecha en formato YYYY-MM-DD.
     */
    public function convertirFechaISOaBD($fecha) {
        return $this->fechaIso_Base($fecha);
    }
}
