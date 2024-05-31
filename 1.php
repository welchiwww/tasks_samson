<pre>
<?php

// Исправления по заданиям для прохождения на стажировку Авдеев Данила Вячеславович, 29.05.2024
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//Версия PHP 8.3.7
/*
1.
Реализовать функцию findSimple ($a, $b). $a и $b – целые положительные числа. 
Результат ее выполнения: массив простых чисел от $a до $b.
*/

//Функция для проверки простоты числа
function checkSimple(int $value): bool
{
    if ($value < 2) {
        return false;
    }
    for ($i = 2; $i <= sqrt($value); $i++) {
        if ($value % $i == 0) {
            return false;
        }
    }
    return true;
}

function findSimple(int $a, int $b): array
{
    if ($a <= 0 || $b <= 0 || $a >= $b) {
        throw new Exception("Введите правильные границы");
    }

    $simple_array = [];
    for ($i = $a; $i <= $b; $i++) {
        if (checkSimple($i)) {
            $simple_array[] = $i;
        }
    }
    return $simple_array;
}

//Пример использования
//$simple_array = findSimple(7, 24);
//print_r($simple_array);

/*
2.
Реализовать функцию createTrapeze($a). 
$a – массив положительных чисел, количество элементов кратно 3. 
Результат ее выполнения: двумерный массив (массив состоящий из ассоциативных массивов с ключами a, b, c). 
Пример для входных массива [1, 2, 3, 4, 5, 6] результат [[‘a’=>1,’b’=>2,’с’=>3],[‘a’=>4,’b’=>5 ,’c’=>6]].
*/

//Реализована для проверки переменной в функциях

function checkArray(array $a): void
{
    if (empty($a)) {
        throw new Exception("Полученный массив не может быть пустым");
    }
}

//Реализована для генерации массивов от a до b
function generateArray(int $a, int $b): array
{
    if ($a >= $b) {
        throw new Exception("Левая граница не может быть больше правой");
    } else if ($a < 0 || $b < 0) {
        throw new Exception("Границы не могут быть меньше 0");
    }

    $array = [];
    for ($i = $a; $i <= $b; $i++) {
        $array[] = $i;
    }
    return $array;
}


function createTrapeze(array &$a): void
{
    checkArray($a);
    $negatives = array_filter($a, function ($value) {
        return $value < 0;
    });
    if (count($a) % 3 != 0) {
        throw new Exception("Полученный массив не кратен 3");
    } else if (!empty($negatives)) {
        print_r($negatives);
        throw new Exception("Массив содержит отрицательные числа");
    }
    $result = [];
    $array_chunks = array_chunk($a, 3);
    foreach ($array_chunks as $values) {
        $result[] = array_combine(['a', 'b', 'c'], $values);
    }
    $a = $result;
}

//$a = [1, 2, 3, 4, 5, 6];
//createTrapeze($a);
//createTrapeze();
//print_r($a);

/*
3.
Реализовать функцию squareTrapeze($a). 
$a – массив результата выполнения функции createTrapeze(). 
Результат ее выполнения: в исходный массив для каждой тройки чисел добавляется дополнительный ключ s, 
содержащий результат расчета площади трапеции со сторонами a и b, и высотой c.
*/

function squareTrapeze(array &$a): void
{
    checkArray($a);
    foreach ($a as &$inner_arr) {
        if (isset($inner_arr['a'], $inner_arr['b'], $inner_arr['c'])) {
            $inner_arr['s'] = (($inner_arr['a'] + $inner_arr['b']) * $inner_arr['c']) / 2;
        } else {
            throw new Exception("Полученная переменная это не результат функции createTrapeze");
        }
    }
}

//$d = [1, 3, 5, 2, 1, 2];
//createTrapeze($d);
//squareTrapeze($d);
//$c = [];
//print_r($d);

/*
4.
Реализовать функцию getSizeForLimit($a, $b). 
$a – массив результата выполнения функции squareTrapeze(), $b – максимальная площадь. 
Результат ее выполнения: массив размеров трапеции с максимальной площадью, но меньше или равной $b.
*/

function getSizeForLimit(array $a, int $b): array
{
    checkArray($a);
    if ($b <= 0) {
        throw new Exception("Максимальная площадь не может быть меньше 0");
    }
    $result = null;
    foreach ($a as $inner_arr) {
        if (isset($inner_arr['s'])) {
            if ($inner_arr['s'] <= $b && ($result === null || $inner_arr['s'] > $result['s'])) {
                $result = $inner_arr;
            }
        } else {
            throw new Exception("Полученная переменная это не результат функции squareTrapeze");
        }
    }
    return $result ?? [];
}

// Пример использования
//$b = generateArray(1,6);
//createTrapeze($b);
//squareTrapeze($b);
//$maxSize = getSizeForLimit($b, 5);
//print_r($maxSize);

/*
$a = [
    ['a' => 1, 'b' => 3, 'c' => 5, 's' => 10.0],
    ['a' => 4, 'b' => 6, 'c' => 8, 's' => 40.0],
    ['a' => 5, 'b' => 6, 'c' => 7, 's' => 38.5],
    ['a' => 6, 'b' => 4, 'c' => 2, 's' => 10.0]
];
print_r(getSizeForLimit($a, 39));
*/

/*
5.
Реализовать функцию getMin($a). $a – массив чисел. 
Результат ее выполнения: минимальное число в массиве (не используя функцию min, ключи массива могут быть ассоциативными).
*/

function getMin(array $a): int|float
{
    checkArray($a);
    $min = reset($a);
    foreach ($a as $value) {
        if ($value < $min) {
            $min = $value;
        }
    }
    return $min;
}

// Пример использования
//$array = [];
//$array = generateArray(1, 5);
//$array = ['a' => 0, 'b' => 4, 'c' => -5.3];
//print_r(getMin($array));

/*
6.
Реализовать функцию printTrapeze($a). 
$a – массив результата выполнения функции squareTrapeze(). 
Результат ее выполнения: вывод таблицы с размерами трапеций, строки с нечетной площадью трапеции отметить любым способом.
*/

function printTrapeze(array $a): void
{
    checkArray($a);
    echo "<ul>";
    $i = 1;
    foreach ($a as $inner_arr) {
        if (gettype($inner_arr['s']) == 'double') {
            $odd = true;
        } else {
            $odd = $inner_arr['s'] % 2 != 0;
        }

        echo "<li>";
        echo "{$i} трапеция" . "</br>";
        echo "a: {$inner_arr['a']}" . "</br>";
        echo "b: {$inner_arr['a']}" . "</br>";
        echo "c: {$inner_arr['a']}" . "</br>";
        ($odd == true) ? $s = "s: {$inner_arr['s']}" . " - нечетная площадь." : $s = "s: {$inner_arr['s']}";
        echo "s: {$s}";
        $i++;
    }
    echo "</ul";
}

// Пример использования
//$a = generateArray(1, 21);
//createTrapeze($a);
//printTrapeze(squareTrapeze($a));

/*
7.
Реализовать абстрактный класс BaseMath содержащий 3 метода: exp1($a, $b, $c) и exp2($a, $b, $c),getValue(). 
Метод exp1 реализует расчет по формуле a*(b^c). 
Метод exp2 реализует расчет по формуле (a/b)^c. 
Метод getValue() возвращает результат расчета класса наследника.
*/

abstract class BaseMath
{
    //$a - множитель 1, $b - множитель 2, $c - степень для $b
    public function exp1(float $a, float $b, float $c): float
    {
        return $a * ($b ** $c);
    }
    //$a - делимое, $b - делитель, $c - степень
    public function exp2(float $a, float $b, float $c): float
    {
        return ($a / $b) ** $c;
    }
    public abstract function getValue();
}

/*
8.
Реализовать класс F1 наследующий методы BaseMath, содержащий конструктор с параметрами ($a, $b, $c) и метод getValue(). 
Класс реализует расчет по формуле f=(a*(b^c)+(((a/c)^b)%3)^min(a,b,c)).
*/

class F1 extends BaseMath
{
    public function __construct(private float $a, private float $b, private float $c)
    {
    }
    public function getValue(): float
    {
        //Чтобы избежать повторения $this для переменных в return
        $a = $this->a;
        $b = $this->b;
        $c = $this->c;
        return $this->exp1($a, $b, $c) + ($this->exp2($a, $c, $b) % 3) ** min($a, $b, $c);
    }
}

/*
$exampleF = new F1(2.2, 6.4 , 5);
print_r("Класс F1:");
print_r("Метод getValue(): " . $exampleF->getValue() . "</br>");
print_r("Метод exp1: " . $exampleF->exp1(1, 2, 3) . "</br>");
print_r("Метод exp2: " . $exampleF->exp2(2, 4, 7) . "</br>");
*/

?>
</pre>