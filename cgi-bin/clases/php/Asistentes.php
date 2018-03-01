<?php

/**
 * Clase Asistentes.
 */

/**
 * La clase Asistentes guarda los datos de los asistentes a una Junta General.
 * Permite manejar los datos de los asistentes y representados en una Juna.
 *
 * @author xavi
 */
class Asistentes {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Fecha de la Junta a la que se ha asistido.
     * 
     * @var date Fecha en formato YYYY-MM-DD. 
     */
    private $fecha;
    
    /**
     * Controla como se ordenaran los datos de los asistentes, el orden puede ser:
     * <ul>
     * <li>0 - Por apartamento.</li>
     * <li>1 - Por propietarios.</li>
     * <li>2 - Por representantes.</li>
     * </ul>
     * 
     * @var int Orden 0, 1 o 2. 
     */
    private $orden;
    
    /**
     * Array cuyo codigo es el <b>codigo de apartamento</b> y los datos un array:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre del asistente.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * <li>8 - Fase.</li>
     * <li>9 - Nombre del propietario.</li>
     * </ul>
     * 
     * @var array del tipo array('codapar'=>array('apartamento','codpers','nombre','repre','voto','urba','fase200','bloque','fase','propietario')...) 
     */
    private $aAsistentes;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @param int $orden Orden para los datos: 0, 1 o 2.
     */
    public function __construct($fecha, $orden=0) {
        $this->cargarAsistentes($this->fechaIso_Base($fecha), $orden);
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
     * Carga los asistentes a una junta determinada ordenados por la opcion elegida.
     * Devuelve un array cuyo codigo es el <b>codigo de apartamento</b> y los datos un array:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre del asistente.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * <li>8 - Fase.</li>
     * <li>9 - Nombre del propietario.</li>
     * </ul>
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD.
     * @param int $orden Orden. 0 - Por apartamento. 1 - Por propietario. 2 - Por representante.
     * @return array del tipo array('codapar'=>array('apartamento','codpers','nombre','repre','voto','urba','fase200','bloque','fase','propietario')...)
     */
    private function cargarAsistentes($fecha, $orden=0) {
        $this->cargarDatosOmision($fecha, $orden);
        switch ($orden) {
            case 1 : $orden = "PROPIETARIO, A.CODAPAR"; break;
            case 2 : $orden = "REPRESENTANTE, A.CODAPAR"; break;
            default: $orden = "A.CODAPAR"; break;
        }
        if ($fecha) {
            $rRes = $this->ejecutarSQL("SELECT A.CODAPAR,CONCAT(A.PORTAL,'-',A.PISO,A.LETRA) AS APARTAMENTO,JA.CODPERS,CONCAT(P.APELLIDOS,' ',P.NOMBRE) AS ASISTENTE,JA.REPRESENTADO,JA.VOTO,A.COEFICIENTE,A.COEFICIENTEFASE,A.COEFICIENTEBLOQ,A.FASE,IF(JA.REPRESENTADO='S',(SELECT CONCAT(PE.APELLIDOS,' ',PE.NOMBRE) AS PERSONA FROM PROPIETARIOS PR LEFT JOIN PERSONAS PE ON PR.CODPERS=PE.CODPERS WHERE PR.CODAPAR=A.CODAPAR AND IFNULL(PR.BAJA,'9999-99-99')=(SELECT MIN(IFNULL(BAJA,'9999-99-99')) FROM PROPIETARIOS WHERE CODAPAR=PR.CODAPAR AND IFNULL(BAJA,'9999-99-99')>'$fecha') ORDER BY IFNULL(PR.BAJA,'9999-99-99') DESC,PR.ORDEN LIMIT 1),'') AS PROPIETARIO FROM ASISTENTES JA LEFT JOIN APARTAMENTOS A ON A.CODAPAR=JA.CODAPAR LEFT JOIN PERSONAS P ON P.CODPERS=JA.CODPERS WHERE JA.FECHA='$fecha' ORDER BY $orden");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $this->aAsistentes[$aRow['CODAPAR']] = array($aRow['APARTAMENTO'],$aRow['CODPERS'], $aRow['ASISTENTE'], $aRow['REPRESENTADO'],$aRow['VOTO'],$aRow['COEFICIENTE'],$aRow['COEFICIENTEFASE'],$aRow['COEFICIENTEBLOQ'],$aRow['FASE'],$aRow['PROPIETARIO']);
            }
            $rRes->closeCursor();
        }
    }
    
    /**
     * Carga los datos por omision.
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD.
     * @param int $orden Orden. 0 - Por apartamento. 1 - Por propietario. 2 - Por representante.
     */
    private function cargarDatosOmision($fecha, $orden) {
        $this->fecha = $fecha;
        $this->orden = ($orden != 0 && $orden != 1 && $orden != 2) ? 0 : $orden;
        $this->aAsistentes = array();
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
     * Asigna una nueva fecha.
     * 
     * @param date $fecha Fecha en cualquier formato.
     */
    public function setFecha($fecha) {
        $date = $this->fechaIso_Base($fecha);
        $orden = $this->orden;
        if ($date && $date != $this->fecha) {
            // Si cambia la fecha, recarga los datos.
            $this->cargarAsistentes($date, $orden);
        }
    }
    
    /**
     * Obtiene la fecha de la Junta a la que se asiste.
     * 
     * @return date Fecha en formato YYYY-MM-DD.
     */
    public function getFecha() {
        return $this->fecha;
    }
    
    /**
     * Obtiene la fecha de la Junta a la que se asiste, en formato ISO.
     * 
     * @return date Fecha en formato DD-MM-YYYY.
     */
    public function getFechaISO() {
        return $this->fechaBase_Iso($this->fecha);
    }
    
    /**
     * Se asigna un nuevo orden para los datos de los asistentes.
     * El orden puede ser:
     * <ul>
     * <li>0 - Por apartamento.</li>
     * <li>1 - Por propietarios.</li>
     * <li>2 - Por representantes.</li>
     * </ul>
     * 
     * @param int $orden Tipo de orden: 0, 1 o 2.
     */
    public function setOrden($orden) {
        $ord = ($orden != 0 && $orden != 1 && $orden != 2) ? 0 : $orden;
        if ($ord != $this->orden) {
            // Si cambia el orden, recarga los datos.
            $fecha = $this->fecha;
            $this->cargarAsistentes($fecha, $ord);
        }
    }
    
    /**
     * Devuelve el orden de los datos de los asistentes.
     * El orden puede ser:
     * <ul>
     * <li>0 - Por apartamento.</li>
     * <li>1 - Por propietarios.</li>
     * <li>2 - Por representantes.</li>
     * </ul>
     * 
     * @return Tipo de orden: 0, 1 o 2.
     */
    public function getOrden() {
        return $this->orden;
    }
    
    /**
     * Obtiene los datos de los asistentes a una Junta.
     * Array cuyo codigo es el <b>codigo de apartamento</b> y los datos un array:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre del asistente.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * <li>8 - Fase.</li>
     * <li>9 - Nombre del propietario.</li>
     * </ul>
     * 
     * @return array del tipo array('codapar'=>array('apartamento','codpers','nombre','repre','voto','urba','fase200','bloque','fase','propietario')...)
     */
    public function getAsistentes() {
        return $this->aAsistentes;
    }
    
    /**
     * Obtiene el numero de asistentes, representados y votos de la junta actual.
     * Los datos se devuelven en un array del siguiente tipo:
     * array(
     *      'prop' => array('propietarios', 'distintos', 'con voto', 'sin voto', 'coef. urb', 'coef. fase'),
     *      'repr' => array('representados', 'distintos', 'con voto', 'sin voto', 'coef. urb', 'coef. fase')
     * )
     * 
     * @return array con las sumas de los asistentes.
     */
    public function getSumas() {
        $aDatos = $this->getAsistentes();
        $aAsi = array();
        $aPer = array();
        $aRep = array();
        $prop = 0;
        $repr = 0;
        $vosp = 0;
        $vonp = 0;
        $vosr = 0;
        $vonr = 0;
        $coup = 0;
        $cour = 0;
        $cofp = 0;
        $cofr = 0;
        
        foreach ($aDatos as $aAsistente) {
            if($aAsistente[3] == 'S') {
                // Representante.
                $repr++;                                    // Numero de respresentantes.
                $aRep[$aAsistente[1]] = $aAsistente[1];     // Para el numero de respresentantes diferentes.
                $vosr += ($aAsistente[4] == 'S') ? 1 : 0;   // Representantes con voto.
                $vonr += ($aAsistente[4] == 'N') ? 1 : 0;   // Representantes sin voto.
                $cour += $aAsistente[5];                    // Coeficiente urbanizacion.
                $cofr += $aAsistente[6];                    // Coeficiente fase.
            } else {
                // Propietario.
                $prop++;
                $aPer[$aAsistente[1]] = $aAsistente[1];     // Para el numero de propietarios diferentes.
                $vosp += ($aAsistente[4] == 'S') ? 1 : 0;   // Propietarios con voto.
                $vonp += ($aAsistente[4] == 'N') ? 1 : 0;   // Propietarios sin voto.
                $coup += $aAsistente[5];                    // Coeficiente urbanizacion.
                $cofp += $aAsistente[6];                    // Coeficiente fase.
            }
        }
        $aAsi['prop'] = array($prop, count($aPer), $vosp, $vonp, $coup, $cofp);
        $aAsi['repr'] = array($repr, count($aRep), $vosr, $vonr, $cour, $cofr);
        return $aAsi;
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
    
    /**
     * Elimina los datos de todos los asistentes a una Junta.
     * 
     * @return boolean Devuelve TRUE si todo ha ido bien o FALSE si ha fallado algo.
     */
    public function eliminar() {
        $fecha = $this->fecha;
        return ($fecha) ? $this->ejecutarSQL("DELETE FROM ASISTENTES WHERE FECHA='$fecha'") : FALSE;
    }
}
