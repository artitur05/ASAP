<?php
include 'Func.php';
include 'log.php';

$cart = [
  [
      'name' => 'Кофеварка',
      'price' => 1,
      'qty' => 5
  ],
    [
        'name' => 'Лопата',
        'price' => 2,
        'qty' => 7
    ],
    [
        'name' => 'Стиральная машина',
        'price' => 1,
        'qty' => 6
    ]
];
_log($cart);
_log('Сумма равна ' . total($cart));
