<pre>
<?php

require_once('../parsecsv.lib.php');

$csv = new parseCSV();

$csv->conditions = array( 'title' => array('*paperback*', '*hardcover*') );

$csv->auto('books.csv');

print_r($csv->data);

?>
</pre>