<?php


include('fpdf.php');
//setlocale(LC_CTYPE, 'en_US');


if(!isset($_POST["multi"])){

    $full_name = 'Dr. ' . $_POST["firstname"] . " " . $_POST["lastname"];
    // Instanciation of inherited class

    try {
        $pdf = new FPDF('P','pt',array(840, 590));
    } catch (\Throwable $th) {
        var_dump( $th );
    }
    // die();
    $pdf->AliasNbPages();
    $pdf->AddPage("L");

    if(isset($_POST["bg_extra"]) && $_POST["bg_extra"] != ""){
        $pdf->Image('diploma_footer_text.jpg', 0, 0,  841, 595);
    }
    else{
        $pdf->Image('diploma.jpg', 0, 0,  841, 595);
        if( isset($_POST['color']) && $_POST['color'] != '' ){
            list($r, $g, $b) = sscanf($_POST['color'], "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            $pdf->Rect(0, 0, 840, 590, 'F');
            $pdf->Image('diploma_tran.png', 0, 0,  841, 595);
        }
    }

    $pdf->SetY(265);
    $pdf->SetFont('Times','',36);
    $pdf->cell(0,20,$full_name ,0, 1,"C");
    $pdf->SetY(308);
    $pdf->SetX(395);
    $pdf->SetFont('Times','',20);
    $pdf->cell(0,20,$_POST["type_bottom"]);
    $pdf->SetY(148);
    $pdf->SetX(459);
    $pdf->SetFont('Times','',36);
    $pdf->cell(0,20,$_POST["type"]);

    if($_POST["bg_extra"]){
        $pdf->SetY(340);
    }
    else{
        $pdf->SetY(350);
    }
    $pdf->SetFont('Times','BI',40);
    $pdf->cell(0,20, $_POST["course_name"] ,0, 1,"C");
    if(isset($_POST["bg_extra"]) && $_POST["bg_extra"] != ""){
        $pdf->SetY(370);
        $pdf->SetFont('Times','BI',20);
        $pdf->cell(0,20,"RACE approved",0, 1,"C");
    }
    $pdf->SetY(430);
    if($_POST["date_text"] != ""){
    $pdf->SetFont('Times','BI',20);
    $pdf->cell(0,20,$_POST["date_text"] ,0, 1,"C");
    }
    $pdf->SetY(475);
    $pdf->SetX(100);
    $pdf->SetFont('Times','',17);
    $pdf->cell(0,20,$_POST["lecturer_name"]);

    $pdf->SetY(455);
    $pdf->SetX(100);
    $pdf->SetFont('Times','',17);
    $pdf->cell(0,20,$_POST["lecturer_name_2"]);


    if(isset($_POST["bg_extra"]) && $_POST["bg_extra"] != ""){
        $pdf->SetY(405);
    $pdf->SetX(410);	
    }
    else{
        $pdf->SetY(395);
    $pdf->SetX(265);	
    }

    $pdf->SetFont('Times','B',30);
    $pdf->cell(0,20,$_POST["hour"]);

    $pdf->SetY(475);
    $pdf->SetX(450);
    $pdf->SetFont('Times','',17);
    $pdf->cell(0,20,"CPD-Vet Manager, Dr Erez Cohen DVM, DABVP");
    $pdf->Output();

}else{	

$h = fopen($_FILES['file_csv']['tmp_name'], "r");
$pdf = new FPDF('P','pt',array(840, 590));

while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {

    if( $_POST["multi_t"] == 'file' ){
        $pdf = new FPDF('P','pt',array(840, 590));
    }

    $full_name = 'Dr. ' . $data[0] . " " . $data[1];
    // Instanciation of inherited class
    $file_name = $data[0] . "_" . $data[1];

    $pdf->AliasNbPages();
    $pdf->AddPage("L");
    if($data[7] == "yes"){
        $pdf->Image('diploma_footer_text.jpg', 0, 0,  841, 595);
    }
    else{
        $pdf->Image('diploma.jpg', 0, 0,  841, 595);
        if( isset($_POST['color']) && $_POST['color'] != '' ){
            list($r, $g, $b) = sscanf($_POST['color'], "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            $pdf->Rect(0, 0, 840, 590, 'F');
            $pdf->Image('diploma_tran.png', 0, 0,  841, 595);
        }
    }

    $pdf->SetY(265);
    $pdf->SetFont('Times','',36);
    $pdf->cell(0,20,$full_name ,0, 1,"C");
    $pdf->SetY(308);
    $pdf->SetX(395);
    $pdf->SetFont('Times','',20);
    $pdf->cell(0,20,$data[6]);
    $pdf->SetY(148);
    $pdf->SetX(459);
    $pdf->SetFont('Times','',36);
    $pdf->cell(0,20,$data[5]);

    if($data[7] == "yes"){
        $pdf->SetY(340);
    }
    else{
        $pdf->SetY(350);
    }
    $pdf->SetFont('Times','BI',40);
    $pdf->cell(0,20, $data[2] ,0, 1,"C");
    if($data[7] == "yes"){
        $pdf->SetY(370);
        $pdf->SetFont('Times','BI',20);
        $pdf->cell(0,20,"RACE approved",0, 1,"C");
    }
    $pdf->SetY(430);
    if($data[8] != ""){
    $pdf->SetFont('Times','BI',20);
    $pdf->cell(0,20,$data[8] ,0, 1,"C");
    }


    $lecturer_name = explode(' | ', $data[4]);

    $pdf->SetY(475);
    $pdf->SetX(100);
    $pdf->SetFont('Times','',17);
    $pdf->cell(0,20,$lecturer_name[0]);

    if( $lecturer_name > 1 ){
        $pdf->SetY(455);
        $pdf->SetX(100);
        $pdf->SetFont('Times','',17);
        $pdf->cell(0,20,$lecturer_name[1]);
    }

    if($data[7] == "yes"){
    $pdf->SetY(405);
    $pdf->SetX(410);	
    }
    else{
    $pdf->SetY(395);
    $pdf->SetX(265);	
    }

    $pdf->SetFont('Times','B',30);
    $pdf->cell(0,20,$data[3]);

    $pdf->SetY(475);
    $pdf->SetX(450);
    $pdf->SetFont('Times','',17);
    $pdf->cell(0,20,"CPD-Vet Manager, Dr Erez Cohen DVM, DABVP");
    //$myfile = fopen("diplomas/" . $file_name . ".pdf", "w") or die("Unable to open file!");
    
    if( $_POST["multi_t"] == 'file' ){
        $pdf->Output( "diplomas/" . $file_name . ".pdf","F");
        echo "The file of " .$full_name . " is: <a target='_blank' href='https://iscpdvet.com/wp-content/plugins/wp-diploma/fpdf/diplomas/" . $file_name . ".pdf'>הורד קובץ</a><br>";
    }
}	

    if( $_POST["multi_t"] == 'files' ){
        $pdf->Output();
    }
}

