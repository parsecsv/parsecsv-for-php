<pre>
<?php

require_once('../parsecsv.lib.php');

$csv = new parseCSV();

# if sorting is enabled, the whole CSV file
# will be processed and sorted and then rows
# are extracted based on offset and limit
$csv->sort_by = 'title';

$csv->limit = 3;
$csv->offset = 2;

$csv->auto('books.csv');

print_r($csv->data);

?>
</pre>