<?php
$subMenu = [0 => [
    'title' => 'Все',
    'path' => '/',
    'sort' => 10,]];
$i = 12;
foreach ($allSections as $section) {
    $subMenu[] = [
        'title' => $section['name'],
        'path' => '/?categoryId=' . $section['id'],
        'sort' => $i,
    ];
     $i = $i + 2;
}
