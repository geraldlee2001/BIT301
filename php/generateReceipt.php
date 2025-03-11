<?php

require_once('../vendor/autoload.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

include "../php/databaseConnection.php";
include "../php/tokenDecoding.php";

$sql = "SELECT
cci.cart_item_id,
cci.cart_id,
ci.createdAt AS cart_item_createdAt,
ci.updatedAt AS cart_item_updatedAt,
ci.quantity AS cart_item_quantity,
ci.productId,
p.ID AS product_id,
p.createdAt AS product_createdAt,
p.updatedAt AS product_updatedAt,
p.name AS product_name,
p.productCode,
p.description AS product_description,
p.price AS product_price,
p.amount AS product_amount,
p.imageUrl AS product_imageUrl,
p.merchantID
FROM
cartcartitem cci
JOIN
cartitem ci ON cci.cart_item_id = ci.id
JOIN
product p ON ci.productId = p.ID
WHERE
cci.cart_id =\"$_GET[id]\"";

$cartQuery = "SELECT * FROM cart WHERE id = \"$_GET[id]\"";
$data = $conn->query($sql);
$cartResult = $conn->query($cartQuery);
$cartData = $cartResult->fetch_assoc();

$customerQuery = "SELECT * FROM customer WHERE id = \"$decoded->customerId\"";
$customerResult = $conn->query($customerQuery);
$customerData = $customerResult->fetch_assoc();


use TCPDF as TCPDF;

class ReceiptPDF extends TCPDF
{
    // Add any customization methods or properties here if needed

    public function generateReceipt($customerName, $items, $totalAmount, $cartData)
    {
        $this->AddPage();

        // Customize your receipt layout here

        $this->SetFont('times', 'B', 16);
        $this->Cell(0, 10, 'Receipt', 0, 1, 'C');

        $this->SetFont('times', '', 12);
        $this->Cell(0, 10, 'Customer Name: ' . $customerName, 0, 1);
        $this->Cell(0, 10, 'Order code: ' . $cartData['code'], 0, 1);

        $this->Cell(0, 10, '', 0, 1); // Add spacing

        // Add a table for items
        $this->SetFillColor(200, 220, 255);
        $this->SetFont('times', 'B', 12);
        $this->Cell(100, 10, 'Item', 1, 0, 'C', 1);
        $this->Cell(30, 10, 'Quantity', 1, 0, 'C', 1);
        $this->Cell(40, 10, 'Total', 1, 1, 'C', 1);

        $this->SetFont('times', '', 12);
        foreach ($items as $item) {
            // Customize the appearance of each row
            $this->Cell(100, 10, $item['Product Name'], 1);
            $this->Cell(30, 10, $item['Quantity'], 1);
            $this->Cell(40, 10, $item['Total'], 1, 1, 'R'); // Align to the right
        }

        $this->Cell(0, 10, '', 0, 1); // Add spacing

        // Add total amount
        $this->SetFont('times', 'B', 12);
        $this->Cell(170, 10, 'Total Amount: RM ' . number_format($totalAmount, 2), 1, 1, 'R');

        // Output PDF to browser or save to a file
        $this->Output('receipt-' . $cartData['code'] . '.pdf', 'D');

        echo '<script>
        setTimeout(function() {
            window.close();
        }, 2000); // Close the tab after 2000 milliseconds (2 seconds)
    </script>';
    }
}
$items = array();
$totalAmount = 0;

// Example usage:\
if ($data->num_rows > 0) {
    while ($row = $data->fetch_assoc()) {
        $item = [
            'Product Name' => $row['product_name'],
            'Quantity' => $row['cart_item_quantity'],
            'Total' => $row['cart_item_quantity'] * $row['product_price']
        ];

        // Add the item to the $items array
        $items[] = $item;

        // Update the total amount
        $totalAmount += $item['Total'];
    }
}
$pdf = new ReceiptPDF();
$customerName = $customerData['fullName'];

$pdf->generateReceipt($customerName, $items, $totalAmount, $cartData);
