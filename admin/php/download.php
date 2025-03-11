<?php
// Get the file path from the query parameter
$filePath = isset($_GET['file']) ? $_GET['file'] : null;

// Check if the file path is valid
if ($filePath && is_file($filePath)) {
  // Set the appropriate headers for file download
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
  header('Content-Length: ' . filesize($filePath));

  // Flush the output buffer and send the file in chunks
  ob_clean();
  flush();

  // Read the file and output its content using binary mode
  $file = fopen($filePath, 'rb');
  while (!feof($file)) {
    echo fread($file, 8192); // Read in 8KB chunks
  }
  fclose($file);
  exit;
} else {
  // File not found, handle the error (e.g., redirect to an error page)
  header('HTTP/1.1 404 Not Found');
  echo 'File not found.';
  exit;
}
