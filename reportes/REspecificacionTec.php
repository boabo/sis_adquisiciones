<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class REspecificacionTec extends ReportePDF
{
    var $datos;

    function setDatos($datos) {
        $this->datos = $datos;

    }

    function Header() {
        $tbl = '
                
                <font size="8">
                    <table width="100%" style="text-align: center;" cellspacing="0" cellpadding="1" border="1">
                        
                            <tr>
                                <th style="width: 25%; color: #444444;" rowspan="2">
                                    &nbsp;<br><img  style="width: 150px;" src="'.dirname(__FILE__) . '/../../pxp/lib' . $_SESSION['_DIR_LOGO'].'" alt="Logo">
                                </th>		
                                <th style="width: 45%; " rowspan="2"><h1>ESPECIFICACIONES</h1><h1>TÉCNICAS</h1></th>
                                <th style="width: 30%;" colspan="2" valign="middle" ><br><b>R-AJ-01</b> <br> <b>Rev.14-Feb/2012<br></b></th>              
                            </tr>
                        
                            <tr>
                                <th style="width: 10%;"  valign="middle" ><b>Fecha:</b></th>
                                <td style="width: 20%;  color: #444444;"  valign="middle" >'.date('d-m-Y').'</td>
                                
                            </tr>
                            
                       
                    </table>
                </font>
                ';
        $this->ln(5);

        $this->writeHTML($tbl, true, false, false, false, '');
        $this->Ln(20);
    }


    function generarReporte(){
        $this->AddPage();
        $this->SetMargins(20, 35, 15);
        //$this->SetHeaderMargin(10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


        $this->SetFont('helvetica','',10);


        $this->SetFont('helvetica','B',11);
        $this->Ln(15);
        $this->writeHTML('<p>1. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OBJETO DE CONTRATACIÓN</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['justificacion'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>2. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ESPECIFICACIONES TÉCNICAS</p>',true);
        $this->SetFont('helvetica','',10);
        $this->Ln(5);
        $tbl = '
                <table>
            <tr>
            <td style="width: 10%"></td>
            <td style="width: 80%">
            <table cellspacing="0" cellpadding="1" border="1" align="center">
                <tr>
                    <th align="center" >Item</th>
                    <th align="center" >Detalle</th>
                    <th align="center" >Unidad</th>
                    <th align="center" >Cantidad</th>
                </tr>
                <tr>
                    <td align="center" >
                    '.$this->datos[0]['desc_ingas'].'    
                    </td>
                    <td align="center" >
                    '.$this->datos[0]['descripcion'].'      
                    </td>
                    <td align="center" >
                    '.$this->datos[0]['precio_unitario'].'      
                    </td>
                    <td align="center" >
                    '.$this->datos[0]['cantidad'].'      
                    </td>
                 </tr>
                 <!--<tr>
                    <td>xx</td>
                    <td>xx</td>
                    <td>xx</td>
                    <td>xx</td>
                 </tr>-->
            </table>
            </td>
            <td style="width:10%;"></td>
            </tr>
            </table>
        ';
        $this->writeHTML($tbl,true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>3. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;VALIDEZ DE LA OFERTA</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['validez_oferta'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>4. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GARANTIAS</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['garantias'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>5. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LUGAR DE ENTREGA DEL BIEN</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['lugar_entrega'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>6. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PLAZO DE ENTREGA</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['dias_plazo_entrega'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>7. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MULTAS</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['multas'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>8. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FORMA DE PAGO</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['forma_pago'].'</p>',true);

        if($this->getPageHeight()-$this->GetY()<=74){
            $this->setY($this->getPageHeight());
        }

        $tbl = '<table>
            <tr>
            <td style="width: 5%"></td>
            <td style="width: 90%">
            <table cellspacing="0" cellpadding="1" border="1">
                <tr>
                    <td style="font-family: Calibri; font-size: 9px;"> <b>Elaborado por:</b> '.$this->datos[0]['p_nombre_sol'].'</td>
                    <td style="font-family: Calibri; font-size: 9px;"> <b>Aprobado por:</b> '.$this->datos[0]['p_nombre_jefe'].'</td>
                </tr>
                <tr>
                    <td align="center" >
                        <br><br>
                        <img  style="width: 95px; height: 95px;" src="'.$this->generarImagen($this->datos[0]['p_nombre_sol'], $this->datos[0]['p_cargo_sol']).'" alt="Logo">
                        <br>Solicitante
                    </td>
                    <td align="center" >
                        <br><br>
                        <img  style="width: 95px; height: 95px;" src="'.$this->generarImagen($this->datos[0]['p_nombre_jefe'],$this->datos[0]['p_cargo_jefe']).'" alt="Logo">
                        <br>Vo.Bo.
                    </td>
                 </tr>
                 <!--<tr>
                    <td>Firma Electrónica</td>
                    <td>Firma Electrónica</td>
                 </tr>-->
            </table>
            </td>
            <td style="width:5%;"></td>
            </tr>
            </table>

        ';
        $this->Ln(5);
        $this->writeHTML($tbl, true, false, false, false, '');


    }

    function generarImagen($nom, $nac){
        $cadena_qr = 'Nombre: '.$nom. "\n" . 'Nacionalidad: '.$nac ;
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nac . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nac . ".png"; //$this->objParam->getParametro('nombre_archivo')

        return $url_archivo;
    }

    function obtenerFechaEnLetra($fecha){
        $dia= $this->conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return $dia.', '.$num.' de '.$mes.' del '.$anno;
    }

    function conocerDiaSemanaFecha($fecha) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia = $dias[date('w', strtotime($fecha))];
        return $dia;
    }

    public function Footer() {

    }


}
?>