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
     * Array cuyo codigo es el codigo de apartamento y los datos un array del siguiente tipo:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre de persona.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @var array del tipo array('codapar' => array('apart','codpers','nombre','repre','voto')...) 
     */
    private $aAsistentes;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    public function __construct($fecha) {
        $this->cargarAsistentes($this->fechaIso_Base($fecha));
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
     * Carga los asistentes a una Junta General.
     * Los datos se cargan en un array cuyo codigo es el codigo de apartamento y los datos un array:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre de persona.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @param type $fecha
     */
    private function cargarAsistentes($fecha) {
        $aDatos = array();
        if($fecha) {
            $rRes = $this->ejecutarSQL("SELECT J.CODAPAR,CONCAT('Portal ',PORTAL,'-',PISO,LETRA) AS APARTAMENTO,J.CODPERS,CONCAT(P.APELLIDOS,' ',P.NOMBRE) AS NOM,J.REPRESENTADO,J.VOTO,A.COEFICIENTE,A.COEFICIENTEFASE,A.COEFICIENTEBLOQ FROM ASISTENTES J LEFT JOIN APARTAMENTOS A ON A.CODAPAR=J.CODAPAR LEFT JOIN PERSONAS P ON P.CODPERS=J.CODPERS WHERE J.FECHA='$fecha' ORDER BY J.CODAPAR");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $aDatos[$aRow['CODAPAR']] = array($aRow['APARTAMENTO'],$aRow['CODPERS'],$aRow['NOM'],$aRow['REPRESENTADO'],$aRow['VOTO'],$aRow['COEFICIENTE'],$aRow['COEFICIENTEFASE'],$aRow['COEFICIENTEBLOQ']);
            }
            $rRes->closeCursor(); 
        }
        $this->fecha = $fecha;
        $this->aAsistentes = $aDatos;
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
     * Obtiene los datos de los asistentes a una Junta.
     * Array cuyo codigo es el codigo de apartamento y los datos un array del siguiente tipo:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Codigo de persona.</li>
     * <li>2 - Nombre de persona.</li>
     * <li>3 - Representado, S/N.</li>
     * <li>4 - Voto, S/N.</li>
     * <li>5 - Coeficiente urbanizacion.</li>
     * <li>6 - Coeficiente fase 200%.</li>
     * <li>7 - Coeficiente bloque.</li>
     * </ul>
     * @return array
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
