<?php 
$file = __DIR__.'/users_crest.csv';
if (file_exists($file)) {
    unlink($file);
    echo "File deleted successfully";
} else {
    echo "File not found";
}
?>