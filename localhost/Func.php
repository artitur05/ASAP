<?php

function total($cart)
{
    $dateWD = date("m.d.y");
    $sum = 0;
    foreach ($cart as $item)
    {
        $sum = $sum + $item['price'] * $item['qty'] . ' ' . $dateWD;
    }
    return $sum;
}
