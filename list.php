<?php
	$tests = array_slice(scandir('uploads/'), 2);
	echo '<h1>Доступные тесты</h1>';
	$i = 1; 
	foreach ($tests as $filename) {
		echo "<p>$i. <a href='test.php?id=$i'>$filename</a></p>";
		$i++;	
	}
?>