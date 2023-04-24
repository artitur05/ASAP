<?php

function total($cart)
{

    $sum = 0;
    foreach ($cart as $item)
    {
        $sum = $sum + $item['price'] * $item['qty'];
    }
    return $sum;
}
