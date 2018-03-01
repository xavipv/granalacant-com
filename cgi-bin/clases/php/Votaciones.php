<?php

/**
 * Clase Votaciones.
 */

/**
 * La clase Votaciones guarda el contenido de todas las votaciones realizadas.
 * Permite obtener datos basicos y realizar busquedas en las votaciones guardadas.
 *
 * @author xavi
 */
class Votaciones {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Guarda las votaciones realizadas.
     * Es un array que tiene como clave la <b>fecha</b> de la votacion que contiene
     * varios arrays cuyas claves son los <b>numeros</b> de la votacion y su contenido
     * la <b>fecha</b> en formato ISO.
     * 
     * @var array del tipo array('fecha'=>array('numvot'=>'fechaISO'...)...) 
     */
    private $aVotaciones;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     */
    public function __construct() {
        $this->cargarVotaciones();
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
     * Carga los datos de las votaciones.
     */
    private function cargarVotaciones() {
        $res = $this->ejecutarSQL("SELECT DISTINCT FECHA,DATE_FORMAT(FECHA,'%d-%m-%Y') AS FECHAISO,NUMVOT FROM VOTOS ORDER BY FECHA DESC,NUMVOT");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->aVotaciones[$aRow['FECHA']][$aRow['NUMVOT']] = $aRow['FECHAISO'];
        }
        $res->closeCursor(); 
    }
    
    /**
     * Convierte una fecha, si hace falta, del formato DD-MM-YYYY a YYYY-MM-DD.
     * 
     * @param date $fecha Fecha en cualquier formato, DD-MM-YYYY o YYYY-MM-DD.
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
     * @param date $date Fecha en cualquier formato, DD-MM-YYYY o YYYY-MM-DD.
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
     * Recarga los datos de las votaciones.
     */
    public function recargar() {
        $this->cargarVotaciones();
    }

    /**
     * Obtiene los datos de todas las votaciones.
     * 
     * @return array del tipo array('fecha'=>array('numvot'=>'fechaISO'...)...) 
     */
    public function getVotaciones() {
        return $this->aVotaciones;
    }
    
    /**
     * Obtiene los años de las diferentes votaciones.
     * 
     * @return array del tipo array('año1','año2','año3'...)
     */
    public function getVotacionesAnyos() {
        $aAny = array();
        $sAny = "";
        foreach (array_keys($this->aVotaciones) as $date) {
            $any = substr($date, 0, 4);
            if ($any != $sAny) {
                $aAny[] = $any;
                $sAny = $any;
            }
        }
        return $aAny;
    }
    
    /**
     * Obtiene la fecha de la ultima votacion realizada.
     * 
     * @return fecha en formato YYYY-MM-DD.
     */
    public function getUltimaFechaVotacion() {
        $aAnys = array_keys($this->aVotaciones);
        reset($aAnys);
        return current($aAnys);
    }
    
    /**
     * Comprueba si una votacion ya existe.
     * 
     * @param date $fecha Fecha de la votacion en cuaquier formato.
     * @param int $num Numero de votacion.
     * @return boolean devuelve TRUE si existe y FALSE si no existe.
     */
    public function existeVotacion($fecha, $num=1) {
        $bVot = FALSE;
        $date = $this->fechaIso_Base($fecha); 
        if (array_key_exists($date, $this->aVotaciones)) {
            $aVot = $this->aVotaciones[$date];
            $bVot = array_key_exists($num, $aVot);
        }
        return $bVot;
    }
    
    /**
     * Obtiene el numero de la ultima votacion realizada en una fecha.
     * 
     * @param date $fecha Fecha de la votacion en cuaquier formato.
     * @return int Numero de la ultima votacion.
     */
    public function getUltimaVotacion($fecha) {
        $num  = 0;
        $date = $this->fechaIso_Base($fecha);
        $aVot = array_keys($this->aVotaciones[$date]);
        foreach ($aVot as $numvot) {
            $num = ($numvot > $num) ? $numvot : $num;
        }
        return $num;
    }
    
    /**
     * Convierte una fecha del formato YYYY-MM-DD a DD-MM-YYYY.
     * 
     * @param date $date Fecha en formato DD-MM-YYYY o YYYY-MM-DD.
     * @return date Fecha en formato YYYY-MM-DD.
     */
    public function convertirFechaBDaISO($date) {
        return $this->fechaBase_Iso($date);
    }
    
    /**
     * Convierte una fecha del formato DD-MM-YYYY a YYYY-MM-DD.
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD o DD-MM-YYYY.
     * @return date Fecha en formato DD-MM-YYYY.
     */
    public function convertirFechaISOaBD($fecha) {
        return $this->fechaIso_Base($fecha);
    }
}
