<pre>
<?php

require_once('../parsecsv.lib.php');

$csv = new parseCSV();

$csv->auto('books.csv');

print_r($csv);

?>
</pre>