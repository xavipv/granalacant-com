<?php

/**
 * Clase Propietario.
 */

/**
 * La clase Propietario extiende la clase Persona y la amplia con los datos de las propiedades que tiene.
 * Permite obtener datos de los apartamentos que tiene una persona.
 *
 * @author xavi
 */
class Propietario extends Persona {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Contiene todas las propiedades del propietario actual. 
     * El array tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @var array con formato array('codapar'=>array('apartamento','date','fecha','orden)...)
     */
    private $aPropiedades;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase Propietario.
     * 
     * @param int $cod Codigo de persona.
     */
    function __construct($cod = 0) {
        parent::__construct($cod);
        $this->cargarPropiedades();
    }
    
    //--- METODOS PRIVADOS ---------------------------------------------------//
    
    /**
     * Carga todas las propiedades del propietario actual.
     */
    private function cargarPropiedades() {
        $aDa = array();
        $per = $this->getCodigo();
        $res = parent::ejecutarSQL("SELECT P.CODAPAR,CONCAT('Portal ',A.PORTAL,'-',PISO,LETRA) AS APARTAMENTO,P.BAJA,DATE_FORMAT(P.BAJA,'%d-%m-%Y') AS FECHA,P.ORDEN FROM PROPIETARIOS P LEFT JOIN APARTAMENTOS A ON P.CODAPAR=A.CODAPAR WHERE P.CODPERS='$per' ORDER BY ifnull(P.BAJA,'9999-99-99') DESC,P.ORDEN");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $aDa[$aRow['CODAPAR']] = array($aRow['APARTAMENTO'], $aRow['BAJA'], $aRow['FECHA'], $aRow['ORDEN']);
        }
        $res->closeCursor();
        $this->aPropiedades = $aDa;
    }
    
    /**
     * Obtiene las propiedades de alta o de baja del propietario actual.
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @param boolean $alta Si es TRUE carga las propiedades de alta y si es FALSE carga las de baja.
     * @return array con las propiedades seleccionadas.
     */
    private function obtenerPropiedades($alta=TRUE) {
        $aRes = array();
        $aPro = $this->aPropiedades;
        foreach ($aPro as $apa => $aDat) {
            $fec = $aDat[1];    // Fecha de baja.
            if($fec && $alta) {
                $aRes[$apa] = $aDat;
            } elseif (!$fec && !$alta) {
                $aRes[$apa] = $aDat;
            }
        }
        return $aRes;
    }
    
    /**
     * Convierte una fecha de formato YYYY-MM-DD a DD-MM-YYYY o viceversa.
     * 
     * @param string $fecha Fecha a convertir.
     * @param boolean $base Indica si la fecha a convertir tiene el formato de base de datos o no.
     * @return string Fecha en el nuevo formato.
     */
    private function convertirISO_Base($fecha, $base) {
        $fec = $fecha;
        $sql = ($base) ? "SELECT DATE_FORMAT('$fecha','%d-%m-%Y') AS FECHA" : "SELECT STR_TO_DATE('$fecha','%d-%m-%Y') AS FECHA";
        $res = parent::ejecutarSQL($sql);
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $fec = $aRow['FECHA'];
        }
        $res->closeCursor();
        return $fec;
    }
    
    /**
     * Convierte una fecha de un formato a otro.
     * 
     * @param string $fecha Fecha a convertir.
     * @param boolean $base Si es TRUE se convierte a YYYY-MM-DD y si es FALSE a DD-MM-YYYY.
     * @return string Fecha en el formato indicado.
     */
    private function convertirFecha($fecha, $base=TRUE) {
        $nueva = $fecha;
        
        if($fecha && substr($fecha, 4, 1) == '-' && substr($fecha, 7 , 1) == '-') {
            // Tenemos una fecha del tipo YYYY-MM-DD
            $nueva = ($base) ? $fecha : $this->convertirISO_Base($fecha,TRUE);
            
        } elseif($fecha && substr($fecha, 2, 1) == '-' && substr($fecha, 5 , 1) == '-') {
            // Tenemos una fecha del tipo DD-MM-YYYY
            $nueva = (!$base) ? $fecha : $this->convertirISO_Base($fecha,FALSE);
        }
        return $nueva;
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene todas las propiedades del propietario actual. 
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @return array con formato array('codapar'=>array('date','fecha','orden)...)
     */
    public function getPropiedades() {
        return $this->aPropiedades;
    }
    
    /**
     * Obtiene las propiedades de alta del propietario actual. 
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @return array con formato array('codapar'=>array('date','fecha','orden)...)
     */
    public function getPropiedadesAlta() {
        return $this->obtenerPropiedades(TRUE);
    }
    
    /**
     * Obtiene las propiedades de baja del propietario actual. 
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @return array con formato array('codapar'=>array('date','fecha','orden)...)
     */
    public function getPropiedadesBaja() {
        return $this->obtenerPropiedades(FALSE);
    }
            
    /**
     * Indica si el apartamento indicado es o ha sido propiedad de la persona actual.
     * 
     * @param int $apa Codigo de apartamento.
     * @return boolean TRUE si es propiedad o FALSE si no lo es.
     */
    public function esPropiedad($apa) {
        return array_key_exists($apa, $this->aPropiedades);
        
    }
    
    /**
     * Indica si el apartamento indicado es propiedad de la persona actual.
     * 
     * @param int $apa Codigo de apartamento.
     * @return boolean TRUE si es propiedad o FALSE si no lo es.
     */
    public function esPropiedadAlta($apa) {
        $aPro = $this->aPropiedades;
        return (isset($aPro[$apa]) && !$aPro[$apa][1]) ? TRUE : FALSE;
    }
    
    /**
     * Asigna los datos de una propiedad.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fec Fecha de baja en cualquier formato.
     * @param int $ord Orden.
     */
    public function setPropiedad($apa, $fec, $ord) {  
        if ($apa) {
            $oApa = new Apartamento($apa);
            $nomb = $oApa->getApartamento();
            $date = $this->convertirFecha($fec, TRUE);
            $fech = $this->convertirFecha($fec, FALSE);
            $this->aPropiedades[$apa] = array($nomb, $date, $fech, $ord);
        }
    }
    
    /**
     * Graba los datos de las propiedades de la persona.
     * 
     * @return boolean Devuelve TRUE si todo es correcto o FALSE si ha habido algun error.
     */
    public function grabarPropiedades() {
        $bRes = TRUE;
        $iPer = $this->getCodigo();
        $aPro = $this->aPropiedades;
        foreach ($aPro as $apa => $aDatos) {
            $fec = (!$aDatos[1]) ? 'NULL' : "'" . $this->convertirFecha($aDatos[1], TRUE) . "'";
            $ord = $aDatos[3];
            $res = parent::ejecutarSQL("REPLACE INTO PROPIETARIOS (CODAPAR,CODPERS,BAJA,ORDEN) VALUES ('$apa','$iPer',$fec,'$ord')");
            $bRes = (!$res) ? FALSE : $bRes;
        }
        $this->cargarPropiedades();
        return $bRes;
    }
    
    /**
     * Elimina una propiedad de las que tiene el propietario.
     * 
     * @param int $apa Codigo de apartamento.
     * @return boolean Devuelve TRUE si todo es correcto o FALSE si ha habido algun error.
     */
    public function eliminarPropiedad($apa) {
        $iPer = $this->getCodigo();
        $res = parent::ejecutarSQL("DELETE FROM PROPIETARIOS WHERE CODAPAR='$apa' AND CODPERS='$iPer'");
        if($res) {
            $this->cargarPropiedades();
            return TRUE;
        }
        return FALSE;
    }
}
