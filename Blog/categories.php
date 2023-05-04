<?php
include __DIR__ . "/functions/db.php";
$title = "Категории";
$result = getConnection()->query("SELECT * FROM categories");
$categories = $result->fetchAll();
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
Категории:
<ul>
    <?php foreach ($categories as $item):?>
        <li><a href="posts.php?category_id=<?=$item['id']?>"><?=$item['catName']?></a></li>
    <?php endforeach; ?>
</ul>
</body>
</html>
