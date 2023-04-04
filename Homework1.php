<?php
    $arr = ['яблок','яблоко','яблока'];

for($i=0 ; $i<=50 ; $i++){
    
if ($i==0 or $i%10==0 or $i%10>4 or $i>10 and $i<15)
{
    echo $arr[0],PHP_EOL;
}
else if ($i%10>0 and $i%10<2)
{
    echo $arr[1],PHP_EOL;
}
else
{
echo $arr[2],PHP_EOL;
}
}
?>
