<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RInformeSolicitud extends ReportePDF
{
    var $datos;
    var $datos2;

    function setDatos($datos) {
        $this->datos = $datos;

    }

    function Header() {

        $tbl = '
                
                <font size="8">
                    <table width="100%" style="text-align: center;" cellspacing="0" cellpadding="1" border="1">
                        
                            <tr>
                                <th style="width: 25%; color: #444444;" rowspan="3">
                                    &nbsp;<br><img  style="width: 150px;" src="'.dirname(__FILE__) . '/../../pxp/lib' . $_SESSION['_DIR_LOGO'].'" alt="Logo">
                                </th>		
                                <th style="width: 45%;" rowspan="3"><br>  <h1>INFORME </h1></th>
                                <th style="width: 30%;" colspan="2"><b>R-GG-14</b> <br> <b>Rev.01-Sep/2012<br></b></th>
                               
                            </tr>
                            <tr>
                                <th style="width: 10%; "><b>CITE:</b></th>
                                <td style="width: 20%;  color: #444444;"><b>'.$this->datos[0]['p_nro_cite'].'</b></td>
                            </tr>
                            <tr>
                                <th style="width: 10%;"  ><b>Fecha:</b></th>
                                <td style="width: 20%;  color: #444444;"  >'.date('d-m-Y').'</td>
                                
                            </tr>
                            
                       
                    </table>
                </font>
                ';

        $height = 25;
        $this->ln(5);
        /*$this->MultiCell(40, $height, '', 1, 'C', 0, '', '');
        //$this->SetFontSize(15);
        $this->SetFont('helvetica', 'B', 15);
        $this->MultiCell(105, $height, 'INFORME', 1, 'C', 0, '', '');
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(0, $height, 'CITE' , 1, 'C', 0, '', '');
        $this->Image(dirname(__FILE__) . '/../../pxp/lib' . $_SESSION['_DIR_LOGO'], 17, 15, 36);*/
        $this->writeHTML($tbl, true, false, false, false, '');
        $this->Ln(20);
    }


    function generarReporte(){
        $this->AddPage();
        $this->SetMargins(20, 35, 15);
        //$this->SetHeaderMargin(10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


        $this->SetFont('helvetica','',10);
        $this->Ln(10);

        $this->Cell(150, 2,'        A                        :      '.$this->datos[0]['p_nombre_jefe'], 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('helvetica','B',10);
        $this->Cell(150, 2,'                                          '.$this->datos[0]['p_cargo_jefe'] , 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('helvetica','',10);
        $this->Cell(150, 2,'' , 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell(150, 2,'        De                      :      '.$this->datos[0]['p_nombre_sol'], 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('helvetica','B',10);
        $this->Cell(150, 2,'                                          '.$this->datos[0]['p_cargo_sol'] , 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell(150, 2,'' , 0, 2, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell(150, 2,'        Ref.                    :      '.'INFORME DE JUSTIFICACIÓN PARA COMPRA', 0, 2, 'L', false, '', 0, false, 'T', 'C');

        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black');
        $this->Line(17, 80, 200, 80, $style);

        $this->Ln(15);
        $this->writeHTML('<p>I. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ANTECEDENTES</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['antecendentes'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>II. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NECESIDAD DE CONTRATACIÓN</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['necesidad_contra'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>III. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BENEFICIOS PARA REALIZAR LA CONTRATACIÓN</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['beneficios_contra'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>IV. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RESULTADOS ESPERADOS</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['resultados'].'</p>',true);

        $this->SetFont('helvetica','B',11);
        $this->Ln(5);
        $this->writeHTML('<p>V. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONCLUSIONES Y RECOMENDACIONES</p>',true);
        $this->SetFont('helvetica','',10);
        $this->writeHTML('<p style="text-align: justify; ">'.$this->datos[0]['conclusiones_r'].'</p>',true);

        //$this->Text(150,$this->GetY(),'texto');
        //var_dump('y: '.$this->getPageHeight().'dimesion'.$this->GetY());exit;

        if($this->getPageHeight()-$this->GetY()<=74){
            $this->setY($this->getPageHeight());
        }

        $tbl = '<table>
            <tr>
            <td style="width: 5%"></td>
            <td style="width: 90%">
            <table cellspacing="0" cellpadding="1" border="1">
                <tr>
                    <td style="font-family: Calibri; font-size: 9px;"><b> Elaborado por:</b> '.$this->datos[0]['p_nombre_sol'].'</td>
                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b> '.$this->datos[0]['p_nombre_jefe'].'</td>
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

    function generarImagen($nom, $nac)
    {
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