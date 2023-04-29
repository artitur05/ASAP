<?php
include __DIR__ . '/functions/db.php';
$title = 'Post';
$id = $_GET['id'];
$result = getConnection()->prepare('SELECT category_id,"catName",title, text FROM posts 
    JOIN categories ON posts.category_id = categories.id WHERE posts.id = :id'
);
$result->execute([':id'=>$id]);
$post = $result->fetch();
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
<b><?=$post['catName']?></b><br>

<b><?=$post['title']?></b>
<p><?=$post['text']?></p>
</body>
</html>
