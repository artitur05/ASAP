    <?php
include dirname(__DIR__) . "/functions/db.php";
//messages
$messages = [
    'del' => 'Категория удалена',
    'add' => 'Категория добавлена',
    'edit' => 'Категория изменена'
];

$message = !empty($_GET['status']) ? $messages[$_GET['status']] : '';
$raw = [
    'id' => 0,
    'catName' => '',
];
$action = "add";
$formText = "Добавить";
//CRUD edit
if (!empty($_GET['action']) and $_GET['action'] == 'edit') {
    $id = (int)$_GET['id'];
    $result = getConnection()->prepare("SELECT * FROM categories WHERE id = :id");
    $result->execute(['id'=>$id]);
    $raw = $result->fetch();
    $action = "save";
    $formText = "Изменить";
}

if (!empty($_GET['action']) and $_GET['action'] == 'save') {
    $title = strip_tags($_POST['title']);
    $id = (int)$_POST['id'];

    $result = getConnection()->prepare('UPDATE categories SET "catName" = ? WHERE id = ?');
    $result->execute([$title, $id]);

    header("Location: /admin?status=edit");
    die();
}
//CRUD create
if (!empty($_POST) and $_GET['action'] == 'add')
{
    $title = strip_tags($_POST['title']);


        $result = getConnection()->prepare('INSERT INTO categories ("catName") VALUES (?)');
    $result->execute([$title]);

    header("Location: /admin?status=add");
    die();
}


//CRUD delete
if(!empty($_GET['action']) and $_GET['action']== 'delete')
{
    $id = (int)$_GET['id'];
    $result = getConnection()->prepare("DELETE FROM categories WHERE id = :id");
    $result->execute(['id'=>$id]);

    header("Location: /admin?status=del");
    die();

}
//  CRUD read
$result = getConnection()->prepare('SELECT id,"catName" FROM categories ORDER BY id DESC');
$result->execute();
$category = $result->fetchAll();
?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?='Админка'?></title>
</head>
<body>
<?php include dirname(__DIR__) . "/widgets/admin.php" ?>
<h2>CRUD Категории</h2>
<h3 style="color: red"><?=$message?></h3>
<form action="?action=<?=$action?>" method="post">
    Название категории:<br>
    <input type="text" name="catName" value="<?=$raw['catName']?>"><br>
    <input hidden type="text" name="id"  value="<?=$raw['id']?>">
    <a><?=var_dump($raw);?></a>

</form>
<?php foreach ($category as $cat):?>
    <li><a href="/categories.php?id=<?=$cat['id']?>"><?=$cat['catName']?></a>
        <a href="?id=<?=$cat['id']?>&action=edit">[Edit]</a>
    <a href="?id=<?=$cat['id']?>&action=delete">[Delete]</a>
    </li>
<?php endforeach; ?>

</body>
</html>