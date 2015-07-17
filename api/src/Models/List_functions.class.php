<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 3/24/2015
 * Time: 6:40 PM
 */
require_once(__DIR__ . './../Models/Database.class.php');
class List_functions {
    private $db;


    public function swap_menu_items($table_name, $id_1, $id_2){
        $lo_1 = $this->db->select_single_item('list_order', $table_name, Array('id' => $id_1));
        $lo_2 = $this->db->select_single_item('list_order', $table_name, Array('id' => $id_2));

        $this->db->update($table_name, $id_1, Array('list_order' => $lo_2));
        $this->db->update($table_name, $id_2, Array('list_order' => $lo_1));
    }
    /*
     * Order menu items for display on the site.
     *
     * Rules: if the list_order has been set to zero, the item is not included
     */
    public function order_menu_array($raw_array){
        $return_array = Array();

        foreach($raw_array as $idx => $item){
            $header = $item['header'];
            $item_id = (int)$item['list_order']-1;
            if(!array_key_exists($header, $return_array)){
                $return_array[$header] = Array();
            }

            $section = &$return_array[$header];
            if(array_key_exists($item_id, $section)){
                array_push($section[$item_id]['descriptions'], Array(
                    'text' => $item['description'],
                    'id' => $item['desc_id'],
                    'price' => $item['subprice']
                ));
            }else{
                if((int)$item['list_order'] === 0){
                    continue;
                }
                $section[(int)$item_id] = Array(
                    'title' => $item['title'],
                    'descriptions' => Array(
                        Array(
                            'text' => $item['description'],
                            'id' => $item['desc_id'],
                            'price' => $item['subprice']
                        )
                    ),
                    'price' => $item['price']
                );
            }


        }
        return $return_array;
    }

    public function order_menu_cms($raw_array){
        $return_array = Array();
        $group_array = Array();
        foreach($raw_array as $key => $value){
            $title = $value['title'];
            $content = Array(
                'id' => $value['id'],
                'header' => $value['header'],
                'list_order' => $value['list_order'],
                'title' => $value['title'],
                'description' => $value['description'],
                'desc_id' => $value['desc_id'],
                'price' => $value['price'],
                'subprice' => $value['subprice'],
                'subprice_desc_id' => $value['subprice_id']
            );
            if((int)$value['list_order'] === 0){
                continue;
            }

            if(array_key_exists($title, $group_array)){
                array_push($group_array[$title], $content);
            }else{
                $group_array[$title] = Array($content);
            }

        }

        foreach($group_array as $item){
            $header = $item[0];
            $header = $header['header'];
            $title = $item[0];
            $title = $title['title'];


            if(!array_key_exists($header, $return_array)){
                $return_array[$header] = Array();
            }
            $key = &$return_array[$header];
            $key[$title] = $item;


        }
        return $return_array;

    }
    public function order_press($uo_list){
        $return_array = Array();
        foreach($uo_list as $value){
            $type = $value['type'];
            if(!isset($return_array[$type])){
                $return_array[$type] = Array();
            }
            array_push($return_array[$type], $value);
        }
        return $return_array;
    }
    public function __construct(){
        $this->db = Database::get_instance();
    }
} 