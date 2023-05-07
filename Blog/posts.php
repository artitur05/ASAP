<?php
include __DIR__ . '/functions/db.php';
$title = 'Posts';
$id = $_GET['category_id'];
$result = getConnection()->prepare('SELECT * FROM posts WHERE category_id = :id');
$result->execute([':id'=>$id]);
$posts = $result->fetchAll();
$catNam = getConnection()->prepare('SELECT "catName" FROM categories WHERE id = :id');
$catNam->execute([':id' => $id]);
$catN = $catNam->fetch();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$title?></title>
</head>
<body>
<?php include __DIR__ . "/widgets/menu.php"; ?>
<b><?=$catN['catName'] ?></b>

<ul><?php foreach ($posts as $item):?>
    <li><a href="post.php?id=<?=$item['id']?>"><?=$item['title']?></a> </li>
    <?php endforeach; ?>
</ul>
</body>
</html>
