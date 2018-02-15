<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Тест</title>
</head>
<body>

<?php
	include 'menu.php';
	if (isset($_GET['id'])) { //Задан номер теста
		$id = htmlspecialchars($_GET['id']);
		$tests = array_slice(scandir('uploads/'), 2);
		$countOfTests = count($tests);			
		if ((is_numeric($id))&&($id>=1)&&($id<=$countOfTests)) { // 0 <= номер теста <= равно количества тестов
			$filename = $tests[$id-1];
			$jsonData = file_get_contents("uploads/$filename");
			$test = json_decode($jsonData);

			if (isset($_GET['ready'])) { //Если форма отправлена
				foreach ($test as $qId => $question) {
					echo '<p><b>'.htmlspecialchars(strip_tags($question->text)).'</b></p>';
					$isRight = true;
					foreach ($question->options as $optionId => $option) {
						if ((isset($_GET[$qId.'_'.$optionId])) xor ($option[1]==1))
							$isRight = false;
					}
					if ($isRight) echo '<p style="color: green;">Верно</p>';
					else echo '<p style="color: red;">Не верно</p>';
				}
			}
			else { //Отрисовка формы
				echo "<form method='GET' action='test.php'>";
				foreach ($test as $qId => $question) {
					echo '<p><b>'.htmlspecialchars(strip_tags($question->text)).'</b></p>';
					foreach ($question->options as $optionId => $option) {
						echo "<input type='checkbox' name='".htmlspecialchars(strip_tags($qId))." ".htmlspecialchars(strip_tags($optionId))."' value='1'>".htmlspecialchars(strip_tags($option[0]))."<br>";
					}
				}
				echo "<input type='hidden' name='id' value='$id'>";
				echo "<br><input type='submit' name='ready' value='Проверить'></form>";
			}

		}
		else
			die('Неверный параметр id');
	}
	else {
		die('Не задан параметр id, указывающий номер теста');
	}

?>
		
</body>
</html>