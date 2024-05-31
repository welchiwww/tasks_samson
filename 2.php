<?php
//Блок заданий 2 - Авдеев Данила Вячеславович

/*
Реализовать функцию convertString($a, $b). 
Результат ее выполнение: если в строке $a содержится 2 и более подстроки $b, 
то во втором месте заменить подстроку $b на инвертированную подстроку.
*/

function convertString(string &$a, string $b) : void {
    $count = substr_count($a, $b);
    
    if ($count >=2){
        $first_entry = strpos($a, $b);
        $second_entry = strpos($a, $b, $first_entry + strlen($b));
        $reversed_second = strrev($b);
        $a = substr_replace($a, $reversed_second, $second_entry, strlen($b));
    }
}

/*
$a = 'abcabcabc bac abc abc cbd abc';
convertString($a, 'abc');
print_r($a);
*/
// 'abccbdabc bac abc abc cbd abc'

/*
Реализовать функцию mySortForKey($a, $b). 
$a – двумерный массив вида [['a'=>2,'b'=>1],['a'=>1,'b'=>3]], 
$b – ключ вложенного массива. 
Результат ее выполнения: двумерном массива $a отсортированный по возрастанию значений для ключа $b. 
В случае отсутствия ключа $b в одном из вложенных массивов, 
выбросить ошибку класса Exception с индексом неправильного массива.
*/


function mySortForKey(array &$a, $b) : void{
    foreach ($a as $index => $inner_arr) {
        if (!array_key_exists($b, $inner_arr)) {
            throw new Exception("Ключ '$b' не найден в подмассиве с индексом $index");
        }
    }

    usort($a, function($x, $y) use ($b) {
        return $x[$b] <=> $y[$b];
    });

}

/*
$a = [
    ['a' => 1, 'b' => 3, 'e' => 5, 's' => 10.0],
    ['a' => 4, 'b' => 6, 'c' => 8, 's' => 40.0],
    ['a' => 5, 'b' => 6, 'c' => 7, 's' => 38.5],
    ['a' => 6, 'b' => 4, 'c' => 2, 's' => 10.0]
];

mySortForKey($a, 'c');
print_r($a);
*/

/*
Реализовать функцию importXml($a). 
$a – путь к xml файлу (структура файла приведена ниже). 
Результат ее выполнения: прочитать файл $a и импортировать его в созданную БД.
*/

function importXml($a) : void{

}

/*
Реализовать функцию exportXml($a, $b). 
$a – путь к xml файлу вида (структура файла приведена ниже), 
$b – код рубрики. 
Результат ее выполнения: 
выбрать из БД товары (и их характеристики, необходимые для формирования файла) 
выходящие в рубрику $b или в любую из всех вложенных в нее рубрик, сохранить результат в файл $a.
*/