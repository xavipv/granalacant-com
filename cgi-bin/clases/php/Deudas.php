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
     * <li>6 - Suma de deudas.</li>
     * <li>7 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @var array del tipo array('fecha'=>array('portal'=>array('piso','letra','fase','ordinaria','extraordinaria','suma','fechaiso')...)...)
     */
    private $aDeudas;
    
    /**
     * Orden para mostrar las deudas.
     * <ul>
     * <li>0 - Fecha.</li>
     * <li>1 - Apartamento.</li>
     * <li>2 - Deuda.</li>
     * </ul>
     * 
     * @var int Orden 0, 1 o 2. 
     */
    private $orden;
    
    /**
     * Filtra por una fecha o, si esta vacio, las muestra todas.
     * 
     * @var date Fecha para filtrar en formato YYYY-MM-AA.
     */
    private $filtro;
    
    /**
     * Array con los siguientes valores:
     * <ul>
     * <li>0 - Fecha.</li>
     * <li>1 - Apartamento.</li>
     * <li>2 - Portal.</li>
     * <li>3 - Piso.</li>
     * <li>4 - Letra.</li>
     * <li>5 - Fase.</li>
     * <li>6 - Deuda ordinaria.</li>
     * <li>7 - Deuda extraordinaria.</li>
     * <li>8 - Suma de deudas.</li>
     * <li>9 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @var array del tipo array('fecha','apartamento','portal','piso','letra','fase','ordinaria','extraordinaria','suma','fechaiso') 
     */
    private $aFiltrado;
    
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
        $rRes = $this->ejecutarSQL("SELECT D.FECHA,D.CODAPAR,A.PORTAL,A.PISO,A.LETRA,A.FASE,D.ORDINARIA,D.EXTRAORDINARIA,D.ORDINARIA+D.EXTRAORDINARIA AS SUMA,DATE_FORMAT(D.FECHA,'%d-%m-%Y') AS FECHAISO FROM DEUDAS D LEFT JOIN APARTAMENTOS A ON A.CODAPAR=D.CODAPAR ORDER BY D.FECHA DESC,A.CODAPAR");
        while($aRow = $rRes->fetch(PDO::FETCH_ASSOC)) {
            $this->aDeudas[$aRow['FECHA']][$aRow['CODAPAR']] = array($aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA'],$aRow['FASE'],$aRow['ORDINARIA'],$aRow['EXTRAORDINARIA'],$aRow['SUMA'],$aRow['FECHAISO']);
            $this->aFiltrado[] = array($aRow['FECHA'],$aRow['CODAPAR'],$aRow['PORTAL'],$aRow['PISO'],$aRow['LETRA'],$aRow['FASE'],$aRow['ORDINARIA'],$aRow['EXTRAORDINARIA'],$aRow['SUMA'],$aRow['FECHAISO']);
        }
        $rRes->closeCursor();
    }
    
    /**
     * Carga los datos por omision de los apartamentos.
     */
    private function cargarOmision() {
        $this->aDeudas = array();
        $this->orden = 0;
        $this->filtro = '';
        $this->aFiltrado = array();
    }
    
    /**
     * Carga las deudas filtradas.
     * Si no hay fecha se cargan todas las deudas, pero si hay fecha solo carga las de esa fecha.
     * 
     * @param date $fecha Fecha en cualquier formato, puede estar vacia.
     */
    private function filtroDeudaFecha($fecha='') {
        $this->aFiltrado = array();
        if(!$fecha) {
            // Todas las fechas.
            $this->filtro = '';
            $aDeudas = $this->aDeudas;
            foreach ($aDeudas as $fec => $aDeuda) {
                $this->fitroApartamentos($fec, $aDeuda);
            }
        } else {
            // Solo una fecha.
            $date = $this->fechaIso_Base($fecha);
            $this->filtro = $date;
            $aDeuda = $this->aDeudas[$date];
            $this->fitroApartamentos($fecha, $aDeuda);
        }
        $this->ordenarFiltradas();
    }
    
    /**
     * Carga los datos del filtro segun la fecha y datos indicados.
     * 
     * @param date $fecha Fecha en formato YYYY-MM-DD.
     * @param array $aDatos Datos de la deuda.
     */
    private function fitroApartamentos($fecha, $aDatos) {
        foreach ($aDatos as $apa => $aDeuda) {
            $this->aFiltrado[] = array($fecha, $apa, $aDeuda[0], $aDeuda[1], $aDeuda[2], $aDeuda[3], $aDeuda[4], $aDeuda[5], $aDeuda[6], $aDeuda[7]);
        }
    }
    
    /**
     * Comprueba los valores permitidos para el orden.
     * Si el valor no es correcto le asignara 0.
     * 
     * @param int $orden Orden a comprobar.
     * @return int Valor del orden comprobado.
     */
    private function arreglarOrden($orden) {
        return ($orden != 0 && $orden != 1 && $orden != 2) ? 0 : $orden;
    }
    
    /**
     * Ordena la deuda filtrada por el orden indicado.
     */
    private function ordenarFiltradas() {
        $orden = $this->orden;
        $aDeu = $this->aFiltrado;
        foreach ($aDeu as $num => $aDatos) {
            $aFec[$num] = $aDatos[0];
            $aApa[$num] = $aDatos[1];
            $aSum[$num] = $aDatos[8];
        }
        switch ($orden) {
            case 1 : array_multisort($aApa, SORT_ASC, $aFec, SORT_DESC, $aDeu); break;  // Apartamento, fecha.
            case 2 : array_multisort($aFec, SORT_DESC, $aSum, SORT_DESC, $aDeu); break; // Fecha, deuda.
            default: array_multisort($aFec, SORT_DESC, $aApa, SORT_ASC, $aDeu); break;  // Fecha, apartamento.
        }
        $this->aFiltrado = $aDeu;
    }
    
    /**
     * Obtiene la fecha mayor y la fecha menor de todas las deudas.
     * 
     * @return array del tipo array(0=>array('fmayorDB', 'fmayorISO), 1=>array('fmenorDB', 'fmenorISO')).
     */
    private function getFechaMayorMenor() {
        $aResult = array();
        $aFechas = $this->getFechas();
        $aFecKey = array_keys($aFechas);
        $fMayor = "0000-00-00";
        $fMenor = "9999-99-99";
        foreach ($aFecKey as $fDB) {
            $fMayor = ($fDB > $fMayor) ? $fDB : $fMayor;
            $fMenor = ($fDB < $fMenor) ? $fDB : $fMenor;
        }
        $aResult[0] = array($fMayor, $aFechas[$fMayor]);
        $aResult[1] = array($fMenor, $aFechas[$fMenor]);
        
        return $aResult;
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
     * Obtiene la deudas existentes en un array cuyas claves son las <b>fechas</b> y los <b>codigos de apartamento</b>, y los valores:
     * <ul>
     * <li>0 - Portal.</li>
     * <li>1 - Piso.</li>
     * <li>2 - Letra.</li>
     * <li>3 - Fase.</li>
     * <li>4 - Deuda ordinaria.</li>
     * <li>5 - Deuda extraordinaria.</li>
     * <li>6 - Suma de deudas.</li>
     * <li>7 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * El orden que se devuelve es por fecha y apartamento.
     * 
     * @return array del tipo array('fecha'=>array('portal'=>array('piso','letra','fase','ordinaria','extraordinaria','suma','fechaiso')...)...)
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
     * <li>6 - Suma de deudas.</li>
     * <li>7 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @return array del tipo array('codapar'=>array('portal','piso','letra','fase','ordinaria','extraordinaria','suma','fechaiso')...)
     */
    public function getDeudaFecha($fecha) {
        $date = $this->fechaIso_Base($fecha);
        return $this->aDeudas[$date];
    }
    
    /**
     * Obtiene la deuda ordinaria, la extraordinaria y la suma de ambas de un apartamento en una fecha determinada.
     * 
     * @param date $fecha Fecha en cualquier formato.
     * @param int $apar Codigo del apartamento.
     * @return array con las deudas del tipo array('ordinaria','extraordinaria','suma')
     */
    public function getDeudaFechaApartamento($fecha, $apar) {
        $ord = 0;
        $ext = 0;
        $aDeudas = $this->getDeudaFecha($fecha);
        foreach ($aDeudas as $apa => $aDeuda) {
            if ($apar == $apa) {
                $ord = $aDeuda[4];
                $ext = $aDeuda[5];
                $sum = $aDeuda[6];
            }
        }
        return array($ord, $ext, $sum);
    }
    
    /**
     * Obtiene un array con las sumas de las deudas ordinarias, las deudas extraordinarias y la de ambas.
     * Si se indica una fecha solo se sumaran las deudas de esa fecha, sino se sumaran todas las deudas.
     * 
     * @param date $fecha Fecha en cualquier formato (opcional).
     * @return array con la suma de las deudas, array(suma ordinarias, suma extraordinarias, suma de ambas)
     */
    public function getSumas($fecha='') {
        $sumao = 0;
        $sumae = 0;
        $sumas = 0;
        $date = ($fecha) ? $this->fechaIso_Base($fecha) : '';
        
        foreach ($this->aDeudas as $fec => $aDeudas) {
            foreach ($aDeudas as $aDatos) {
                if ($date) {
                    $sumao += ($fec == $date) ? $aDatos[4] : 0;
                    $sumae += ($fec == $date) ? $aDatos[5] : 0;
                    $sumas += ($fec == $date) ? $aDatos[6] : 0;
                } else {
                    $sumao += $aDatos[4];
                    $sumae += $aDatos[5];
                    $sumas += $aDatos[6];
                }
            }
        }
        return array($sumao, $sumae, $sumas);
    }
    
    /**
     * Obtiene un array cuyas son las fechas y los valores las sumas de las deudas ordinarias, extraordinarias y las totales.
     * 
     * @return array del tipo array('fecha'=>array(suma ordinarias, suma extraordinarias, suma de ambas)...)
     */
    public function getSumasFechas() {
        $aSumas = array();
        foreach ($this->aDeudas as $fec => $aDeudas) {
            $sumao = 0;
            $sumae = 0;
            $sumas = 0;
            foreach ($aDeudas as $aDatos) {
                $sumao += $aDatos[4];
                $sumae += $aDatos[5];
                $sumas += $aDatos[6];
            }
            $aSumas[$fec] = array($sumao, $sumae, $sumas);
        }
        return $aSumas;
    }
    
    /**
     * Obtiene las fechas de todas las deudas.
     * 
     * @return array del tipo array('date'=>'fecha'...)
     */
    public function getFechas() {
        $aFechas = array();
        foreach (array_keys($this->aDeudas) as $date) {
            $aFechas[$date] = $this->fechaBase_Iso($date);
        }
        return $aFechas;
    }
    
    /**
     * Obtiene la fecha mayor de todas las fechas de las deudas.
     * 
     * @return array del tipo array('fechaDB', 'fechaISO')
     */
    public function getFechaMayor() {
        $aFechas = $this->getFechaMayorMenor();
        return $aFechas[0];
    }
    
    /**
     * Obtiene la fecha menor de todas las fechas de las deudas.
     * 
     * @return array del tipo array('fechaDB', 'fechaISO')
     */
    public function getFechaMenor() {
        $aFechas = $this->getFechaMayorMenor();
        return $aFechas[1];
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
    
    /**
     * Asigna un filtro de fecha o si se deja vacio las carga todas.
     * 
     * @param date $fecha Fecha en cualquier formato.
     */
    public function setFiltro($fecha='') {
        $this->filtroDeudaFecha($fecha);
    }
    
    /**
     * Obtiene el filtro aplicado actualmente.
     * 
     * @return date Fecha o vacio.
     */
    public function getFiltro() {
        return $this->filtro;
    }
    
    /**
     * Asigna el orden de las deudas filtradas.
     * <ul>
     * <li>0 - Fecha.</li>
     * <li>1 - Apartamento.</li>
     * <li>2 - Deuda.</li>
     * </ul>
     * 
     * @param int $orden Orden a aplicar.
     */
    public function setOrden($orden) {
        $ord = $this->arreglarOrden($orden);
        if ($this->orden != $ord) {
            $this->orden = $ord;
            $this->ordenarFiltradas();
        }
    }
    
    /**
     * Obtiene el orden aplicado actualmente.
     * <ul>
     * <li>0 - Fecha.</li>
     * <li>1 - Apartamento.</li>
     * <li>2 - Deuda.</li>
     * </ul>
     * 
     * @return int Orden aplicado.
     */
    public function getOrden() {
        return $this->orden;
    }
    
    /**
     * Obtiene los datos de las deudas filtradas y ordenadas.
     * Devuelve un array con los siguientes valores:
     * <ul>
     * <li>0 - Fecha.</li>
     * <li>1 - Apartamento.</li>
     * <li>2 - Portal.</li>
     * <li>3 - Piso.</li>
     * <li>4 - Letra.</li>
     * <li>5 - Fase.</li>
     * <li>6 - Deuda ordinaria.</li>
     * <li>7 - Deuda extraordinaria.</li>
     * <li>8 - Suma de deudas.</li>
     * <li>9 - Fecha DD-MM-AAAA.</li>
     * </ul>
     * 
     * @return array del tipo array('fecha','apartamento','portal','piso','letra','fase','ordinaria','extraordinaria','suma','fechaiso')
     */
    public function getFiltradas() {
        return $this->aFiltrado;
    }
    
}
