<?php

namespace App\Http\Controllers;

use App\model\Menu;
use App\model\MenuCategory;
use App\model\Shop;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //所有商家
    public function shops(){
        $shops=Shop::all();
        $data=[];
        foreach ($shops as $key=>$value){
            $data[$key]['id']=$value->id;
            $data[$key]['shop_name']=$value->shop_name;
            $data[$key]['shop_img']=$value->shop_img;
            $data[$key]['shop_rating']=$value->shop_rating;
            $data[$key]['brand']=$value->brand;
            $data[$key]['on_time']=$value->on_time;
            $data[$key]['fengniao']=$value->fengniao;
            $data[$key]['bao']=$value->bao;
            $data[$key]['piao']=$value->piao;
            $data[$key]['zhun']=$value->zhun;
            $data[$key]['start_send']=$value->start_send;
            $data[$key]['send_cost']=$value->send_cost;
            $distance=mt_rand(1,10000);
            $data[$key]['distance']=$distance;
            $estimate_time=ceil($distance/100);
            $data[$key]['estimate_time']=$estimate_time;
            $data[$key]['notice']=$value->notice;
            $data[$key]['discount']=$value->discount;
        }
        echo json_encode($data);
    }
    //某个商家
    public function shop(Request $request){
        $shop=Shop::where('id','=',$request->id)->first();
        $data=[];
        $data['id']=$shop->id;
        $data['shop_name']=$shop->shop_name;
        $data['shop_img']=$shop->shop_img;
        $data['shop_rating']=$shop->shop_rating;
        $data['service_code']=$shop->service_code;
        $service_code=mt_rand(1,50)/10;
        $data['service_code']=$service_code;
        $foods_code=mt_rand(1,50)/10;
        $data['foods_code']=$foods_code;
        $high_or_low=mt_rand(0,1);
        $data['high_or_low']=$high_or_low;
        $h_l_percent=mt_rand(1,99);
        $data['h_l_percent']=$h_l_percent;
        $data['brand']=$shop->brand;
        $data['on_time']=$shop->on_time;
        $data['fengniao']=$shop->fengniao;
        $data['bao']=$shop->bao;
        $data['piao']=$shop->piao;
        $data['zhun']=$shop->zhun;
        $data['start_send']=$shop->start_send;
        $data['send_cost']=$shop->send_cost;
        $distance=mt_rand(1,10000);
        $data['distance']=$distance;
        $estimate_time=ceil($distance/100);
        $data['estimate_time']=$estimate_time;
        $data['notice']=$shop->notice;
        $data['discount']=$shop->discount;
        //评价
        $evaluate=[[
                "user_id"=>12344,
                "username"=> "w******k",
                "user_img"=>"http://www.homework.com/images/slider-pic4.jpeg",
                "time"=>"2017-2-22",
                "evaluate_code"=>1,
                "send_time"=>30,
                "evaluate_details"=>"不怎么好吃"
             ],[
                "user_id"=>12344,
                "username"=> "w******e",
                "user_img"=>"http://www.homework.com/images/slider-pic4.jpeg",
                "time"=>"2017-2-23",
                "evaluate_code"=>4.5,
                "send_time"=>30,
                "evaluate_details"=>"很好吃"
            ]
        ];
        foreach ($evaluate as $value){
            $data['evaluate'][]=$value;
        }
        //菜品与菜品分类
        $menu_categories=MenuCategory::all()->where('shop_id','=',$shop->id);
        $menus=Menu::all()->where('shop_id','=',$shop->id);
        $commodity=[];
        foreach ($menu_categories as $key=>$menu_category){
            $commodity[$key]['description']=$menu_category->description;
            $commodity[$key]['is_selected']=$menu_category->is_selected;
            $commodity[$key]['name']=$menu_category->name;
            $commodity[$key]['type_accumulation']=$menu_category->id;
            $k=0;
            foreach ($menus as $menu){
                if ($menu->category_id==$menu_category->id){
                    $commodity[$key]['goods_list'][$k]['goods_id']=$menu->id;
                    $commodity[$key]['goods_list'][$k]['goods_name']=$menu->goods_name;
                    $commodity[$key]['goods_list'][$k]['rating']=$menu->rating;
                    $commodity[$key]['goods_list'][$k]['goods_price']=$menu->goods_price;
                    $commodity[$key]['goods_list'][$k]['description']=$menu->description;
                    $commodity[$key]['goods_list'][$k]['month_sales']=$menu->month_sales;
                    $commodity[$key]['goods_list'][$k]['rating_count']=$menu->rating_count;
                    $commodity[$key]['goods_list'][$k]['tips']=$menu->tips;
                    $commodity[$key]['goods_list'][$k]['satisfy_count']=$menu->satisfy_count;
                    $commodity[$key]['goods_list'][$k]['satisfy_rate']=$menu->satisfy_rate;
                    $commodity[$key]['goods_list'][$k]['goods_img']=$menu->goods_img;
                    $k++;
                }
            }
        }


        $data['commodity']=$commodity;
        echo json_encode($data);

    }
}
