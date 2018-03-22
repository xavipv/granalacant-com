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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @var array del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')...)
     */
    private $aDatosApartamentos;
    
    /**
     *Contiene los datos de los apartamentos tras aplicar los filtros.
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @var array del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')...)
     */
    private $aFiltrados;
    
    /**
     * Filtro para el portal inicial.
     * 
     * @var int Portal inicial (1). 
     */
    private $filtroPortalIni;
    
    /**
     * Filtro para el portal final.
     * 
     * @var int Portal final (26). 
     */
    private $filtroPortalFin;
    
    /**
     * Filtro para el tipo de apartamento.
     * 
     * @var string Tipo de apartamento. 
     */
    private $filtroTipo;
    
    /**
     * Filtro para apartamentos con terraza.
     * 
     * @var string S/N. 
     */
    private $filtroConTerraza;
    
    /**
     * Filtro para apartamentos con garaje.
     * 
     * @var string S/N. 
     */
    private $filtroConGaraje;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     */
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
        $this->cargarApartamentosOmision();
        $rRes = $this->ejecutarSQL("SELECT A.CODAPAR,A.PORTAL,A.PISO,A.LETRA,A.FASE,A.TIPO,A.FINCA,A.METROS,A.TERRAZA,A.COEFICIENTE,A.COEFICIENTEFASE,A.COEFICIENTEBLOQ, (SELECT COUNT(*) FROM GARAJES G WHERE G.CODAPAR=A.CODAPAR) AS GARAJES FROM APARTAMENTOS A ORDER BY A.CODAPAR");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $this->aDatosApartamentos[$aRow['CODAPAR']] = array($aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA'],$aRow['FASE'],$aRow['TIPO'],$aRow['FINCA'],$aRow['METROS'],$aRow['TERRAZA'],$aRow['COEFICIENTE'],$aRow['COEFICIENTEFASE'],$aRow['COEFICIENTEBLOQ'],$aRow['GARAJES']);
        }
        $rRes->closeCursor(); 
        $this->aFiltrados = $this->aDatosApartamentos;
    }
    
    /**
     * Carga los datos por omision de los apartamentos.
     */
    private function cargarApartamentosOmision() {
        $this->aDatosApartamentos = array();
        $this->aFiltrados = array();
        $this->filtroPortalIni = '';
        $this->filtroPortalFin = '';
        $this->filtroTipo;
        $this->filtroConTerraza = '';
        $this->filtroConGaraje = '';
    }
    
    /**
     * Filtar los apartamentos por los filtros indicados.
     */
    private function filtrarApartamentos() {
        $aFil = array();
        $aApa = $this->getApartamentos();
        $fIni = $this->filtroPortalIni;
        $fFin = $this->filtroPortalFin;
        $fTip = $this->filtroTipo;
        $fTer = $this->filtroConTerraza;
        $fGar = $this->filtroConGaraje;
        
        foreach ($aApa as $apa => $aApartamento) {
            // array('0 portal','1 piso','2 letra','3 fase','4 tipo','5 finca','6 metros','7 terraza','8 coef.urb','9 coef.fase','10 coef.blo')
            
            $bOK = ($aApartamento[0] < $fIni || $aApartamento[0] > $fFin) ? FALSE : TRUE;
            if ($fTip) {
                $bOK = ($aApartamento[4] != $fTip) ? FALSE : $bOK;
            }
            if ($fTer == 'S') {
                $bOK = (intval($aApartamento[7]) > 0) ? $bOK : FALSE;
            }
            if ($fGar == 'S') {
                $bOK = ($this->getNumeroGarajes($apa) > 0) ? $bOK : FALSE;
            }
            if ($bOK) {
                $aFil[$apa] = $aApartamento;
            }
        }
        $this->aFiltrados = $aFil;
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @param array $aApar Apartamentos a excluir o a incluir.
     * @param boolean $bExc Si es TRUE se excluyen y si es FALSE se incluyen.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')...)
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
    
    /**
     * Arregla el valor para un filtro permitiendo solo S, N o vacio.
     * 
     * @param string $fil Cadena a filtrar.
     * @return string Cadena filtrada.
     */
    private function arreglarFiltro($fil) {
        if ($fil == 'S' || $fil == 's') {
            $fil = 'S';
        } elseif ($fil == 'N' || $fil == 'n') {
            $fil = 'N';
        } else {
            $fil = '';
        }
        return $fil;
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @return array del tipo array('codigo'=>array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq,numgar)...)
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @param array $aInc Apartamentos a incluir.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')...)
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @param array $aInc Apartamentos a excluir.
     * @param boolean $bNom Si es TRUE devuelve los nombres 'Portal 1-3A' y si es false el array con los datos de los apartamentos.
     * @return array Con los apartamentos del tipo array('codapar'=>array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')...)
     */
    public function getApartamentosExcluyendo($aInc, $bNom=FALSE) {
        return $this->getApartamentosIncExc($aInc, TRUE, $bNom);
    }
     
    /**
     * Obtiene los datos de un apartamento.
     * 
     * @param int $cod Codigo del apartamento.
     * @return array Con los apartamentos del tipo array('portal','piso','letra','fase','tipo','finca','metros','terraza','coef.urb','coef.fase','coef.blo','numgar')
     */
    public function getApartamento($cod) {
        return isset($this->aDatosApartamentos[$cod]) ? $this->aDatosApartamentos[$cod] : array(0,0,'','','',0,0,0,0,0,0,0);
    }
    
    /**
     * Obtiene los codigos de todos los apartamentos.
     * 
     * @return array del tipo array('cod1','cod2'...)
     */
    public function getCodigos() {
        return array_keys($this->aDatosApartamentos);
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
    
    public function getNombreCompleto($apa, $bPortal=TRUE) {
        $aApa = $this->aDatosApartamentos;
        $sPor = ($bPortal) ? "Portal " : "";
        $aDat = $aApa[$apa];
        return $sPor . $aDat[0] . "-" . $aDat[1] . $aDat[2];
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
     * Obtiene el numero de apartamentos que tiene un portal.
     * 
     * @param int $por Numero de portal. Si es 0 se devolvera el total de apartamentos.
     * @return int Numero de apartamentos.
     */
    public function getNumApartamentosPortal($por=0) {
        //array('codigo'=>array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq,numgar)...)
        $aApar = $this->getApartamentos();
        $num = 0;
        if (!$por) {
            $num = count($aApar);
        } else {
            foreach ($aApar as $aDatos) {
                $num += ($aDatos[0] == $por) ? 1 : 0;
            }
        }
        return $num;
    }
    
    /**
     * Obtiene el numero de apartamentos que tiene una fase.
     * 
     * @param string $fas Numero de fase. Si es 0 o vacio se devolvera el total de apartamentos.
     * @return int Numero de apartamentos.
     */
    public function getNumApartamentosFase($fas='') {
        //array('codigo'=>array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq,numgar)...)
        $aApar = $this->getApartamentos();
        $num = 0;
        if (!$fas) {
            $num = count($aApar);
        } else {
            $fas = strtoupper($fas);
            foreach ($aApar as $aDatos) {
                $num += ($aDatos[3] == $fas) ? 1 : 0;
            }
        }
        return $num;
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
    
    /**
     * Obtiene el numero de garajes de todos los apartamentos.
     * 
     * @return array del tipo array('cod1'=>num1,'cod2'=>num2...)
     */
    public function getGarajes() {
        return $this->getDatos(11);
    }
    
    /**
     * Obtiene el numero de garajes de un apartamento.
     * 
     * @param int $apa Codigo de apartamento.
     * @return int Numero de garajes.
     */
    public function getNumeroGarajes($apa) {
        $aGar = $this->getDatos(11);
        return $aGar[$apa];
    }
    
    /**
     * Indica si un apartamento tiene garajes o no.
     * 
     * @param int $apa Codigo de apartamento.
     * @return boolean Devuelve TRUE si tiene garajes o FALSE si no tiene.
     */
    public function conGarajes($apa) {
        return ($this->getNumeroGarajes($apa) > 0) ? TRUE : FALSE;
    }
    
    /**
     * Carga el filtro del portal inicial.
     * 
     * @param int $num Numero de portal.
     */
    public function setFiltroPortalIni($num=1) {
        $this->filtroPortalIni = $num;
        $this->filtrarApartamentos();
    }
    
    /**
     * Carga el filtro del portal final.
     * 
     * @param int $num Numero de portal.
     */
    public function setFiltroPortalFin($num=26) {
        $this->filtroPortalFin = $num;
        $this->filtrarApartamentos();
    }
    
    /**
     * Carga el filtro del tipo de apartamento.
     * 
     * @param string $fil Tipo de apartamento.
     */
    public function setFiltroTipo($fil='') {
        $this->filtroTipo = $fil;
    }
    
    /**
     * Carga el filtro de apartamentos con garajes.
     * 
     * @param string $fil S/N.
     */
    public function setFiltroGarajes($fil='') {
        $this->filtroConGaraje = $this->arreglarFiltro($fil);
        $this->filtrarApartamentos();
    }
    
    /**
     * Carga el filtro de apartamentos con terraza.
     * 
     * @param string $fil S/N.
     */
    public function setFiltroTerrazas($fil='') {
        $this->filtroConTerraza = $this->arreglarFiltro($fil);
        $this->filtrarApartamentos();
    }
    
    /**
     * Obtiene un array con los datos de los apartamentos filtrados.
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
     * <li>11 - Numero de garajes.</li>
     * </ul>
     * 
     * @return array del tipo array('codigo'=>array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq,numgar)...)
     */
    public function getFiltrados() {
        return $this->aFiltrados;
    }
}
