<?php
    class ProductTree{
        private $parent_child_array = array();

        #Функция для считывания данных из Csv файла
        #Фходные параметры: (1) путь к входному файлу, (2) максимальное количество строк ввода, (3) делиминатор string данных
        public function readCsvData($input_path, $maxlines, $delim){
            #открыть поток файла для чтения
            $open = fopen($input_path, "r");

            #читаем каждую строку, устанавливая разделитель на символ ';'
            while (($data = fgetcsv($open, $maxlines, $delim)) !== FALSE) 
            {
                # data[0] - id
                # data[1] - type
                # data[2] - parent
                # data[3] - relation       
                $this->parent_child_array[$data[2]][] = $data; #создаем массив родитель -> дети

            }
            # закрываем поток
            fclose($open);
        }

        #Wrapper функция для вывода дерева пользователю
        public function output_tree(){
            return $this->generate_tree($this->parent_child_array, $this->parent_child_array['']);
        }

        #Рекурсивная функция для создания дерева в форме массива
        #Входные параметры: (1) считанный массив данных, (2) изначальный нод (root node)
        private function generate_tree(&$tree_array, $child_nodes){
            $output_tree = array();
            #итерируем всех детей входного элемента
            foreach($child_nodes as $order=>$child){

                #задаем формат конечного вывода в JSON
                $output_child['itemName'] = $child[0];
                $child[2] != '' ? $output_child['parent'] = $child[2] : $output_child['parent'] = null;
               
                
                #проверка на наличие дальнейших детей у этого элемента
                if(isset($tree_array[$child[0]])){ 
                    #если есть дети, исследуем их рекурсивно
                    $output_child['children'] = $this->generate_tree($tree_array, $tree_array[$child[0]]);

                #проверка на наличие связи через relation в случае если это элемент типа "Прямые компоненты"
                }elseif($child[1] == 'Прямые компоненты' && isset($child[3])){ 
                    
                    #в случае если связь есть, проверяем его наличие в ключах массива родитель -> дети
                    if(isset($tree_array[$child[3]])){
                        #если все условия соблюдены, генерируем дерево через эту связь
                        $output_child['children'] = $this->generate_tree($tree_array, $tree_array[$child[3]]);
                    }else{
                        #в противном случае обозначаем что детей нет
                        $output_child['children'] = [];
                    }
                }else{
                    #в противном случае обозначаем что детей нет
                    $output_child['children'] = [];
                }
                # добавляем рекурсивную цепочку детей в массив
                $output_tree[] = $output_child;
            }
            #выводим в следующий шаг рекурсии
            return $output_tree;
        }
    }

?>