<?php
include dirname(__DIR__) . "/functions/db.php";


//messages
$messages = [
    'del' => 'Пост удален',
    'add' => 'Пост добавлен',
    'edit' => 'Пост изменен',
    'error' => 'Ошибка'
];

$action = $_GET['action'] ?? '';

$message = !empty($_GET['status']) ? $messages[$_GET['status']] ?? '' : '';

$raw = [
    'id' => 0,
    'title' => '',
    'text' => '',
    'category_id' => '',
];
$formAction = "add";
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
    $id = (int)$_POST['id'];

    $result = getConnection()->prepare("UPDATE posts SET title = ?, text = ?, category_id = ? WHERE id = ?");
    $result->execute([$title, $text, $category_id, $id]);

    header("Location: /admin?status=edit");
    die();
}
//CRUD create
if (!empty($_POST) and $_GET['action'] == 'add')
{
    $title = strip_tags($_POST['title']);
    $category_id = strip_tags($_POST['category']);
    $text = strip_tags($_POST['text']);
    $image = '';
    if (!empty($_FILES)) {
        if ($_FILES["image"]["size"] > 1024 * 5 * 1024) {
            echo("Размер файла не больше 5 мб");
            exit;
        }

        $blacklist = [".php", ".phtml", ".php3", ".php4"];
        foreach ($blacklist as $item) {
            if (preg_match("/$item\$/i", $_FILES['image']['name'])) {
                echo "Загрузка php-файлов запрещена!";
                exit;
            }
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . '/images/' . $_FILES['image']['name'])) {
            header("Location: ?status=error");
            die();
        }

        $image = $_FILES['image']['name'];
    }

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

$resultCategories = getConnection()->prepare("SELECT * FROM categories");
$resultCategories->execute();
$categories = $resultCategories->fetchAll();

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
        <?php foreach ($categories as $category): ?>
            <option
                <?php if ($category['id'] == $raw['category_id']): ?>
                    selected
                <?php endif; ?>
                value="<?= $category['id'] ?>">  <?= $category['catName'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    Текст поста: <br>
    <textarea name="text" cols="30" rows="2"><?= $raw['text'] ?></textarea><br>

    Загрузите картинку<br>
    <input type="file" name="image"><br>
    <input type="submit" value="<?= $formText ?>">

</form>
    <?php foreach ($posts as $post):?>
        <li><a href="/post.php?id=<?=$post['id']?>"><?=$post['title']?></a>
            <a href="?id=<?=$post['id']?>&action=edit">[Edit]</a>
            <a href="?id=<?=$post['id']?>&action=delete">[Delete]</a>
        </li>
    <?php endforeach; ?>
</body>
</html>