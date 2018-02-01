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
     * Los datos se cargan en un array que tiene como claves las <b>fechas</b> de las Juntas y como valores:
     * <ul>
     * <li>0 - Fecha en formato ISO.</li>
     * <li>1 - Tipo de junta.</li>
     * <li>2 - Presidente.</li>
     * <li>3 - Vicepresidente 1.</li>
     * <li>4 - Vicepresidente 2.</li>
     * <li>5 - Vocal 1.</li>
     * <li>6 - Vocal 2.</li>
     * <li>7 - Vocal 3.</li>
     * <li>8 - Vocal 4.</li>
     * <li>9 - Secretario.</li>
     * <li>10 - Administracion.</li>
     * </ul>
     */
    private function cargarJuntas() {
        $this->aJuntas = array();
        $res = $this->ejecutarSQL("SELECT FECHA,DATE_FORMAT(FECHA,'%d-%m-%Y') AS FECHAISO,TIPO,PRESIDENTE,VICEPRESIDENTE1,VICEPRESIDENTE2,VOCAL1,VOCAL2,VOCAL3,VOCAL4,SECRETARIO,ADMINISTRACION FROM JUNTAS ORDER BY FECHA DESC");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->aJuntas[$aRow['FECHA']] = array($aRow['FECHAISO'], $aRow['TIPO'], $aRow['PRESIDENTE'], $aRow['VICEPRESIDENTE1'], $aRow['VICEPRESIDENTE2'], $aRow['VOCAL1'], $aRow['VOCAL2'], $aRow['VOCAL3'], $aRow['VOCAL4'], $aRow['SECRETARIO'], $aRow['ADMINISTRACION']);
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
     * Se devuelven en un array que tiene como claves las <b>fechas</b> de las Juntas y como valores:
     * <ul>
     * <li>0 - Fecha en formato ISO.</li>
     * <li>1 - Tipo de junta.</li>
     * <li>2 - Presidente.</li>
     * <li>3 - Vicepresidente 1.</li>
     * <li>4 - Vicepresidente 2.</li>
     * <li>5 - Vocal 1.</li>
     * <li>6 - Vocal 2.</li>
     * <li>7 - Vocal 3.</li>
     * <li>8 - Vocal 4.</li>
     * <li>9 - Secretario.</li>
     * <li>10 - Administracion.</li>
     * </ul>
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
     * Obtiene los datos de la junta anterior a una fecha dada.
     * Los datos se cargan en un array que tiene como clave la <b>fecha</b> de la ultima Junta y como valores:
     * <ul>
     * <li>0 - Fecha en formato ISO.</li>
     * <li>1 - Tipo de junta.</li>
     * <li>2 - Presidente.</li>
     * <li>3 - Vicepresidente 1.</li>
     * <li>4 - Vicepresidente 2.</li>
     * <li>5 - Vocal 1.</li>
     * <li>6 - Vocal 2.</li>
     * <li>7 - Vocal 3.</li>
     * <li>8 - Vocal 4.</li>
     * <li>9 - Secretario.</li>
     * <li>10 - Administracion.</li>
     * </ul>
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @return array con los datos de la Junta.
     */
    public function getJuntaAnterior($fecha) {
        $aUlt = array();
        $date = $this->convertirFechaISOaBD($fecha);
        $aJun = $this->aJuntas;
        foreach ($aJun as $fec => $aJun) {
            $aUlt = $aJun;
            if ($date > $fec) {
                return $aUlt;
            }
        }
        return $aUlt;
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
