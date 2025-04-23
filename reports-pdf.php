<?php
require_once './includes/functions.php';
requireLogin();
requireRole('admin');

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if TCPDF is installed
if (!file_exists('./TCPDF-main/tcpdf.php')) {
    die("Error: TCPDF library not found. Please make sure TCPDF is installed in the 'tcpdf' directory.");
}

// Include TCPDF configuration
require_once('config/tcpdf_config.php');

// Direct include of TCPDF class file
require_once('./TCPDF-main/tcpdf.php');

$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

try {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Inventory Management System');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Inventory Report');
    $pdf->SetSubject('Inventory Report');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'Inventory Management System', 'Generated on ' . date('Y-m-d H:i:s'));

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page
    $pdf->AddPage();

    if ($type == 'sales') {
        $start_date = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : '';
        
        if ($start_date && $end_date) {
            $sales_data = getSalesReport($start_date, $end_date);
            
            // Title
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, 'Period: ' . $start_date . ' to ' . $end_date, 0, 1, 'C');
            $pdf->Ln(10);
            
            if (!empty($sales_data)) {
                // Table header
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
                $pdf->Cell(60, 10, 'Product', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Total', 1, 0, 'C');
                $pdf->Cell(50, 10, 'Date', 1, 1, 'C');
                
                // Table data
                $pdf->SetFont('helvetica', '', 10);
                $total_sales = 0;
                
                foreach ($sales_data as $sale) {
                    $pdf->Cell(20, 10, $sale['id'], 1, 0, 'C');
                    $pdf->Cell(60, 10, $sale['name'], 1, 0, 'L');
                    $pdf->Cell(30, 10, $sale['quantity'], 1, 0, 'C');
                    $pdf->Cell(30, 10, '$' . number_format($sale['total_price'], 2), 1, 0, 'R');
                    $pdf->Cell(50, 10, $sale['sale_date'], 1, 1, 'C');
                    
                    $total_sales += $sale['total_price'];
                }
                
                // Total row
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(110, 10, 'Total Sales', 1, 0, 'R');
                $pdf->Cell(30, 10, '$' . number_format($total_sales, 2), 1, 0, 'R');
                $pdf->Cell(50, 10, '', 1, 1, 'C');
            } else {
                $pdf->Cell(0, 10, 'No sales data found for the selected period.', 0, 1, 'C');
            }
        }
    } elseif ($type == 'low_stock') {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
        $low_stock_data = getLowStockProducts($threshold);
        
        // Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Low Stock Report', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Products with stock below ' . $threshold . ' units', 0, 1, 'C');
        $pdf->Ln(10);
        
        if (!empty($low_stock_data)) {
            // Table header
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
            $pdf->Cell(60, 10, 'Name', 1, 0, 'C');
            $pdf->Cell(30, 10, 'SKU', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Price', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Value', 1, 1, 'C');
            
            // Table data
            $pdf->SetFont('helvetica', '', 10);
            
            foreach ($low_stock_data as $product) {
                $pdf->Cell(20, 10, $product['id'], 1, 0, 'C');
                $pdf->Cell(60, 10, $product['name'], 1, 0, 'L');
                $pdf->Cell(30, 10, $product['sku'], 1, 0, 'C');
                $pdf->Cell(30, 10, '$' . number_format($product['price'], 2), 1, 0, 'R');
                $pdf->Cell(30, 10, $product['quantity'], 1, 0, 'C');
                $pdf->Cell(30, 10, '$' . number_format($product['price'] * $product['quantity'], 2), 1, 1, 'R');
            }
        } else {
            $pdf->Cell(0, 10, 'No products below the stock threshold.', 0, 1, 'C');
        }
    }

    // Output PDF
    $pdf->Output('inventory_report.pdf', 'I');
    
} catch (Exception $e) {
    // Display error message
    echo '<div style="color: red; padding: 20px; border: 1px solid red; margin: 20px; font-family: Arial, sans-serif;">';
    echo '<h2>Error Generating PDF</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p>File: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>';
    echo '<h3>Troubleshooting:</h3>';
    echo '<ol>';
    echo '<li>Make sure TCPDF is properly installed in the "tcpdf" directory</li>';
    echo '<li>Check that the tcpdf_config.php file exists and is properly configured</li>';
    echo '<li>Ensure PHP has write permissions to the temporary directory</li>';
    echo '<li>Check that all required PHP extensions are enabled (gd, mbstring, etc.)</li>';
    echo '</ol>';
    echo '<p><a href="reports.php">Return to Reports</a></p>';
    echo '</div>';
}
?>

