<?php

/**
 * Clase Transformar.
 */

/**
 * La clase Transformar se usa para convertir los textos de las tablas de la base de datos
 * a entidades HTML y viceversa. Solo se codifican las entidades basicas.
 *
 * @author xavi
 */
class Transformar {
    
    //--- INSTANCIACION ------------------------------------------------------//
    
    /**
     * Contructor de la clase.
     */
    public function __construct() {
        
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
     * Codifica un texto con las entidades HTML basicas.
     * 
     * @param string $txt Texto a codificar.
     * @return string Texto codificado.
     */
    private function codificar($txt) {
        // return htmlentities($txt, ENT_QUOTES, 'UTF-8', FALSE);
        return htmlspecialchars($txt, ENT_QUOTES, 'UTF-8', FALSE);
    }
    
    /**
     * Decodifica un texto con entidades HTML basicas.
     * 
     * @param string $txt Texto a decodificar.
     * @return string Texto decodificado.
     */
    private function decodificar($txt) {
        // return addslashes(html_entity_decode($txt, ENT_QUOTES, 'UTF-8'));
        return addslashes(html_entity_decode($txt, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Ejecuta la transformacion en la base de datos.
     * 
     * @param string $sql Sentencia SQL a ejecutar.
     * @return boolean Devuelve TRUE si todo ha ido bien y FALSE si ha fallado algo.
     */
    private function setTranformacion($sql) {
        $res = $this->ejecutarSQL($sql);
        $res->closeCursor();
        return $res;
    }
    
    //--- METODOS PUBLICOS ---------------------------------------------------//

    /**
     * Transforma el texto codificando o decodificando las entidades que contenga.
     * 
     * @param string $tabla Nombre de la tabla.
     * @param array $aClaves Claves primarias en forma array('clave1','clave2'...)
     * @param array $aCampos Campos a transformar en forma array('campo1','campo2'...)
     * @param boolean $bCodif Si es TRUE codifica los textos, si es FALSE los decodifica.
     * @return boolean Devuelve TRUE si todo ha ido bien y FALSE si ha fallado algo.
     */
    public function transformar($tabla, $aClaves, $aCampos, $bCodif=FALSE) {
        $bOK = TRUE;
        $sClaves = implode(', ', $aClaves);
        $sCampos = implode(', ', $aCampos); 
        $res = $this->ejecutarSQL("SELECT $sClaves, $sCampos FROM $tabla");
        while($aRow = $res->fetch(PDO::FETCH_ASSOC)) {
            $set = "";
            foreach ($aCampos as $campo) {
                $txt  = ($bCodif) ? $this->codificar($aRow[$campo]) : $this->decodificar($aRow[$campo]);
                $set .= ($set) ? ", $campo='$txt'" : "SET $campo='$txt'";
            }
            $whe = "";
            foreach ($aClaves as $clave) {
                $txt = $aRow[$clave];
                $whe .= ($whe) ? " AND $clave='$txt'" : " WHERE $clave='$txt'"; 
            }
            $bOK = (!$this->setTranformacion("UPDATE $tabla $set $whe")) ? FALSE : $bOK;
        }
        $res->closeCursor(); 
        return $bOK;
    }
}
