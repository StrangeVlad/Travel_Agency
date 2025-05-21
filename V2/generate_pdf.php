<?php
// Include the FPDF library
require('fpdf/fpdf.php');

// Get data from the form (hidden fields sent via POST)
$name = $_POST['name'];
$email = $_POST['email'];
$destination = $_POST['destination'];
$delivery_date = $_POST['delivery_date'];
$expiration_date = $_POST['expiration_date'];

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set title and font to Arial
$pdf->SetFont('Arial', 'B', 16); // Changed to Arial

// Title
$pdf->Cell(200, 10, 'Travel Package Confirmation', 0, 1, 'C');

// Line break
$pdf->Ln(10);

// Set font for the content
$pdf->SetFont('Arial', '', 12); // Changed to Arial

// Add name
$pdf->Cell(100, 10, 'Name: ' . $name, 0, 1);

// Add email
$pdf->Cell(100, 10, 'Email: ' . $email, 0, 1);

// Add destination
$pdf->Cell(100, 10, 'Destination: ' . $destination, 0, 1);

// Add delivery date
$pdf->Cell(100, 10, 'Delivery Date: ' . $delivery_date, 0, 1);

// Add expiration date
$pdf->Cell(100, 10, 'Expiration Date: ' . $expiration_date, 0, 1);

// Line break
$pdf->Ln(10);

// Add a thank you note
$pdf->Cell(200, 10, 'Thank you for choosing us!', 0, 1, 'C');

// Output the PDF
$pdf->Output();
?>
