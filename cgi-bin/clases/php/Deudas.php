<?php

/**
 * Clase Deudas.
 */

/**
 * La clase Deudas obtiene los datos de las deudas que tienen los apartamentos.
 *
 * @author xavi
 */
class Deudas {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Array cuyas claves son las <b>fechas</b> y los <b>codigos de apartamento</b>, y los valores:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Deuda ordinaria.</li>
     * <li>5 - Deuda extraordinaria.</li>
     * <li>6 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @var array del tipo array('fecha'=>array('portal'=>array('piso','letra','fase','ordinaria','extraordinaria','fechaiso')...)...)
     */
    private $aDeudas;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     */
    function __construct() {
        $this->cargar();
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
     * Carga las deudas de los apartamentos.
     */
    private function cargar() {
        $this->cargarOmision();
        $rRes = $this->ejecutarSQL("SELECT D.FECHA,D.CODAPAR,A.PORTAL,A.PISO,A.LETRA,A.FASE,D.ORDINARIA,D.EXTRAORDINARIA,DATE_FORMAT(D.FECHA,'%d-%m-%Y') AS FECHAISO FROM DEUDAS D LEFT JOIN APARTAMENTOS A ON A.CODAPAR=D.CODAPAR ORDER BY D.FECHA,A.CODAPAR");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $this->aDeudas[$aRow['FECHA']][$aRow['CODAPAR']] = array($aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA'],$aRow['FASE'],$aRow['ORDINARIA'],$aRow['EXTRAORDINARIA'],$aRow['FECHAISO']);
        }
        $rRes->closeCursor();
    }
    
    /**
     * Carga los datos por omision de los apartamentos.
     */
    private function cargarOmision() {
        $this->aDeudas = array();
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
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene la deudas existentes en un array cuyas claves son las <b>fechas</b> y los <b>codigos de apartamento</b>, y los valores:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Deuda ordinaria.</li>
     * <li>5 - Deuda extraordinaria.</li>
     * <li>6 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @return array del tipo array('fecha'=>array('portal'=>array('piso','letra','fase','ordinaria','extraordinaria','fechaiso')...)...)
     */
    public function getDeudas() {
        return $this->aDeudas;
    }
    
    /**
     * Obtiene las deudas en una fecha.
     * Devuelve un array cuyas claves son los <b>codigos de apartamento</b> y los valores:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Deuda ordinaria.</li>
     * <li>5 - Deuda extraordinaria.</li>
     * <li>6 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @return array del tipo array('codapar'=>array('portal','piso','letra','fase','ordinaria','extraordinaria','fechaiso')...)
     */
    public function getDeudaFecha($fecha) {
        $date = $this->fechaIso_Base($fecha);
        return $this->aDeudas[$date];
    }
    
    /**
     * Obtiene la deuda ordinaria y la extraordinaria de un apartamento en una fecha determinada.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @param int $apar Codigo del apartamento.
     * @return array con las deudas del tipo array('ordinaria','extraordinaria')
     */
    public function getDeudaFechaApartamento($fecha, $apar) {
        $ord = 0;
        $ext = 0;
        $aDeudas = $this->getDeudaFecha($fecha);
        foreach ($aDeudas as $apa => $aDeuda) {
            if ($apar == $apa) {
                $ord = $aDeuda[4];
                $ext = $aDeuda[5];
            }
        }
        return array($ord, $ext);
    }
    
    /**
     * Obtiene un array con las sumas de las deudas ordinarias y las deudas extraordinarias.
     * Si se indica una fecha solo se sumaran las deudas de esa fecha, sino se sumaran todas las deudas.
     * 
     * @param date $fecha Fecha en cualquier formato (opcional).
     * @return array con la suma de las deudas, array(suma ordinarias, suma extraordinarias)
     */
    public function getSumas($fecha='') {
        $sumao = 0;
        $sumae = 0;
        $date = ($fecha) ? $this->fechaIso_Base($fecha) : '';
        
        foreach ($this->aDeudas as $fec => $aDeudas) {
            foreach ($aDeudas as $aDatos) {
                if ($date) {
                    $sumao += ($fec == $date) ? $aDatos[4] : 0;
                    $sumae += ($fec == $date) ? $aDatos[5] : 0;
                } else {
                    $sumao += $aDatos[4];
                    $sumae += $aDatos[5];
                }
            }
        }
        return array($sumao, $sumae);
    }
    
    /**
     * Obtiene las fechas de todas las deudas.
     * 
     * @return array del tipo array('date'=>'fecha'...)
     */
    public function getFechas() {
        $aFechas = array();
        foreach ($this->aDeudas as $date => $aDat) {
            $aFechas[$date] = $aDat[6];
        }
        return $aFechas;
    }
    
    /**
     * Obtiene los años de las diferentes deudas.
     * 
     * @return array del tipo array('año1','año2','año3'...)
     */
    public function getAnyos() {
        $aAny = array();
        $sAny = "";
        foreach (array_keys($this->aDeudas) as $date) {
            $any = substr($date, 0, 4);
            if ($any != $sAny) {
                $aAny[] = $any;
                $sAny = $any;
            }
        }
        return $aAny;
    }
    
}
