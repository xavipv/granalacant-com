<?php

/**
 * Clase Apartamento.
 */

/**
 * La clase Apartamento permite interactuar con los datos de un apartamento determinado.
 * Permite agregar y obtener los datos relacionados con un apartamento.
 *
 * @author xavi
 */
class Apartamento {
    
    //--- VARIABLES ----------------------------------------------------------//
    
    /**
     * Codigo del apartamento.
     * 
     * @var int Codigo de apartamento. 
     */
    private $codigo;
    
    /**
     * Numero del portal.
     * 
     * @var int Numero de portal. 
     */
    private $portal;
    
    /**
     * Numero de piso, planta baja o local.
     * 
     * @var string Numero de piso, B o L. 
     */
    private $piso;
    
    /**
     * Letra de la puerta.
     * 
     * @var string Letra. 
     */
    private $letra;
    
    /**
     * Numero de fase.
     * 
     * @var string Fase I o II. 
     */
    private $fase;
    
    /**
     * Tipo de apartamento.
     * 
     * @var string Tipo de apartamento. 
     */
    private $tipo;
    
    /**
     * Numero de finca segun consta en la escritura.
     * 
     * @var int Numero de finca. 
     */
    private $finca;
    
    /**
     * Metros cuadrados de la superficie del apartamento.
     * 
     * @var int Metros cuadrados. 
     */
    private $metros;
    
    /**
     * Metros cuadrados de la terraza.
     * 
     * @var int Metros cuadrados. 
     */
    private $terraza;
    
    /**
     * Porcentaje de coeficiente del apartamento respecto al 100% de la urbanizacion.
     * 
     * @var int Coeficiente de urbanizacion. 
     */
    private $coeficiente;
    
    /**
     * Porcentaje de coeficiente del apartamento respecto al 200% de la urbanizacion.
     * 
     * @var int Coeficiente de fase. 
     */
    private $coeficientefase;
    
    /**
     * Porcentaje de coeficiente del apartamento respecto al 100% del bloque en el que esta.
     * 
     * @var int Coeficiente de bloque. 
     */
    private $coeficientebloq;
    
    /**
     * Contiene los garajes pertenecientes al apartamento.
     * Es un array cuyas claves son los <b>codigos de garaje</b> y como datos tienen:
     * <ul>
     * <li>0 - Fecha de baja.</li>
     * <li>1 - Notas.</li>
     * </ul>
     * 
     * @var array del tipo array('codgar'=>array('baja','notas')...) 
     */
    private $garajes;
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Constructor de la clase.
     * 
     * @param int $cod Codigo del apartamento.
     */
    public function __construct($cod=0) {
        $this->cargarApartamento($cod);
    }
    
    //--- METODOS PRIVADOS ---------------------------------------------------//
    
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
     * Obtiene el siguiento codigo de apartamento disponible en la base de datos.
     * 
     * @return int Codigo de apartamento.
     */
    private function siguienteCodigo() {
        $cod  = 1;
        $rRes = $this->ejecutarSQL("SELECT MAX(CODAPAR)+1 AS NUM FROM APARTAMENTOS;");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $cod = $aRow['NUM'];
        }
        return $cod;
    }
    
    /**
     * Carga los datos del apartamento.
     * 
     * @param int $cod Codigo de apartamento.
     */
    private function cargarApartamento($cod) { 
        $this->cargarApartamentoOmision();
        if($cod) {
            $rRes = $this->ejecutarSQL("SELECT CODAPAR,PORTAL,PISO,LETRA,FASE,TIPO,FINCA,METROS,TERRAZA,COEFICIENTE,COEFICIENTEFASE,COEFICIENTEBLOQ FROM APARTAMENTOS WHERE CODAPAR='$cod'");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $this->codigo = $aRow['CODAPAR'];
                $this->portal = $aRow['PORTAL'];
                $this->piso = $aRow['PISO'];
                $this->letra = $aRow['LETRA'];
                $this->fase = $aRow['FASE'];
                $this->tipo = $aRow['TIPO'];
                $this->finca = $aRow['FINCA'];
                $this->metros = $aRow['METROS'];
                $this->terraza = $aRow['TERRAZA'];
                $this->coeficiente = $aRow['COEFICIENTE'];
                $this->coeficientefase = $aRow['COEFICIENTEFASE'];
                $this->coeficientebloq = $aRow['COEFICIENTEBLOQ'];
                $this->cargarGarajes($aRow['CODAPAR']);
            }
            $rRes->closeCursor(); 
        }
    }
    
    /**
     * Carga los datos por omision de un apartamento.
     */
    private function cargarApartamentoOmision() {
        $this->codigo = 0;
        $this->portal = 0;
        $this->piso = '';
        $this->letra = '';
        $this->fase = '';
        $this->tipo = '';
        $this->finca = 0;
        $this->metros = 0;
        $this->terraza = 0;
        $this->coeficiente = 0;
        $this->coeficientefase = 0;
        $this->coeficientebloq = 0;
        $this->garajes = array();
    }
    
    /**
     * Carga los datos de los garajes del apartamento.
     * 
     * @param int $cod Codigo de apartamento.
     */
    private function cargarGarajes($cod) {
        $aGar = array();
        if($cod) {
            $rRes = $this->ejecutarSQL("SELECT CODGAR,BAJA,NOTAS FROM GARAJES WHERE CODAPAR='$cod' AND BAJA IS NULL ORDER BY CODGAR");
            while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
                $aGar[$aRow['CODGAR']] = array($aRow['BAJA'], $this->decodificar($aRow['NOTAS']));
            }
            $rRes->closeCursor(); 
        }
        $this->garajes = $aGar;
    }
    
    /**
     * Obtiene el numero total de garajes que hay en la urbanizacion.
     * 
     * @return int Numero de garajes.
     */
    private function totalGarajes() {
        $iGar =0;
        $rRes = $this->ejecutarSQL("SELECT COUNT(*) AS TOT FROM GARAJES WHERE BAJA IS NULL");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $iGar = $aRow['TOT'];
        }
        $rRes->closeCursor(); 
        return $iGar;
    }
    
    /**
     * Graba los datos de los garajes del apartamento.
     * 
     * @return boolean Devuelve TRUE si se ha guardado bien o FALSE si ha fallado.
     */
    private function grabarGarajes() {
        $bOk = TRUE;
        $apa = $this->codigo;
        $gar = $this->garajes;
        
        foreach ($gar as $cod => $aGaraje) {
            $baja = ($aGaraje[0]) ? "'" . $aGaraje[0] . "'" : "NULL";
            $nota = $this->codificar($aGaraje[1]);
            $bOk  = $this->ejecutarSQL("REPLACE INTO GARAJES (CODGAR,CODAPAR,BAJA,NOTAS) VALUES ('$cod', '$apa', $baja, '$nota')");
        }
        return $bOk;
    }
    
    /**
     * Codifica caracteres especiales en entidades HTML.
     * 
     * @param string $txt Texto a codificar.
     * @return string Texto codificado.
     */
    private function codificar($txt) {
        return htmlspecialchars(trim($txt), ENT_QUOTES, 'UTF-8', FALSE);
    }
    
    /**
     * Decodifica entidades HTML a caracteres especiales. A las comillas les añade una barra.
     * 
     * @param string $txt Texto a decodificar.
     * @return string Texto decodificado.
     */
    private function decodificar($txt) {
        return htmlspecialchars_decode($txt, ENT_QUOTES);
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//
    
    /**
     * Obtiene el codigo del apartamento.
     * 
     * @return int Codigo del apartamento.
     */
    public function getCodigo() {
        return $this->codigo;
    }
    
    /**
     * Asigna el portal para el apartamento.
     * 
     * @param int $num Numero de portal.
     */
    public function setPortal($num) {
        $this->portal = $num;
    }
    
    /**
     * Obtiene el numero de portal.
     * 
     * @return int Numero de portal.
     */
    public function getPortal() {
        return $this->portal;
    }
    
    /**
     * Asigna el numero de piso, planta baja o local.
     * 
     * @param string $txt Numero de piso, B o L.
     */
    public function setPiso($txt) {
        $this->piso = $txt;
    }
    
    /**
     * Obtien el numero de piso, planta o local.
     * 
     * @return string Numero de piso, B o L.
     */
    public function getPiso() {
        return $this->piso;
    }
    
    /**
     * Asigna la letra del apartamento.
     * 
     * @param string $txt Letra.
     */
    public function setLetra($txt) {
        $this->letra = $txt;
    }
    
    /**
     * Obtiene la letra del apartamento.
     * 
     * @return stromg Letra.
     */
    public function getLetra() {
        return $this->letra;
    }
    
    /**
     * Obtiene el apartamento el formato texto.
     * Ejemplo: 'Portal 6-2B'
     * 
     * @return string Apartamento.
     */
    public function getApartamento() {
        return "Portal " . $this->getPortal() . "-" . $this->getPiso() . $this->getLetra(); 
    }
    
    /**
     * Asigna la fase a la que pertenece el apartamento.
     * 
     * @param string $txt Fase I o II.
     */
    public function setFase($txt) {
        $this->fase = $txt;
    }
    
    /**
     * Obtiene la fase a la que pertenece el apartamento.
     * 
     * @return string Fase I o II.
     */
    public function getFase() {
        return $this->fase;
    }
    
    /**
     * Asigna el tipo de apartamento.
     * 
     * @param string $txt Tipo A0, A1, A2, A3, A4, L1, L2, L3, L4.
     */
    public function setTipo($txt) {
        $this->tipo = $txt;
    }
    
    /**
     * Obtiene el tipo de apartamento.
     * 
     * @return string Tipo A0, A1, A2, A3, A4, L1, L2, L3, L4.
     */
    public function getTipo() {
        return $this->tipo;
    }
    
    /**
     * Asigna el numero de finca segun la escritura.
     * 
     * @param int $num Numero de finca.
     */
    public function setFinca($num) {
        $this->finca = $num;
    }
    
    /**
     * Obtiene el numero de finca segun la escritura.
     * 
     * @return int Numero de finca.
     */
    public function getFinca() {
        return $this->finca;
    }
    
    /**
     * Asigna los metros cuadrados del apartamento.
     * 
     * @param int $num Metros cuadrados.
     */
    public function setMetros($num) {
        $this->metros = $num;
    }
    
    /**
     * Obtiene los metros cuadrados del apartamento.
     * 
     * @return int Metros cuadrados.
     */
    public function getMetros() {
        return $this->metros;
    }
    
    /**
     * Asigna los metros cuadrados de la terraza.
     * 
     * @param int $num Metros cuadrados.
     */
    public function setTerraza($num) {
        $this->terraza = $num;
    }
    
    /**
     * Obtiene los metros cuadrados de la terraza.
     * 
     * @return int Metros cuadrados.
     */
    public function getTerraza() {
        return $this->terraza;
    }
    
    /**
     * Asigna el coeficiente de urbanizacion al 100%.
     * 
     * @param int $num Porcentaje del piso.
     */
    public function setCoeficiente($num) {
        $this->coeficiente = $num;
    }
    
    /**
     * Obtiene el coeficiente de urbanizacion al 100%.
     * 
     * @return int Porcentaje del piso.
     */
    public function getCoeficiente() {
        return $this->coeficiente;
    }
    
    /**
     * Asigna el coeficiente de fase al 200%.
     * 
     * @param int $num Porcentaje del piso.
     */
    public function setCoeficienteFase($num) {
        $this->coeficientefase = $num;
    }
    
    /**
     * Obtiene el coeficiente de fase al 200%.
     * 
     * @return int Porcentaje del piso.
     */
    public function getCoeficienteFase() {
        return $this->coeficientefase;
    }
    
    /**
     * Asigna el coeficiente del bloque.
     * 
     * @param int $num Porcentaje del piso.
     */
    public function setCoeficienteBloque($num) {
        $this->coeficientebloq = $num;
    }
    
    /**
     * Obtiene el coeficiente del bloque.
     * 
     * @return int Porcentaje del piso.
     */
    public function getCoeficienteBloque() {
        return $this->coeficientebloq;
    }
    
    /**
     * Asigna un garaje al apartamento.
     * 
     * @param int $cod Codigo del garaje.
     * @param date $baja Fecha de baja.
     * @param string $notas Notas.
     */
    public function setGaraje($cod, $baja=NULL, $notas='') {
        $this->garajes[$cod] = array($baja, $this->codificar($notas));
    }
    
    /**
     * Obtiene los datos de los garajes del apartamento.
     * Es un array cuyas claves son los <b>codigos de garaje</b> y como datos tienen:
     * <ul>
     * <li>0 - Fecha de baja.</li>
     * <li>1 - Notas.</li>
     * </ul>
     * 
     * @return array del tipo array('codgar'=>array('baja','notas')...)
     */
    public function getGarajes() {
        return $this->garajes;
    }
    
    /**
     * Obtiene los codigos de los garajes del apartamento.
     * 
     * @return array del tipo array('cod1', 'cod2'...)
     */
    public function getGarajesApartamento() {
        return array_keys($this->garajes);
    }
    
    /**
     * Obtiene el numero de garajes que tiene el apartamento.
     * 
     * @return int Numero de garajes.
     */
    public function getGarajesNumero() {
        return count($this->garajes);
    }
    
    /**
     * Obtiene el coeficiente de los garajes que tiene el apartamento.
     * 
     * @return int Coeficiente de los garajes.
     */
    public function getGarajesCoeficiente() {
        $num = $this->totalGarajes();       // Numero total de garajes.
        $nua = $this->getGarajesNumero();   // Numero de garajes del apartamento.
        $coe = ($num) ? 100/$num : 0;
        return $nua * $coe;
    }
    
    /**
     * Guarda los datos del apartamento actual.
     * 
     * @return boolean Devuelve TRUE si se ha guardado bien o FALSE si ha fallado.
     */
    public function grabar() {
        $bOk = FALSE;
        $cod = $this->codigo;
        $por = $this->portal;
        $pis = $this->piso;
        $let = $this->letra;
        $fas = $this->fase;
        $tip = $this->tipo;
        $fin = $this->finca;
        $met = $this->metros;
        $ter = $this->terraza;
        $cou = $this->coeficiente;
        $cof = $this->coeficientefase;
        $cob = $this->coeficientebloq;
        
        if($cod) {
            // UPDATE.
            $sql = "UPDATE APARTAMENTOS SET PORTAL='$por',PISO='$pis',LETRA='$let',FASE='$fas',TIPO='$tip',FINCA='$fin',METROS='$met',TERRAZA='$ter',COEFICIENTE='$cou',COEFICIENTEFASE='$cof',COEFICIENTEBLOQ='$cob' WHERE CODAPAR='$cod'";
        } else {
            // INSERT.
            $cod = $this->siguienteCodigo();
            $sql = "INSERT INTO APARTAMENTOS (CODAPAR,PORTAL,PISO,LETRA,FASE,TIPO,FINCA,METROS,TERRAZA,COEFICIENTE,COEFICIENTEFASE,COEFICIENTEBLOQ) VALUES ('$cod','$por','$pis','$let','$fas','$tip','$fin','$met','$ter','$cou','$cof','$cob')";
        }
        
        if ($this->ejecutarSQL($sql)) {
            // Si todo ha ido bien guarda los garajes del apartamento.
            $this->codigo = $cod;   // Por si el apartamento es nuevo.
            $bOk = $this->grabarGarajes();
        }
        return $bOk;
    }
}
