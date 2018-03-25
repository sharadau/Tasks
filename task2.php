<?php
echo "String parsing with PHP regular expression<br>";
$inputString1 = 'Key1:value1 key2:value2 key3:value3';
$inputString ='TestKey1:TestValue1 TestKey2:"a value" TestKey3:"a value with \""';
preg_match_all('/[a-zA-Z0-9]*:((["]+[a-zA-Z0-9 ]*[[\\\]?["]+)|([a-zA-Z0-9]*))/', $inputString, $match);
//preg_match_all('/[a-zA-Z0-9]*:[a-zA-Z0-9]*/', $inputString, $match1);
//print_r($match[0]);

foreach($match[0] as $pair)
{
	$each1 = explode(":",$pair);
	$outputArray[$each1[0]] = $each1[1];
}
print_r("<br>");
		print_r($outputArray);

?>
