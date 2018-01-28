<?php

/**
 * Clase Apartamentos.
 */

/**
 * La clase Apartamentos guarda el contenido basico de todos los apartamentos.
 * Permite obtener datos de los apartamentos de la urbanizacion y realizar busquedas.
 *
 * @author xavi
 */
class Apartamentos {
    
    //--- VARIABLES ----------------------------------------------------------//
    //,$aRow['FINCA'],$aRow['METROS'],$aRow['TERRAZA'],$aRow['COEFICIENTE'],$aRow['COEFICIENTEFASE'],$aRow['COEFICIENTEBLOQ']);
    
    /**
     * Contiene los datos de todos los apartamentos.
     * Es un array cuyo indice es el <b>codigo del apartamento</b> y como datos tiene:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Tipo.</li>
     * <li>5 - Finca.</li>
     * <li>6 - Superficie.</li>
     * <li>7 - Terraza.</li>
     * <li>8 - Coeficiente 100%.</li>
     * <li>9 - Coeficiente fase 200%.</li>
     * <li>10 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @var array del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo')...)
     */
    private $aDatosApartamentos;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    function __construct() {
        $this->cargarApartamentos();
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
     * Carga los datos de los apartamentos.
     */
    private function cargarApartamentos() {
        $rRes = $this->ejecutarSQL("SELECT CODAPAR,PORTAL,PISO,LETRA,FASE,TIPO,FINCA,METROS,TERRAZA,COEFICIENTE,COEFICIENTEFASE,COEFICIENTEBLOQ FROM APARTAMENTOS ORDER BY CODAPAR");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $this->aDatosApartamentos[$aRow['CODAPAR']] = array($aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA'],$aRow['FASE'],$aRow['TIPO'],$aRow['FINCA'],$aRow['METROS'],$aRow['TERRAZA'],$aRow['COEFICIENTE'],$aRow['COEFICIENTEFASE'],$aRow['COEFICIENTEBLOQ']);
        }
        $rRes->closeCursor(); 
    }
    
    /**
     * Busca datos de los apartamentos.
     * 
     * @param string $buscar Cadena a buscar.
     * @return array Apartamentos encontrados del tipo array('codpar'=>array('portal','piso','letra')...)
     */
    private function buscarApartamentos($buscar) {
        $aBus = array();
        if($buscar) {
            $rRes = $this->ejecutarSQL("SELECT CODAPAR,PORTAL,PISO,LETRA FROM APARTAMENTOS WHERE STRCMP(CODAPAR,'$buscar')=0 OR PORTAL LIKE '%$buscar%' OR PISO LIKE '%$buscar%' OR LETRA LIKE '%$buscar%' OR CONCAT(PORTAL,'-',PISO,LETRA) LIKE '%$buscar%' ORDER BY CODAPAR");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $aBus[$aRow['CODAPAR']] = array($aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA']);
            }
            $rRes->closeCursor(); 
        }
        return $aBus;
    }
    
    /**
     * Obtiene el dato indicado de los datos del apartamento.
     * 
     * @param int $num Numero de dato a recuperar.
     * @return array con los datos indicados del tipo array('codapar'=>'dato'...)
     */
    private function getDatos($num) {
        $aDat = array();
        $aPer = $this->aDatosApartamentos;
        foreach ($aPer as $cod => $aDatos) {
            $aDat[$cod] = $aDatos[$num];
        }
        return $aDat;
    }
    
    /**
     * Obtiene un dato de los apartamentos, pero sin que haya repetidos.
     * 
     * @param array $aDat Datos a buscar distintos del tipo array('codapar'=>'dato'...)
     * @return array con los datos distintos del tipo array('dato1','dato2'...)
     */
    private function getDistintos($aDat) {
        $aNew = array();
        $sVal = "";
        foreach ($aDat as $valor) {
            if($sVal != $valor) {
                $aNew[] = $valor;
            }
            $sVal = $valor;
        }
        return $aNew;
    }
    
    /**
     * Obtiene una lista de apartamentos excluyendo o incluyendo los indicados.
     * Es un array cuyo indice es el <b>codigo del apartamento</b> y como datos tiene:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Tipo.</li>
     * <li>5 - Finca.</li>
     * <li>6 - Superficie.</li>
     * <li>7 - Terraza.</li>
     * <li>8 - Coeficiente 100%.</li>
     * <li>9 - Coeficiente fase 200%.</li>
     * <li>10 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @param array $aApar Apartamentos a excluir o a incluir.
     * @param boolean $bExc Si es TRUE se excluyen y si es FALSE se incluyen.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo')...)
     */
    private function getApartamentosIncExc($aApar, $bExc=TRUE, $bNom=FALSE) {
        $aDat = array();
        $aApa = $this->aDatosApartamentos;
        foreach ($aApa as $apa => $aApartamento) {
            if($bExc && !array_key_exists($apa, $aApar)) {
                $aDat[$apa] = (!$bNom) ? $aApartamento : "Portal " . $aApartamento[0] . "-" . $aApartamento[1] . $aApartamento[2];
            } elseif (!$bExc && array_key_exists($apa, $aApar)) {
                $aDat[$apa] = (!$bNom) ? $aApartamento : "Portal " . $aApartamento[0] . "-" . $aApartamento[1] . $aApartamento[2];
            }
        }
        return $aDat;
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene un array con los datos de los apartamentos.
     * Es un array cuyo indice es el <b>codigo del apartamento</b> y como datos tiene:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Tipo.</li>
     * <li>5 - Finca.</li>
     * <li>6 - Superficie.</li>
     * <li>7 - Terraza.</li>
     * <li>8 - Coeficiente 100%.</li>
     * <li>9 - Coeficiente fase 200%.</li>
     * <li>10 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @return array del tipo array('codigo'=>array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq)...)
     */
    public function getApartamentos() {
        return $this->aDatosApartamentos;
    }
    
    /**
     * Obtiene los apartamentos indicados.
     * Es un array cuyo indice es el <b>codigo del apartamento</b> y como datos tiene:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Tipo.</li>
     * <li>5 - Finca.</li>
     * <li>6 - Superficie.</li>
     * <li>7 - Terraza.</li>
     * <li>8 - Coeficiente 100%.</li>
     * <li>9 - Coeficiente fase 200%.</li>
     * <li>10 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @param array $aInc Apartamentos a incluir.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo')...)
     */
    public function getApartamentosIncluyendo($aInc, $bNom=FALSE) {
        return $this->getApartamentosIncExc($aInc, FALSE, $bNom);
    }
    
    /**
     * Obtiene los apartamentos excluyendo los indicados..
     * Es un array cuyo indice es el <b>codigo del apartamento</b> y como datos tiene:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Tipo.</li>
     * <li>5 - Finca.</li>
     * <li>6 - Superficie.</li>
     * <li>7 - Terraza.</li>
     * <li>8 - Coeficiente 100%.</li>
     * <li>9 - Coeficiente fase 200%.</li>
     * <li>10 - Coeficiente bloque.</li>
     * </ul>
     * 
     * @param array $aInc Apartamentos a excluir.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo')...)
     */
    public function getApartamentosExcluyendo($aInc, $bNom=FALSE) {
        return $this->getApartamentosIncExc($aInc, TRUE, $bNom);
    }
    
    /**
     * Obtiene los datos de un apartamento.
     * 
     * @param int $cod Codigo del apartamento.
     * @return array Con los apartamentos del tipo array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo')
     */
    public function getApartamento($cod) {
        return isset($this->aDatosApartamentos[$cod]) ? $this->aDatosApartamentos[$cod] : array(0,0,'','','',0,0,0,0,0,0);
    }
    
    /**
     * Obtiene los datos de todos los portales.
     * 
     * @return array del tipo array('cod1'=>'portal1','cod2'=>'portal1','cod3'=>'portal2'...)
     */
    public function getPortales() {
        return $this->getDatos(0);
    }
    /**
     * Obtiene los distintos portales.
     * 
     * @return array del tipo array('portal1','portal2','portal3'...)
     */
    public function getPortalesDistintos() {
        return $this->getDistintos($this->getPortales());
    }
    
    /**
     * Obtiene los datos de todos los pisos.
     * 
     * @return array del tipo array('cod1'=>'piso1','cod2'=>'piso1','cod3'=>'piso2'...)
     */
    public function getPisos() {
        return $this->getDatos(1);
    }
    
    /**
     * Obtiene los pisos distintos..
     * 
     * @return array del tipo array('piso1','piso2','piso3'...)
     */
    public function getPisosDistintos() {
        $aDat = $this->getDistintos($this->getPisos());
        asort($aDat,SORT_STRING);
        return $aDat;
    }
    
    /**
     * Obtiene los datos de todas las letras.
     * 
     * @return array del tipo array('cod1'=>'A','cod2'=>'A','cod3'=>'B'...)
     */
    public function getLetras() {
        return $this->getDatos(2);
    }
    
    /**
     * Obtiene las distintas letras de las puertas.
     * 
     * @return array del tipo array('A','B','C'...)
     */
    public function getLetrasDistintas() {
        $aDat = $this->getDistintos($this->getLetras());
        asort($aDat,SORT_NUMERIC);
        return $aDat;
    }
    
    /**
     * Obtiene los datos de las fases a las que pertenencen los pisos.
     * 
     * @return array del tipo array('cod1'=>'I','cod2'=>'I','cod3'=>'II'...)
     */
    public function getFases() {
        return $this->getDatos(3);
    }
    
    /**
     * Obtiene las distintas fases.
     * 
     * @return array del tipo array('I','II')
     */
    public function getFasesDistintas() {
        $aDat = $this->getDistintos($this->getFases());
        asort($aDat);
        return $aDat;
    }
    
    /**
     * Obtiene los datos de todos los tipos de piso.
     * 
     * @return array del tipo array('cod1'=>'A1','cod2'=>'A1','cod3'=>'A2'...)
     */
    public function getTipos() {
        return $this->getDatos(4);
    }
    
    /**
     * Obtiene los distintos tipos de piso.
     * 
     * @return array del tipo array('A1','A2',A3'...)
     */
    public function getTiposDistintos() {
        $aDat = $this->getDistintos($this->getTipos());
        asort($aDat);
        return $aDat;
    }
    
    /**
     * Obtiene el nombre completo de los apartamentos.
     * Ejemplo: 'Portal 6-1B' o '6-1B'
     * 
     * @param boolean $bPortal Si es TRUE se pone la palabra 'Portal' si es FALSE no se pone.
     * @return string Nombre completo del apartamento.
     */
    public function getNombresCompletos($bPortal=TRUE) {
        $aDat = array();
        $aApa = $this->aDatosApartamentos;
        $sPor = ($bPortal) ? "Portal " : "";
        foreach ($aApa as $cod => $aDatos) {
            $aDat[$cod]=$sPor . $aDatos[0] . "-" . $aDatos[1] . $aDatos[2];
        }
        return $aDat;
    }
    
    /**
     * Obtiene una lista con los portales.
     * 
     * @return array del tipo array('portal1','portal2'...)
     */
    public function getPortalesLista() {
        $aIni = array();
        $aPor = $this->getPortales();
        $sIni = "";
        foreach ($aPor as $portal) {
            if($portal != $sIni) {
                $aIni[] = $portal;
            }
            $sIni = $portal;
        }
        return $aIni;
    }
    
    /**
     * Busca apartamentos.
     * 
     * @param string $busqueda Dato a buscar.
     * @return array con los apartamentos encontrados del tipo array('codpar'=>array('portal','piso','letra')...)
     */
    public function buscar($busqueda) {
        return $this->buscarApartamentos($busqueda);
    }
    
    /**
     * Obtiene los coeficientes de los apartamentos.
     * 
     * @return array del tipo array('cod1'=>array('coefic','coefase','coebloq')...)
     */
    public function getCoeficientes() {
        $aCoef = array();
        foreach ($this->aDatosApartamentos as $apa => $aApar) {
            $aCoef[$apa] = array($aApar[8], $aApar[9], $aApar[10]);
        }
        return $aCoef;
    }
}
