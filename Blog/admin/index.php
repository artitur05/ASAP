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
    'text' => ''
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

    $result = getConnection()->prepare("INSERT INTO posts (title, text, category_id) VALUES (?,?,?)");
    $result->execute([$title, $text, $category_id]);

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
$result = getConnection()->prepare("SELECT id,title FROM posts ORDER BY id DESC");
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
<form action="?action=<?=$action?>" method="post">
    Заголовок поста:<br>
    <input type="text" name="title" value="<?=$raw['title']?>"><br>
    Категория поста:<br>
    <input hidden type="text" name="id"  value="<?=$raw['id']?>">
    <select name="category">
    <option value="1">Хлеб</option>
    <option value="2">Мясо</option>
</select><br>
Текст поста: <br>
<textarea name="text" cols="30" rows="2"><?= $raw['text'] ?></textarea>
<input type="submit" value=<?=$formText?>>

</form>
    <?php foreach ($posts as $post):?>
        <li><a href="/post.php?id=<?=$post['id']?>"><?=$post['title']?></a>
            <a href="?id=<?=$post['id']?>&action=edit">[Edit]</a>
            <a href="?id=<?=$post['id']?>&action=delete">[Delete]</a>
        </li>
    <?php endforeach; ?>
</body>
</html>