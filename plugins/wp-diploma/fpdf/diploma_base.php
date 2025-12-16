<?php
include('fpdf.php');
setlocale(LC_CTYPE, 'en_US');
$full_name = $_POST["firstname"] . " " . $_POST["lastname"];
// Instanciation of inherited class

$pdf = new FPDF('P','pt',array(840, 590));
$pdf->AliasNbPages();
$pdf->AddPage("L");
$pdf->Image('diploma.jpg', 0, 0,  841, 595);
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
$pdf->SetY(350);
$pdf->SetFont('Times','BI',40);
$pdf->cell(0,20, $_POST["course_name"] ,0, 1,"C");
$pdf->SetY(430);
$pdf->SetFont('Times','BI',20);
$pdf->cell(0,20,$_POST["date_text"] ,0, 1,"C");
$pdf->SetY(475);
$pdf->SetX(100);
$pdf->SetFont('Times','',17);
$pdf->cell(0,20,$_POST["lecturer_name"]);
$pdf->SetY(395);
$pdf->SetX(265);
$pdf->SetFont('Times','B',30);
$pdf->cell(0,20,$_POST["hour"]);

$pdf->SetY(475);
$pdf->SetX(500);
$pdf->SetFont('Times','',17);
$pdf->cell(0,20,"CPD-Vet Manager, Dr Erez Cohen");


$pdf->Output();