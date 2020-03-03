<?php
/**
 * Created by PhpStorm.
 * User: vasili
 * Date: 02/03/2020
 * Time: 14:30
 */
include __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('Europe/Moscow');

$row = 0;
$arr = array();
$file= "/Users/vasili/Downloads/products_variations_03032020 (1).csv";
try {
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ";", "\"", "\n")) !== FALSE) {
                $num = count($data);
                //echo "<p><b style='font-size: 22px'> $num полей в строке {$row}:</b> <br /></p>\n"; // вывод количества строк
                for ($c = 0; $c < $num; $c++) {
                    //echo $data[$c] . "<br />\n"; //вывод содержимого
                    $arr[$row][$c] = $data[$c];
                }
                $row++;
            }
            fclose($handle);
            $arrShift = array_shift($arr);
            $categories = array_unique(array_column($arr, 3));
            $import = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <yml_catalog date=\"" . date("Y-m-d H:i") . "\">
            <shop>
                <name>Well-men</name>
                <company>Tne Best inc.</company>
                <url>https://well-men.ru/</url>
                <currencies>
                    <currency id=\"RUR\" rate=\"1\"/>
                </currencies>
                <categories> \n";
            foreach ($categories as $index => $category) {
                $import .= "           <category id=\"".($index)."\">{$category}</category> \n";
            }
            $import .= "
                </categories>
                <delivery-options>
                    <option cost=\"300\" days=\"1-7\"/>
                </delivery-options>
                <offers>";
            foreach ($arr as $item) {
                if(!empty($item[21])){
                    $import .= " 
                        <offer id=\"{$item[2]}\">
                            <name>{$item[21]}</name>
                            <vendor>Well-men</vendor>
                            <url>{$item[33]}</url>
                            <price>{$item[5]}</price>
                            <enable_auto_discounts>false</enable_auto_discounts>
                            <currencyId>RUR</currencyId>
                            <categoryId>";
                    foreach ($categories as $index => $cat) {
                        if ($item[3] == $cat) $import .= $index;
                        //if($item[3] == 'Женские возбудители') $import .= "1";
                    }
                    $import .= "</categoryId>
                            <picture>{$item[38]}</picture>
                            <delivery>true</delivery>
                            <pickup>true</pickup>
                            <delivery-options>
                                <option cost=\"300\" days=\"1-7\" order-before=\"19\"/>
                            </delivery-options>
                            <pickup-options>
                                <option cost=\"0\" days=\"1\"/>
                            </pickup-options>
                            <store>true</store>
                            <description>";
                            $import .= "<![CDATA[  
                              <h3>{$item[19]}</h3>\n";//
                                $import .= addslashes($item[20]);
                            $import .= "]]>
                            </description>
                            <manufacturer_warranty>true</manufacturer_warranty>
                            <condition type=\"likenew\"></condition>
                        </offer> \n";
                }
            }
            $import .= "
                </offers>
                <gifts>
                    <!-- подарки не из прайс‑листа -->
                </gifts>
                <promos>
                    Действуют скидки на заказ от 3 упаковок одного товара!
                </promos>
            </shop>
        </yml_catalog>";

            // открываем файл, если файл не существует,
            //делается попытка создать его
            $fp = fopen("yandex.yml", "w");

            // записываем в файл текст
            fwrite($fp, $import);

            // закрываем
            fclose($fp);
            //dd($arr);
            unset($import, $arr, $categories);
        }else exit("Error with open file");
    }
    catch (Exception $e){
        exit($e);
    }

