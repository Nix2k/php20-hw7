<?php
	if (isset($_GET['id'])) { //Задан номер теста
		$id = htmlspecialchars($_GET['id']);
		$tests = array_slice(scandir('uploads/'), 2);
		$countOfTests = count($tests);			
		if ((is_numeric($id))&&($id>=1)&&($id<=$countOfTests)) { // 0 <= номер теста <= равно количества тестов
			$filename = $tests[$id-1];
			$jsonData = file_get_contents("uploads/$filename");
			$test = json_decode($jsonData);
			$title = array_shift($test);

			if (isset($_GET['ready'])) { //Если форма отправлена
				if (!isset($_GET['username'])) {
					http_response_code(400);
					die('Не задано имя тестируемого');
				}
				$username = htmlspecialchars(strip_tags($_GET['username']));
				$scores = 0; //количество верных ответов
				$numOfQuestions = 0; //количество вопросов
				/*echo "<h1>Результаты прохождения теста: $title->title</h1>";
				foreach ($test as $qId => $question) {
					echo '<p><b>'.htmlspecialchars(strip_tags($question->text)).'</b></p>';
					$isRight = true;
					foreach ($question->options as $optionId => $option) {
						if ((isset($_GET[$qId.'_'.$optionId])) xor ($option[1]==1))
							$isRight = false;
					}
					if ($isRight) {
						echo '<p style="color: green;">Верно</p>';
						$scores++;
					}
					else {
						echo '<p style="color: red;">Не верно</p>';
					}
					$numOfQuestions++;
				}
				$mark = round($scores*100/$numOfQuestions);
				echo "<p><b>Количество набранных баллов $mark из 100</b></p>";*/
				//Генерация файла с сертификатом
				$imgBlank = imagecreatefrompng(__DIR__.'/img/blank.png');
				$fontFile = __DIR__.'/fonts/MarckScript.ttf';
				$textColor = imagecolorallocate($imgBlank, 0, 0, 0);
				imagettftext($imgBlank,36,0,400,400,$textColor,$fontFile,"$username");
				imagettftext($imgBlank,32,0,500,500,$textColor,$fontFile,"выполнил тест");
				imagettftext($imgBlank,36,0,400,600,$textColor,$fontFile,"$title->title");
				imagettftext($imgBlank,36,0,250,700,$textColor,$fontFile,"Результат тестирования: $mark из 100");
				header('Content-Type: image/png');
				header('Content-Disposition: attachment; filename="certificate.png"');
				imagepng($imgBlank);
				imagedestroy($imgBlank);
				exit();
			}
		}
		else {
			http_response_code(404);
			die('Неверный номер теста');
		}
	}
	else {
		http_response_code(400);
		die('Не задан параметр id, указывающий номер теста');
	}

?>
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
	//Отрисовка формы
	echo "<h1>$title->title</h1>";
	echo "<form method='GET' action='test.php'>";
	echo "Имя: <input name='username' required><br>";
	foreach ($test as $qId => $question) {
		echo '<p><b>'.htmlspecialchars(strip_tags($question->text)).'</b></p>';
		foreach ($question->options as $optionId => $option) {
			echo "<input type='checkbox' name='".htmlspecialchars(strip_tags($qId))." ".htmlspecialchars(strip_tags($optionId))."' value='1'>".htmlspecialchars(strip_tags($option[0]))."<br>";
		}
	}
	echo "<input type='hidden' name='id' value='$id'>";
	echo "<br><input type='submit' name='ready' value='Проверить'></form>";
?>
</body>
</html>