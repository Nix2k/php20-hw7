<?php
	if (isset($_POST['upload'])) { //Если форма отправлена
		if (isset($_FILES['test'])) { //Файл передан
			$uploaddir = __DIR__.'/uploads/';
			$uploadfile = $uploaddir . basename($_FILES['test']['name']);
			if ($_FILES['test']['type']!='application/json') {
				http_response_code(400);
				die('Неверный тип файла');
			}
			if ($_FILES['test']['size']>2097152) {
				http_response_code(400);
				die('Слишком большой файл, максимальный размер 2 МБ');
			}
			$jsonData = file_get_contents($_FILES['test']['tmp_name']);
			if (!$jsonData) {
				http_response_code(500);
				die('Не удалось загрузить файл');
			}
			$test = json_decode($jsonData);
			if ((!$test)||($test==NULL)) {
				http_response_code(400);
				die('Неверный формат json файла');
			}

			//проверка структуры файла
			if (!isset($test[0]->title)) {
				http_response_code(400);
				die('Не задано название теста');
			}

			$title = array_shift($test);
			foreach ($test as $qId => $question) {
				if ((!isset($question->text))||(!isset($question->options[0][1]))){
					http_response_code(400);
					die('Неверная структура файла с тестом');
				}
				foreach ($question->options as $optionId => $option) {
					if ((!isset($optionId))||(!isset($option[0]))||(!isset($option[1]))){
						http_response_code(400);
						die('Неверная структура файла с тестом');
					}
				}
			}

			if (move_uploaded_file($_FILES['test']['tmp_name'], $uploadfile)) { //Удалось загрузить файл
	    		header('Location: index.php');
	    		exit();
			} else {
				http_response_code(500);
	    		die ('Ошибка загрузки');
			}
		} else {
			http_response_code(400);
			die ('Файл не получен');
		}
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Загрузка файла с тестом</title>
</head>
<body>
	<?php include 'menu.php';?>
	<h1>Загрузка файла с тестом</h1>
	<form  enctype="multipart/form-data" method="post" action="admin.php">
		<p>Файл с тестом <input type="file" name="test" accept=".json"></p>
		<input type="submit" name="upload" value="Загрузить">
	</form>
</body>
</html>