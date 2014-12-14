<?php
$link = mssql_connect('91.203.194.185', 'sa', 'JokE5001031');

if (!$link)
    die('Unable to connect!');

if (!mssql_select_db('monitoring', $link))
    die('Unable to select database!');

$result = mssql_query('SELECT * FROM ping');

while ($row = mssql_fetch_array($result)) {
    var_dump($row);
}

mssql_free_result($result);