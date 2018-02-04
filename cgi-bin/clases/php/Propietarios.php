<?php

/**
 * Clase Propietarios.
 */

/**
 * La clase Propietarios extiende la clase Personas y la amplica con las propiedades.
 * Permite obtener datos sobre los apartamentos que tienen los propietarios.
 *
 * @author xavi
 */
class Propietarios extends Personas {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Contiene todos los propietarios y sus propiedades.
     * El array tiene como clave el codigo de persona del propietario y como
     * contenido, un array cuya clave es el codigo de apartamento con estos datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @var array('persona'=>array('codapar'=>array('apartamento','date','fecha','orden)...)...) 
     */
    private $aPropietarios;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     * 
     * @param int $ord Tipo de orden 0|1|2.
     */
    public function __construct($ord=0) {
        parent::__construct($ord);
        $this->cargarPropietarios();
    }
    
    //--- METODOS PRIVADOS ---------------------------------------------------//
    
    /**
     * Carga los datos de todos los propietarios y sus propiedades.
     * El array tiene como clave el <b>codigo de persona</b> del propietario y como
     * contenido, un array cuya clave es el <b>codigo de apartamento</b> con estos datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @var array('persona'=>array('codapar'=>array('apartamento','date','fecha','orden)...)...) 
     */
    private function cargarPropietarios() {
        $aDa = array();
        $res = parent::ejecutarSQL("SELECT P.CODPERS,P.CODAPAR,CONCAT('Portal ',A.PORTAL,'-',PISO,LETRA) AS APARTAMENTO,P.BAJA,DATE_FORMAT(P.BAJA,'%d-%m-%Y') AS FECHA,P.ORDEN FROM PROPIETARIOS P LEFT JOIN APARTAMENTOS A ON P.CODAPAR=A.CODAPAR ORDER BY P.CODPERS, ifnull(P.BAJA,'9999-99-99') DESC,P.ORDEN");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $aDa[$aRow['CODPERS']][$aRow['CODAPAR']] = array($aRow['APARTAMENTO'], $aRow['BAJA'], $aRow['FECHA'], $aRow['ORDEN']);
        }
        $res->closeCursor();
        $this->aPropietarios = $aDa;
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
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene los datos de los propietarios.
     * El array tiene como clave el <b>codigo de persona</b> del propietario y como
     * contenido, un array cuya clave es el <b>codigo de apartamento</b> con estos datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @return array('persona'=>array('codapar'=>array('apartamento','date','fecha','orden)...)...)
     */
    public function getPropietarios() {
        return $this->aPropietarios;
    }
    
    /**
     * Obtiene las propiedades de una persona.
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Fecha en formato YYYY-MM-DD.</li>
     * <li>2 - Fecha en formato DD-MM-YYYY.</li>
     * <li>3 - Orden.</li>
     * </ul>
     * 
     * @param int $per Codigo de persona.
     * @return array del tipo array('codapar'=>array('apartamento','bajaBD','bajaISO','orden')...)
     */
    public function getPropiedades($per) {
        return $this->aPropietarios[$per];
    }
    
    /**
     * Obtiene las propiedades de alta de una persona.
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Orden.</li>
     * </ul>
     * 
     * @param int $per Codigo de persona.
     * @return array del tipo array('codapar'=>array('apartamento','orden')...)
     */
    public function getPropiedadesAlta($per) {
        $aAltas = array();
        $aProps = $this->getPropiedades($per);
        foreach ($aProps as $apa => $aDatos) {
            if (!$aDatos[1]) {
                $aAltas[$apa] = array($aDatos[0], $aDatos[3]);
            }
        }
        return $aAltas;
    }
    
    /**
     * Obtiene los codigos de la propiedades de alta de una persona.
     * 
     * @param int $per Codigo de persona.
     * @return array del tipo array('apa1','apa2'...)
     */
    public function getPropiedadesAltaCodigos($per) {
        return array_keys($this->getPropiedadesAlta($per));
    }
    
    /**
     * Obtiene todas las propiedades del primer propietario de un apartamento.
     * El array devuelto tiene como clave el codigo del apartamento y como datos:
     * <ul>
     * <li>0 - Apartamento.</li>
     * <li>1 - Orden.</li>
     * </ul>
     * 
     * @param int $apa Codigo de apartamento.
     * @return array del tipo array('codapar'=>array('apartamento','orden')...)
     */
    public function getMisPropiedades($apa, $fecha='') {
        $aProps = array();
        if ($apa) {
            $date = ($fecha) ? $this->fechaIso_Base($fecha) : date('Y-m-d');
            $per = $this->getPropietarioEnFechaCodigo($apa, $date);
            $res = parent::ejecutarSQL("SELECT P.CODAPAR,CONCAT('Portal ',A.PORTAL,'-',A.PISO,A.LETRA) AS APARTAMENTO,P.ORDEN FROM PROPIETARIOS P LEFT JOIN APARTAMENTOS A ON P.CODAPAR=A.CODAPAR WHERE P.CODPERS='$per' AND P.CODPERS=(SELECT CODPERS FROM PROPIETARIOS WHERE IFNULL(BAJA,'9999-99-99') >= '$date' AND CODAPAR=P.CODAPAR ORDER BY IFNULL(BAJA,'9999-99-99') ASC,ORDEN LIMIT 1) ORDER BY P.CODAPAR");
            while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
                $aProps[$aRow['CODAPAR']] = array($aRow['APARTAMENTO'], $aRow['ORDEN']);
            }
            $res->closeCursor();
        }
        return $aProps;
    }
    
    /**
     * Obtiene el numero de propiedades que tiene una persona.
     * 
     * @param int $per Codigo de persona.
     * @return int Numero de propiedades.
     */
    public function getNumPropiedades($per) {
        return count($this->aPropietarios[$per]);
    }
    
    /**
     * Obtiene el numero de propiedades del alta, baja y total que tiene una persona.
     * 
     * @param int $per Codigo de persona.
     * @return array Propiedades en un array del tipo array(total, alta, baja)
     */
    public function getNumPropiedadesAltaBaja($per) {
        $tota = 0;
        $alta = 0;
        $baja = 0;
        $aPro = $this->aPropietarios[$per];
        foreach ($aPro as $aDatos) {
            $tota++;
            if ($aDatos[1]) {
                $baja++;    // Hay fecha.
            } else {
                $alta++;
            }
        }
        return array($tota, $alta, $baja);
    }

    /**
     * Obtiene el numero de propiedades que tiene cada persona.
     * 
     * @return array del tipo array('per'=>array(nombre, num)...)
     */
    public function getPropietariosNumPropiedades() {
        $aDatos = array();
        $aNombs = parent::getNombresCompletos();
        foreach ($aNombs as $per => $nombre) {
            $aDatos[$per] = array($nombre, $this->getNumPropiedades($per));
        }
        return $aDatos;
    }
    
    /**
     * Obtiene el numero de propidedades total, de alta y de baja que tiene cada persona.
     * 
     * @return array del tipo array('per'=>array(nombre, num, alta, baja)...)
     */
    public function getPropietarosNumPropiedadesAltaBaja() {
        $aDatos = array();
        $aNombs = parent::getNombresCompletos();
        foreach ($aNombs as $per => $nombre) {
            $aNum = $this->getNumPropiedadesAltaBaja($per);
            $aDatos[$per] = array($nombre, $aNum[0], $aNum[1], $aNum[2]);
        }
        return $aDatos;
    }
    
    /**
     * Obtiene los propietarios de un apartamento en una fecha determinada.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fecha Fecha a buscar en cualquier formato.
     * @param boolean $bBaja Si es true tambien obtiene la fecha de baja.
     * @return array de tipo array('codpers'=>array('nombre','baja')...) o array('codpers'=>'nombre'...)
     */
    public function getPropietariosEnFecha($apa, $fecha, $bBaja=TRUE) {
        $aProp = array();
        $date = $this->fechaIso_Base($fecha);
        $res = parent::ejecutarSQL("SELECT PR.CODPERS,CONCAT(APELLIDOS,' ',NOMBRE) AS NOM,PR.BAJA FROM PROPIETARIOS PR LEFT JOIN PERSONAS P ON P.CODPERS=PR.CODPERS WHERE IFNULL(PR.BAJA,'9999-99-99') >= '$date' AND PR.CODAPAR='$apa' ORDER BY IFNULL(PR.BAJA,'9999-99-99') DESC,PR.ORDEN");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $aProp[$aRow['CODPERS']] = ($bBaja) ? array($aRow['NOM'], $aRow['BAJA']) : $aRow['NOM'];
        }
        $res->closeCursor();
        return $aProp;
    }
    
    /**
     * Obtiene el propietario principal de un apartamento en una fecha determinada.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fecha Fecha a buscar en cualquier formato.
     * @param boolean $bBaja Si es true tambien obtiene la fecha de baja.
     * @return array de tipo array('codpers'=>array('nombre','baja')) o array('codpers'=>'nombre')
     */
    public function getPropietarioEnFecha($apa, $fecha, $bBaja=TRUE) {
        $aProp = array();
        $date = $this->fechaIso_Base($fecha);
        $res = parent::ejecutarSQL("SELECT PR.CODPERS, CONCAT(P.APELLIDOS,' ',P.NOMBRE) AS NOM,PR.BAJA FROM PROPIETARIOS PR LEFT JOIN PERSONAS P ON P.CODPERS=PR.CODPERS WHERE IFNULL(PR.BAJA,'9999-99-99') >= '$date' AND PR.CODAPAR='$apa' ORDER BY IFNULL(PR.BAJA,'9999-99-99') ASC,PR.ORDEN LIMIT 1;");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $aProp[$aRow['CODPERS']] = ($bBaja) ? array($aRow['NOM'], $aRow['BAJA']) : $aRow['NOM'];
        }
        $res->closeCursor();
        return $aProp;
    }

    /**
     * Obtiene el codigo del propietario principal de un apartamento en una fecha determinada.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fecha Fecha a buscar enc ualquier formato.
     * @return int Codigo de persona.
     */
    public function getPropietarioEnFechaCodigo($apa, $fecha) {
        $iPro = 0;
        $date = $this->fechaIso_Base($fecha);
        $res = parent::ejecutarSQL("SELECT CODPERS FROM PROPIETARIOS WHERE IFNULL(BAJA,'9999-99-99') >= '$date' AND CODAPAR='$apa' ORDER BY IFNULL(BAJA,'9999-99-99') ASC,ORDEN LIMIT 1");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $iPro = $aRow['CODPERS'];
        }
        $res->closeCursor();
        return $iPro;
    }
    
    /**
     * Obtiene los representantes de un apartamento en una Junta.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fecha Fecha en cualquier formato.
     * @return array Representantes en formato array('codpers'=>'nombre'...)
     */
    public function getRepresentantes($apa, $fecha) {
        $aRepr = array();
        $date = $this->fechaIso_Base($fecha);
        $res = parent::ejecutarSQL("SELECT CODPERS,CONCAT(APELLIDOS,' ',NOMBRE) AS NOM FROM PERSONAS WHERE CODPERS NOT IN (SELECT CODPERS FROM PROPIETARIOS WHERE IFNULL(BAJA,'9999-99-99') >= '$date' AND CODAPAR='$apa') ORDER BY APELLIDOS,NOMBRE,CODPERS");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $aRepr[$aRow['CODPERS']] = $aRow['NOM'];
        }
        $res->closeCursor();
        return $aRepr;
    }
    
    /**
     * Obtiene el ultimo representante de un apartamento en una Junta.
     * 
     * @param int $apa Codigo de apartamento.
     * @param date $fecha Fecha en cualquier formato.
     * @return int Codigo del representante.
     */
    public function getUltimoRepresentante($apa, $fecha) {
        $repr = "";
        $date = $this->fechaIso_Base($fecha);
        $res = parent::ejecutarSQL("SELECT CODPERS FROM ASISTENTES WHERE REPRESENTADO='S' AND FECHA<='$date' AND CODAPAR='$apa' ORDER BY FECHA DESC LIMIT 1");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $repr = $aRow['CODPERS'];
        }
        $res->closeCursor();
        return $repr;
    }
}
