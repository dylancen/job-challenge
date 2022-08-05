<?php
    require('./ProductTree.php');
    #считываем входной путь файла c командной строки
    $input_path = $argv[1];

    #инициализируем класс дерева продуктов
    $treeObj = new ProductTree();

    #считываем данные из входного файла
    $treeObj->readCsvData('input.csv', 20000, ';');
    
    #выводим дерево в формате массива
    $tree = $treeObj->output_tree();

    #создаем json файл для вывода
    file_put_contents("task_output.json", json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>