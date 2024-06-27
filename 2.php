<?php
//Блок заданий 2 - Авдеев Данила Вячеславович

/*
Реализовать функцию convertString($a, $b). 
Результат ее выполнение: если в строке $a содержится 2 и более подстроки $b, 
то во втором месте заменить подстроку $b на инвертированную подстроку.
*/

include "./db.php";

function mb_strrev(string $string): string
{
    $reversed = '';
    for ($i = mb_strlen($string) - 1; $i >= 0; $i--) {
        $reversed .= mb_substr($string, $i, 1);
    }
    return $reversed;
}

function convertString(string $a, string $b): string
{
    $count = mb_substr_count($a, $b);
    if ($count >= 2) {
        $first_entry = mb_strpos($a, $b);
        $second_entry = mb_strpos($a, $b, $first_entry + mb_strlen($b));
        $reversed_second = mb_strrev($b);
        $a = mb_substr($a, 0, $second_entry) . $reversed_second . mb_substr($a, $second_entry + mb_strlen($b));
    }
    return $a;
}


$a = 'абвcbd cbd абв вда вба дад';
$b = convertString('one two three four four', 'four'); 
print_r($b);


/*
Реализовать функцию mySortForKey($a, $b). 
$a – двумерный массив вида [['a'=>2,'b'=>1],['a'=>1,'b'=>3]], 
$b – ключ вложенного массива. 
Результат ее выполнения: двумерном массива $a отсортированный по возрастанию значений для ключа $b. 
В случае отсутствия ключа $b в одном из вложенных массивов, 
выбросить ошибку класса Exception с индексом неправильного массива.
*/

function mySortForKey(array $a, string $b): array
{
    foreach ($a as $index => $inner_arr) {
        if (!array_key_exists($b, $inner_arr)) {
            throw new Exception("Ключ '$b' не найден в подмассиве с индексом $index");
        }
    }

    usort($a, function ($x, $y) use ($b) {
        return $x[$b] <=> $y[$b];
    });

    return $a;
}

/*
$a = [
    ['a' => 1, 'b' => 3, 'c' => 5, 's' => 10.0, 3],
    ['a' => 4, 'b' => 6, 'c' => 8, 's' => 40.0, 4],
    ['a' => 5, 'b' => 6, 'c' => 7, 's' => 38.5, 1],
    ['a' => 6, 'b' => 4, 'c' => 2, 's' => 10.0, 2]
];

$b = mySortForKey($a, '0');
$c = mySortForKey($a, 'c');
print_r($b);
print_r($c);
*/


/*
Реализовать функцию importXml($a). 
$a – путь к xml файлу (структура файла приведена ниже). 
Результат ее выполнения: прочитать файл $a и импортировать его в созданную БД.
*/

//Вставка категории с проверкой на существование
function insertCategory(string $categoryName, int $parentId = null): int
{
    global $connection;
    $stmt = $connection->prepare(
        "SELECT id 
    FROM a_category
    WHERE name = ?"
    );
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["id"];
    } else {
        $stmt = $connection->prepare(
            "INSERT INTO a_category(name, parent_id)
        VALUES
        (?, ?)"
        );
        $stmt->bind_param("si", $categoryName, $parentId);
        $stmt->execute();
        $result = $stmt->insert_id;
        return $result;
    }
}

function importXml(string $a): void
{
    global $connection;
    $parent_global_id = null;
    $dom = new DOMDocument();
    $dom->load($a);

    $products = $dom->getElementsByTagName('Товар');

    foreach ($products as $product) {
        $code = $product->getAttribute('Код');
        $name = $product->getAttribute('Название');
        $stmt = $connection->prepare(
            "SELECT code, name 
            FROM a_product 
            WHERE code = ?"
        );
        $stmt->bind_param("i", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "В данной БД уже существует товар с кодом {$code} под названием {$name}";
            continue;
        }

        $stmt = $connection->prepare(
            "INSERT INTO a_product (code, name) 
        VALUES 
        (?, ?)"
        );
        $stmt->bind_param("is", $code, $name);
        $stmt->execute();
        $product_id = $stmt->insert_id;

        $prices = $product->getElementsByTagName('Цена');
        foreach ($prices as $price) {
            $priceType = $price->getAttribute('Тип');
            $priceValue = $price->nodeValue;
            $stmt = $connection->prepare(
                "INSERT INTO a_price(product_id, price_type, price) 
            VALUES 
            (?, ?, ?)"
            );
            $stmt->bind_param("iss", $product_id, $priceType, $priceValue);
            $stmt->execute();
        }

        $properties = $product->getElementsByTagName('Свойства')->item(0)->childNodes;
        foreach ($properties as $property) {
            if ($property instanceof DOMElement) {
                $propertyName = $property->nodeName;
                $propertyValue = $property->nodeValue;
                $unit = $property->hasAttribute('ЕдИзм') ? $property->getAttribute('ЕдИзм') : null;

                $stmt = $connection->prepare(
                    "INSERT INTO a_property(product_id, property_name, property_value, unit)
                VALUES
                (?, ?, ?, ?)"
                );
                $stmt->bind_param("isss", $product_id, $propertyName, $propertyValue, $unit);
                $stmt->execute();
            }
        }

        $categories = $product->getElementsByTagName('Разделы')->item(0)->childNodes;
        foreach ($categories as $category) {
            if ($category instanceof DOMElement) {
                $categoryName = $category->nodeValue;
                $parent_global_id = insertCategory($categoryName, $parent_global_id);
                $stmt = $connection->prepare(
                    "INSERT INTO a_product_category(product_id, category_id)
                VALUES
                (?, ?)"
                );
                $stmt->bind_param("ii", $product_id, $parent_global_id);
                $stmt->execute();
            }
        }
        $parent_global_id = null;
    }
}

/*
//В репозитории лежит файл datatest.xml использовал для проверки вложенных рубрик, добавил там фотобумагу и ручной принтер
$a = __DIR__ . "\data.xml";
importXml($a);
*/

/*
Реализовать функцию exportXml($a, $b). 
$a – путь к xml файлу вида (структура файла приведена ниже), 
$b – код рубрики. 
Результат ее выполнения: 
выбрать из БД товары (и их характеристики, необходимые для формирования файла) 
выходящие в рубрику $b или в любую из всех вложенных в нее рубрик, сохранить результат в файл $a.
*/

function exportLogic(string $a, int $code) : void{
    global $connection;
    $stmt = $connection->prepare(
        "SELECT 
            p.id, 
            p.code, 
            p.name,
            GROUP_CONCAT(DISTINCT CONCAT(pr.property_name, ': ', pr.property_value, IF(pr.unit IS NOT NULL, CONCAT(' ', pr.unit), '')) SEPARATOR ', ') AS properties,
            GROUP_CONCAT(DISTINCT CONCAT(prc.price_type, ': ', prc.price) SEPARATOR ', ') AS prices,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories
        FROM 
            a_product p
        JOIN 
            a_product_category pc ON p.id = pc.product_id
        JOIN 
            a_category c ON pc.category_id = c.id
        JOIN (
            SELECT id FROM a_category WHERE id = ?
            UNION ALL
            SELECT c.id FROM a_category c JOIN a_category parent ON c.parent_id = parent.id WHERE parent.id = ?
        ) AS subcategories ON pc.category_id = subcategories.id
        LEFT JOIN 
            a_property pr ON p.id = pr.product_id
        LEFT JOIN 
            a_price prc ON p.id = prc.product_id
        GROUP BY 
            p.id, p.code, p.name;
        "
        );
    
        $stmt->bind_param("ii", $code, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $dom = new DOMDocument('1.0', 'utf-8');
        //$dom = new DOMDocument('1.0', 'Windows-1251');
        $dom->formatOutput = true;
    
        $root = $dom->createElement('Товары');
        $dom->appendChild($root);
    
        while ($row = $result->fetch_assoc()) {
            $product = $dom->createElement('Товар');
            $product->setAttribute('Код', $row['code']);
            $product->setAttribute('Название', htmlspecialchars($row['name']));
            $root->appendChild($product);
    
            if ($row['prices']) {
                $prices = explode(', ', $row['prices']);
                foreach ($prices as $price) {
                    list($priceType, $priceValue) = explode(': ', $price);
                    $priceElement = $dom->createElement('Цена', htmlspecialchars($priceValue));
                    $priceElement->setAttribute('Тип', htmlspecialchars($priceType));
                    $product->appendChild($priceElement);
                }
            }
    
            if ($row['properties']) {
                $propertiesElement = $dom->createElement('Свойства');
                $properties = explode(', ', $row['properties']);
                foreach ($properties as $property) {
                    list($propertyName, $propertyValue) = explode(': ', $property, 2);
                    if (strpos($propertyValue, ' ') !== false) {
                        list($propertyValue, $unit) = explode(' ', $propertyValue, 2);
                        $propertyElement = $dom->createElement(htmlspecialchars($propertyName), htmlspecialchars($propertyValue));
                        $propertyElement->setAttribute('ЕдИзм', htmlspecialchars($unit));
                    } else {
                        $propertyElement = $dom->createElement(htmlspecialchars($propertyName), htmlspecialchars($propertyValue));
                    }
                    $propertiesElement->appendChild($propertyElement);
                }
                $product->appendChild($propertiesElement);
            }
    
            if ($row['categories']) {
                $categoriesElement = $dom->createElement('Разделы');
                $categories = explode(', ', $row['categories']);
                foreach ($categories as $category) {
                    $categoryElement = $dom->createElement('Раздел', htmlspecialchars($category));
                    $categoriesElement->appendChild($categoryElement);
                }
                $product->appendChild($categoriesElement);
            }
        }
    
        $dom->save($a);
}


function exportXml(string $a, string|int $b)
{
    global $connection;
    $code = null;
    if (is_string($b)){
        $stmt = $connection->prepare(
            "SELECT id 
            FROM a_category 
            WHERE name = ?"
            );
        $stmt->bind_param("s", $b);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $code = $row["id"];
            exportLogic($a, $code);
        }
        else{
            echo "Категории с именем {$b} не существует.";
            die();
        }
    }
    elseif(is_int($b)){
        $code = $b;
        exportLogic($a, $code);

    }
    else{
        echo "Категории с кодом {$b} не существует";
    }

}

exportXml("./exportPens.xml", "Ручки");
