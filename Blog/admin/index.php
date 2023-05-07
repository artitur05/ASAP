<?php
include dirname(__DIR__) . "/functions/db.php";


//messages
$messages = [
    'del' => 'Пост удален',
    'add' => 'Пост добавлен',
    'edit' => 'Пост изменен'
];

$message = !empty($_GET['status']) ? $messages[$_GET['status']] : '';
$raw = [
    'id' => 0,
    'title' => '',
    'text' => '',
    'image' => ''
];
$action = "add";
$formText = "Добавить";



//CRUD edit
if (!empty($_GET['action']) and $_GET['action'] == 'edit') {
    $id = (int)$_GET['id'];
    $result = getConnection()->prepare("SELECT * FROM posts WHERE id = :id");
    $result->execute(['id'=>$id]);
    $raw = $result->fetch();
    $action = "save";
    $formText = "Изменить";
}

if (!empty($_GET['action']) and $_GET['action'] == 'save') {
    $title = strip_tags($_POST['title']);
    $category_id = strip_tags($_POST['category']);
    $text = strip_tags($_POST['text']);
    $image = strip_tags($_POST['image']);
    $id = (int)$_POST['id'];

    $result = getConnection()->prepare("UPDATE posts SET title = ?, text = ?, category_id = ?, image = ? WHERE id = ?");
    $result->execute([$title, $text, $category_id, $image, $id]);

    header("Location: /admin?status=edit");
    die();
}
//CRUD create
if (!empty($_POST) and $_GET['action'] == 'add')
{
    $title = strip_tags($_POST['title']);
    $category_id = strip_tags($_POST['category']);
    $text = strip_tags($_POST['text']);
    $image = strip_tags($_POST['image']);

    $result = getConnection()->prepare("INSERT INTO posts (title, text, category_id,image) VALUES (?,?,?,?)");
    $result->execute([$title, $text, $category_id,$image]);

    header("Location: /admin?status=add");
    die();
}


//CRUD delete
if(!empty($_GET['action']) and $_GET['action']== 'delete')
{
    $id = (int)$_GET['id'];
    $result = getConnection()->prepare("DELETE FROM posts WHERE id = :id");
    $result->execute(['id'=>$id]);

    header("Location: /admin?status=del");
    die();

}
//  CRUD read
$result = getConnection()->prepare("SELECT * FROM posts ORDER BY id DESC");
$result->execute();
$posts = $result->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?='Админка'?></title>
</head>
<body>
<?php include dirname(__DIR__) . "/widgets/admin.php" ?>
<h2>CRUD посты</h2>
<h3 style="color: red"><?=$message?></h3>
<form enctype="multipart/form-data" action="?action=<?=$action?>" method="post">

    Заголовок поста:<br>
    <input type="text" name="title" value="<?=$raw['title']?>"><br>

    Категория поста:<br>
    <input hidden type="text" name="id"  value="<?=$raw['id']?>">

    <select name="category">
    <option value="1">Мясо</option>
    <option value="2">Хлеб</option>
        <option value="3">Рыба</option>
        <option value="4">Картошка</option>
    </select><br>

Текст поста: <br>
<textarea name="text" cols="30" rows="2"><?= $raw['text'] ?></textarea>
<input type="submit" value=<?=$formText?>>

    <input type="hidden" name="MAX_FILE_SIZE" value="3000" />
    <input name="userfile" type="file" />


    <?php
// File upload.php
// Если в $_FILES существует "image" и она не NULL
if (isset($_FILES['image'])) {
// Получаем нужные элементы массива "image"
$fileTmpName = $_FILES['image']['tmp_name'];
$errorCode = $_FILES['image']['error'];
// Проверим на ошибки
if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
    // Массив с названиями ошибок
    $errorMessages = [
      UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
      UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
      UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
      UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
      UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
      UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
      UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
    ];
    // Зададим неизвестную ошибку
    $unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';
    // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
    $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
    // Выведем название ошибки
    die($outputMessage);
} else {
    // Создадим ресурс FileInfo
    $fi = finfo_open(FILEINFO_MIME_TYPE);
    // Получим MIME-тип
    $mime = (string) finfo_file($fi, $fileTmpName);
    // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
    if (strpos($mime, 'image') === false) die('Можно загружать только изображения.');

    // Результат функции запишем в переменную
    $image = getimagesize($fileTmpName);

    // Зададим ограничения для картинок
    $limitBytes  = 1024 * 1024 * 5;
    $limitWidth  = 1280;
    $limitHeight = 768;

    // Проверим нужные параметры
    if (filesize($fileTmpName) > $limitBytes) die('Размер изображения не должен превышать 5 Мбайт.');
    if ($image[1] > $limitHeight)             die('Высота изображения не должна превышать 768 точек.');
    if ($image[0] > $limitWidth)              die('Ширина изображения не должна превышать 1280 точек.');

    // Сгенерируем новое имя файла через функцию getRandomFileName()
    $name = getRandomFileName($fileTmpName);

    // Сгенерируем расширение файла на основе типа картинки
    $extension = image_type_to_extension($image[2]);

    // Сократим .jpeg до .jpg
    $format = str_replace('jpeg', 'jpg', $extension);

    // Переместим картинку с новым именем и расширением в папку /upload
    if (!move_uploaded_file($fileTmpName, __DIR__ . '/upload/' . $name . $format)) {
        die('При записи изображения на диск произошла ошибка.');
    }

    echo 'Картинка успешно загружена!';
  }
};

// File functions.php
function getRandomFileName($path)
{
  $path = $path ? $path . '/' : '';
  do {
      $name = md5(microtime() . rand(0, 9999));
      $file = $path . $name;
  } while (file_exists($file));

  return $name;
}?>
</form>

    <?php foreach ($posts as $post):?>
        <li><a href="/post.php?id=<?=$post['id']?>"><?=$post['title']?></a>
            <a href="?id=<?=$post['id']?>&action=edit">[Edit]</a>
            <a href="?id=<?=$post['id']?>&action=delete">[Delete]</a>
        </li>
    <?php endforeach; ?>
</body>
</html>