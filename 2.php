<?php
//Блок заданий 2 - Авдеев Данила Вячеславович

/*
Реализовать функцию convertString($a, $b). 
Результат ее выполнение: если в строке $a содержится 2 и более подстроки $b, 
то во втором месте заменить подстроку $b на инвертированную подстроку.
*/

include "./db.php";

function convertString(string &$a, string $b): void
{
    $count = substr_count($a, $b);

    if ($count >= 2) {
        $first_entry = strpos($a, $b);
        $second_entry = strpos($a, $b, $first_entry + strlen($b));
        $reversed_second = strrev($b);
        $a = substr_replace($a, $reversed_second, $second_entry, strlen($b));
    } else {
        throw new Exception("Строка содержит менее 2 подстрок '{$b}'");
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

function mySortForKey(array &$a, $b): void
{
    foreach ($a as $index => $inner_arr) {
        if (!array_key_exists($b, $inner_arr)) {
            throw new Exception("Ключ '$b' не найден в подмассиве с индексом $index");
        }
    }

    usort($a, function ($x, $y) use ($b) {
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


function importXml(string $a): void
{
    global $connection;
    $file = file_get_contents($a);
    $pattern = '/<\?xml version="1.0" encoding="Windows-1251"\?>/i';
    $replacement = '<?xml version="1.0"?>';

    $file = preg_replace($pattern, $replacement, $file);

    $xml = simplexml_load_string($file);

    if ($xml === false) {
        throw new Exception("Ошибка при загрузке XML файла");
    }
    foreach ($xml->Товар as $product) {
        $code = $product['Код'];
        $name = $product['Название'];
        $stmt = $connection->prepare("INSERT INTO a_product (code, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $code, $name);
        $stmt->execute();
        $productId = $connection->insert_id;

        foreach ($product->Цена as $price) {
            $priceType = $price['Тип'];
            $priceValue = (float) $price;
            $stmt = $connection->prepare("INSERT INTO a_price (product_id, price_type, price) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $productId, $priceType, $priceValue);
            $stmt->execute();
        }

        foreach ($product->Свойства->children() as $propertyName => $propertyValue) {
            $propertyValue = (string) $propertyValue;
            $unit = isset($propertyValue['ЕдИзм']) ? (string) $propertyValue['ЕдИзм'] : null;
            $stmt = $connection->prepare("INSERT INTO a_property (product_id, property_name, property_value, unit) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $productId, $propertyName, $propertyValue, $unit);
            $stmt->execute();
        }

        foreach ($product->Разделы->Раздел as $category) {
            $categoryName = (string) $category;
            $categoryCode = (string) $category['Код'];
            $parentCode = isset($category['Родитель']) ? (string) $category['Родитель'] : null;

            if ($parentCode) {
                $stmt = $connection->prepare("SELECT id FROM a_category WHERE code = ?");
                $stmt->bind_Param("s", $parentCode);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $parentId = $row['id'];
                } else {
                    $parentId = null;
                }
            } else {
                $parentId = null;
            }

            $stmt = $connection->prepare("SELECT id FROM a_category WHERE code = ? AND name = ?");
            $stmt->bind_param("ss", $categoryCode, $categoryName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $stmt = $connection->prepare("INSERT INTO a_category (code, name, parent_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $categoryCode, $categoryName, $parentId);
                $stmt->execute();
                $categoryId = $connection->insert_id;
            } else {
                $row = $result->fetch_assoc();
                $categoryId = $row['id'];
            }

            $stmt = $connection->prepare("INSERT INTO a_product_category (product_id, category_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $productId, $categoryId);
            $stmt->execute();
        }
    }
}


//$a = __DIR__ . "\data.xml";
//importXml($a);

/*
Реализовать функцию exportXml($a, $b). 
$a – путь к xml файлу вида (структура файла приведена ниже), 
$b – код рубрики. 
Результат ее выполнения: 
выбрать из БД товары (и их характеристики, необходимые для формирования файла) 
выходящие в рубрику $b или в любую из всех вложенных в нее рубрик, сохранить результат в файл $a.
*/

function exportXml($a, $b) {
    global $connection;
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Товары></Товары>');
    
    $sql = "SELECT 
                p.id AS product_id, 
                p.code, 
                p.name AS product_name,
                GROUP_CONCAT(DISTINCT pr.price_type, ':', pr.price) AS prices,
                GROUP_CONCAT(DISTINCT ap.property_name, ':', ap.property_value) AS properties
            FROM 
                a_product p
            JOIN 
                a_product_category pc ON p.id = pc.product_id
            JOIN 
                a_category c ON pc.category_id = c.id
            JOIN 
                a_price pr ON p.id = pr.product_id
            JOIN 
                a_property ap ON p.id = ap.product_id
            WHERE 
                c.id IN (
                    SELECT 
                        id
                    FROM 
                        (
                            SELECT 
                                id
                            FROM 
                                a_category
                            WHERE 
                                code = '$b'
                            UNION ALL
                            SELECT 
                                c.id
                            FROM 
                                a_category c
                            JOIN 
                                (
                                    SELECT 
                                        id
                                    FROM 
                                        a_category
                                    WHERE 
                                        code = '$b'
                                ) AS ct ON c.parent_id = ct.id
                        ) AS all_categories
                )
            GROUP BY
                p.id, p.code, p.name
            ORDER BY 
                p.id";
                
    $result = $connection->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $product = $xml->addChild('Товар');
        $product->addAttribute('Код', $row['code']);
        $product->addAttribute('Название', $row['product_name']);
        
        $prices = explode(',', $row['prices']);
        foreach ($prices as $price) {
            list($type, $value) = explode(':', $price);
            $priceElement = $product->addChild('Цена', $value);
            $priceElement->addAttribute('Тип', $type);
        }
        
        $properties = explode(',', $row['properties']);
        $propertiesElement = $product->addChild('Свойства');
        foreach ($properties as $property) {
            list($name, $value) = explode(':', $property);
            $propertiesElement->addChild($name, $value);
        }
    }
    
    $xml->asXML($a);
}


exportXml( __DIR__ . "/export23.xml", "20");
