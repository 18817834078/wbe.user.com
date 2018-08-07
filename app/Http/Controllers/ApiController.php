<?php

namespace App\Http\Controllers;

use App\model\Address;
use App\model\Cart;
use App\model\Menu;
use App\model\MenuCategory;
use App\model\Order;
use App\model\OrderGood;
use App\model\Shop;
use App\SignatureHelper;
use App\SphinxClient;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use function Psy\debug;

class ApiController extends Controller
{
    //所有商家
    public function shops(Request $request){
        $search_id=[];
        $data = [];
        $shops=null;
        if (!isset($request->keyword)){

            if (!Redis::get('shops_json')) {
                $shops = Shop::all();

                foreach ($shops as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['shop_name'] = $value->shop_name;
                    $data[$key]['shop_img'] = $value->shop_img;
                    $data[$key]['shop_rating'] = $value->shop_rating;
                    $data[$key]['brand'] = $value->brand;
                    $data[$key]['on_time'] = $value->on_time;
                    $data[$key]['fengniao'] = $value->fengniao;
                    $data[$key]['bao'] = $value->bao;
                    $data[$key]['piao'] = $value->piao;
                    $data[$key]['zhun'] = $value->zhun;
                    $data[$key]['start_send'] = $value->start_send;
                    $data[$key]['send_cost'] = $value->send_cost;
                    $distance = mt_rand(1, 10000);
                    $data[$key]['distance'] = $distance;
                    $estimate_time = ceil($distance / 100);
                    $data[$key]['estimate_time'] = $estimate_time;
                    $data[$key]['notice'] = $value->notice;
                    $data[$key]['discount'] = $value->discount;
                }
                Redis::setex('shops_json',3600*24 ,json_encode($data));
            }
            return Redis::get('shops_json');
        }else{
            $cl = new SphinxClient ();
            $cl->SetServer ( '127.0.0.1', 9312);
//$cl->SetServer ( '10.6.0.6', 9312);
//$cl->SetServer ( '10.6.0.22', 9312);
//$cl->SetServer ( '10.8.8.2', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
// $cl->SetMatchMode ( SPH_MATCH_ANY);
            $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);
            $cl->SetLimits(0, 1000);
            $info = $request->keyword;
            $res = $cl->Query($info, 'shops');//shopstore_search
            if (isset($res['matches'])) {
                foreach ($res['matches'] as $val) {
                    $search_id[] = $val['id'];
                }
            }
                $shops=Shop::whereIn('id',$search_id)->get();
                foreach ($shops as $key => $value) {
                    $data[$key]['id'] = $value->id;
                    $data[$key]['shop_name'] = $value->shop_name;
                    $data[$key]['shop_img'] = $value->shop_img;
                    $data[$key]['shop_rating'] = $value->shop_rating;
                    $data[$key]['brand'] = $value->brand;
                    $data[$key]['on_time'] = $value->on_time;
                    $data[$key]['fengniao'] = $value->fengniao;
                    $data[$key]['bao'] = $value->bao;
                    $data[$key]['piao'] = $value->piao;
                    $data[$key]['zhun'] = $value->zhun;
                    $data[$key]['start_send'] = $value->start_send;
                    $data[$key]['send_cost'] = $value->send_cost;
                    $distance = mt_rand(1, 10000);
                    $data[$key]['distance'] = $distance;
                    $estimate_time = ceil($distance / 100);
                    $data[$key]['estimate_time'] = $estimate_time;
                    $data[$key]['notice'] = $value->notice;
                    $data[$key]['discount'] = $value->discount;
                }
                return json_encode($data);


        }




    }
    //某个商家
    public function shop(Request $request){
        if (!Redis::get('shop_json'.$request->id)) {
            $shop = Shop::where('id', '=', $request->id)->first();
            $data = [];
            $data['id'] = $shop->id;
            $data['shop_name'] = $shop->shop_name;
            $data['shop_img'] = $shop->shop_img;
            $data['shop_rating'] = $shop->shop_rating;
            $data['service_code'] = $shop->service_code;
            $service_code = mt_rand(1, 50) / 10;
            $data['service_code'] = $service_code;
            $foods_code = mt_rand(1, 50) / 10;
            $data['foods_code'] = $foods_code;
            $high_or_low = mt_rand(0, 1);
            $data['high_or_low'] = $high_or_low;
            $h_l_percent = mt_rand(1, 99);
            $data['h_l_percent'] = $h_l_percent;
            $data['brand'] = $shop->brand;
            $data['on_time'] = $shop->on_time;
            $data['fengniao'] = $shop->fengniao;
            $data['bao'] = $shop->bao;
            $data['piao'] = $shop->piao;
            $data['zhun'] = $shop->zhun;
            $data['start_send'] = $shop->start_send;
            $data['send_cost'] = $shop->send_cost;
            $distance = mt_rand(1, 10000);
            $data['distance'] = $distance;
            $estimate_time = ceil($distance / 100);
            $data['estimate_time'] = $estimate_time;
            $data['notice'] = $shop->notice;
            $data['discount'] = $shop->discount;
            //评价
            $evaluate = [[
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http://www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 1,
                "send_time" => 30,
                "evaluate_details" => "不怎么好吃"
            ], [
                "user_id" => 12344,
                "username" => "w******e",
                "user_img" => "http://www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-23",
                "evaluate_code" => 4.5,
                "send_time" => 30,
                "evaluate_details" => "很好吃"
            ]
            ];
            foreach ($evaluate as $value) {
                $data['evaluate'][] = $value;
            }
            //菜品与菜品分类
            $menu_categories = MenuCategory::all()->where('shop_id', '=', $shop->id);
            $menus = Menu::all()->where('shop_id', '=', $shop->id);
            $commodity = [];
            foreach ($menu_categories as $key => $menu_category) {
                $commodity[$key]['description'] = $menu_category->description;
                $commodity[$key]['is_selected'] = $menu_category->is_selected;
                $commodity[$key]['name'] = $menu_category->name;
                $commodity[$key]['type_accumulation'] = $menu_category->id;
                $k = 0;
                foreach ($menus as $menu) {
                    if ($menu->category_id == $menu_category->id) {
                        $commodity[$key]['goods_list'][$k]['goods_id'] = $menu->id;
                        $commodity[$key]['goods_list'][$k]['goods_name'] = $menu->goods_name;
                        $commodity[$key]['goods_list'][$k]['rating'] = $menu->rating;
                        $commodity[$key]['goods_list'][$k]['goods_price'] = $menu->goods_price;
                        $commodity[$key]['goods_list'][$k]['description'] = $menu->description;
                        $commodity[$key]['goods_list'][$k]['month_sales'] = $menu->month_sales;
                        $commodity[$key]['goods_list'][$k]['rating_count'] = $menu->rating_count;
                        $commodity[$key]['goods_list'][$k]['tips'] = $menu->tips;
                        $commodity[$key]['goods_list'][$k]['satisfy_count'] = $menu->satisfy_count;
                        $commodity[$key]['goods_list'][$k]['satisfy_rate'] = $menu->satisfy_rate;
                        $commodity[$key]['goods_list'][$k]['goods_img'] = $menu->goods_img;
                        $k++;
                    }
                }
            }

            $data['commodity'] = $commodity;
            Redis::setex('shop_json'.$request->id,3600*24,json_encode($data));
            //echo json_encode($data);
        }
        return Redis::get('shop_json'.$request->id);
    }
    //短信验证码
    public function sms(){
        $tel = request()->tel;
        $params = array();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIZ7U2WP9J7IGN";
        $accessKeySecret = "dLF5yzjj9XDmCDARp8e7nnniKWNVlV";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "刘云鹏";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_140600024";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $code=mt_rand(1000,9999);
        $params['TemplateParam'] = Array(
            "code" => $code,
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        //return $content;
        //dd($content);
        Redis::setex('tel'.$tel,600,$code);
        echo json_encode(['status'=>true,'message'=>'验证信息已发送']);

    }
    //注册
    public function join(Request $request){
        $status='true';
        $message='注册成功';
//        if ($request->sms!=Redis::get('tel'.$request->tel)){
//            $status='false';
//            $message='手机验证码错误';
//        }elseif(User::where('username','=',$request->username)->first()){
//            $status='false';
//            $message='此用户名已被注册';
//        }elseif(User::where('tel','=',$request->tel)->first()){
//            $status='false';
//            $message='此号码已被注册';
//        }else{
//            User::create([
//                'username'=>$request->username,
//                'password'=>bcrypt($request->password),
//                'tel'=>$request->tel
//            ]);
//        }
        $validator=Validator::make($request->all(),[
            'username'=>'required|unique:users',
            'tel'=>'required|unique:users',
            'sms'=>'required',
            'password'=>'required',
        ],[
            'username.required'=>'用户名不能为空!',
            'username.unique'=>'用户名已被注册',
            'tel.required'=>'电话号码不能为空',
            'tel.unique'=>'电话号码已被注册',
            'sms.required'=>'验证码不能为空',
            'password.required'=>'密码不能为空',
        ]);
        if ($validator->fails()) {
            $status='false';
            $message=$validator->errors()->first();
        }elseif($request->sms!=Redis::get('tel'.$request->tel)){
            $status='false';
            $message='手机验证码错误';
        }else{
            User::create([
                'username'=>$request->username,
                'password'=>bcrypt($request->password),
                'tel'=>$request->tel,
                'status'=>1,
            ]);
        }
        echo json_encode(['status'=>$status,'message'=>$message]);
    }
    //登录
    public function login(Request $request){
        $status='false';
        $message='用户名或密码错误';
        $user_id=null;
        $username=null;

        if (!$request->name){
            $message='请输入用户名';
        }elseif(!$request->password){
            $message='请输入密码';
        }elseif(!User::where('username','=',$request->name)->first()->status){
            $message='此账号已被禁用';
        }elseif(Auth::attempt(['username' => $request->name, 'password' => $request->password])){
            $user=User::where('username','=',$request->name)->first();
            $status='true';
            $message='登录成功';
            $user_id=$user->id;
            $username=$user->username;
        }
        echo json_encode(['status'=>$status,'message'=>$message,'user_id'=>$user_id,'username'=>$username]);

    }
    //修改密码
    public function change_pw(Request $request){
        $status='true';
        $message='密码修改成功';
        if(Hash::check($request->oldPassword,auth()->user()->password)){
            User::where('id','=',auth()->user()->id)->update(['password'=>bcrypt($request->newPassword)]);
        }else{
            $status='false';$message='旧密码输入错误';
        }
        echo json_encode(['status'=>$status,'message'=>$message]);
    }
    //重置密码
    public function forget_password(Request $request){
        $status='true';
        $message='密码修改成功';
        $validator=Validator::make($request->all(),[
            'tel'=>'required',
            'sms'=>'required',
            'password'=>'required',
        ],[
            'tel.required'=>'电话号码不能为空',
            'sms.required'=>'验证码不能为空',
            'password.required'=>'密码不能为空',
        ]);
        if ($validator->fails()){
            $status='false';
            $message=$validator->errors()->first();
        }elseif($request->sms!=Redis::get('tel'.$request->tel)){
            $status='false';
            $message='验证码错误';
        }else{
            User::where('tel','=',$request->tel)->update([
                'password'=>bcrypt($request->password)
            ]);
        }
        return json_encode(['status'=>$status,'message'=>$message]);
    }
    //添加地址
    public function add_address(Request $request){
     $status='true';
     $message='地址添加成功';
        $validator=Validator::make($request->all(),[
            'name'=>'required|max:11',
            'tel'=>'required|max:11',
            'provence'=>'required|max:10',
            'city'=>'required|max:10',
            'area'=>'required|max:10',
            'detail_address'=>'required|max:10',
        ],[
            'name.required'=>'收货人姓名不能为空',
            'name.max'=>'收货人姓名过长',
            'tel.required'=>'电话号码不能为空',
            'tel.max'=>'错误的号码',
            'provence.required'=>'省字段不能为空',
            'provence.max'=>'省字段字符过长',
            'city.required'=>'市字段不能为空',
            'city.max'=>'市字段字符过长',
            'area.required'=>'县字段不能为空',
            'area.max'=>'县字段字符过长',
            'detail_address.required'=>'详细地址不能为空',
            'detail_address.max'=>'详细地址过长',
        ]);
        if ($validator->fails()) {
            $status='false';
            $message=$validator->errors()->first();
        }else{
            $is_default=0;
            if (!Address::where([['is_default','=','1'],['user_id','=',auth()->user()->id]])->first()){
                $is_default=1;
            }
            Address::create([
                'user_id'=>auth()->user()->id,
                'province'=>$request->provence,
                'city'=>$request->city,
                'county'=>$request->area,
                'address'=>$request->detail_address,
                'tel'=>$request->tel,
                'name'=>$request->name,
                'is_default'=>$is_default,
            ]);
        }
        echo json_encode(['status'=>$status,'message'=>$message]);
    }
    //地址列表
    public function address_list(){
        $address=Address::where('user_id','=',auth()->user()->id)->get();
        $address_list=[];
        foreach ($address as $key=>$value){
            $address_list[$key]['id']=$value['id'];
            $address_list[$key]['provence']=$value['province'];
            $address_list[$key]['city']=$value['city'];
            $address_list[$key]['area']=$value['county'];
            $address_list[$key]['detail_address']=$value['address'];
            $address_list[$key]['name']=$value['name'];
            $address_list[$key]['tel']=$value['tel'];
        }
        return json_encode($address_list);
    }
    //详细地址
    public function address(Request $request){
        $address=Address::where('id','=',$request->id)->first();
        $data['id']=$address['id'];
        $data['provence']=$address['province'];
        $data['city']=$address['city'];
        $data['area']=$address['county'];
        $data['detail_address']=$address['address'];
        $data['name']=$address['name'];
        $data['tel']=$address['tel'];
        return json_encode($data);
    }
    //修改地址
    public function edit_address(Request $request){
        $status='true';
        $message='地址修改成功';
        $validator=Validator::make($request->all(),[
            'name'=>'required|max:11',
            'tel'=>'required|max:11',
            'provence'=>'required|max:10',
            'city'=>'required|max:10',
            'area'=>'required|max:10',
            'detail_address'=>'required|max:10',
        ],[
            'name.required'=>'收货人姓名不能为空',
            'name.max'=>'收货人姓名过长',
            'tel.required'=>'电话号码不能为空',
            'tel.max'=>'错误的号码',
            'provence.required'=>'省字段不能为空',
            'provence.max'=>'省字段字符过长',
            'city.required'=>'市字段不能为空',
            'city.max'=>'市字段字符过长',
            'area.required'=>'县字段不能为空',
            'area.max'=>'县字段字符过长',
            'detail_address.required'=>'详细地址不能为空',
            'detail_address.max'=>'详细地址过长',
        ]);
        if ($validator->fails()) {
            $status='false';
            $message=$validator->errors()->first();
        }else{
            Address::where('id','=',$request->id)->update([
                'province'=>$request->provence,
                'city'=>$request->city,
                'county'=>$request->area,
                'address'=>$request->detail_address,
                'tel'=>$request->tel,
                'name'=>$request->name,
            ]);
        }
        echo json_encode(['status'=>$status,'message'=>$message]);
    }
    //加入购物车
    public function add_cart(Request $request){
        $status='true';
        $message='添加成功';
        if (!$request->goodsList||!$request->goodsCount){
            $status='false';
            $message='空的购物信息';
        }else{
            $len=count($request->goodsList);
            Cart::where('user_id','=',auth()->user()->id)->delete();
            for($i=0;$i<$len;$i++){
                Cart::create([
                    'user_id'=>auth()->user()->id,
                    'goods_id'=>$request->goodsList[$i],
                    'amount'=>$request->goodsCount[$i],
                ]);
            }
        }

        echo json_encode(['status'=>$status,'message'=>$message]);
    }
    //展示购物车商品
    public function cart(){
        $cart=Cart::where('user_id','=',auth()->user()->id)->get();
        $data=[];
        $data['totalCost']=0;
        foreach ($cart as $key=>$value){
//            $data['goods_list'][$key]['goods_id']=$value['goods_id'];
//            $data['goods_list'][$key]['goods_name']=$value->menu->goods_name;
//            $data['goods_list'][$key]['goods_img']=$value->menu->goods_img;
//            $data['goods_list'][$key]['amount']=$value->amount;
//            $data['goods_list'][$key]['goods_price']=$value->menu->goods_price;
//            $data['totalCost']+=$value->amount*$value->menu->goods_price;
            $goods=Menu::where('id','=',$value['goods_id'])->first();
            $data['goods_list'][$key]['goods_id']=$value['goods_id'];
            $data['goods_list'][$key]['goods_name']=$goods['goods_name'];
            $data['goods_list'][$key]['goods_img']=$goods['goods_img'];
            $data['goods_list'][$key]['amount']=$value->amount;
            $data['goods_list'][$key]['goods_price']=$goods['goods_price'];
            $data['totalCost']+=$value['amount']*$goods['goods_price'];
        }

        return json_encode($data);
    }

    //生成订单
    public function add_order(Request $request){
        $status='true';
        $message='订单已生成';
        $order_id=null;
        //商品
        $cart=Cart::where('user_id','=',auth()->user()->id)->get();
        $shop_name='商店';
        //地址
        $address=Address::where('id','=',$request->address_id)->first();
        if (!$cart){
            $status='false';
            $message='请先添加商品';
            $order_id=null;
        }else{
            $shop=Shop::where('id','=',$cart[0]->menu->shop_id)->first();
            $shop_name=$shop->shop_name;
            $shop_email=$shop->shop_user->email;

            $total=0;
            foreach ($cart as $value){
                $total+=$value['amount']*$value->menu->goods_price;
            }
            $data=DB::transaction(function () use ($cart,$address,$total,$order_id) {
                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'shop_id' => $cart[0]->menu->shop_id,
                    'sn' => date('Ymd', time()) . uniqid(),
                    'province' => $address->province,
                    'city' => $address->city,
                    'county' => $address->county,
                    'address' => $address->address,
                    'tel' => $address->tel,
                    'name' => $address->name,
                    'total' => $total,
                    'status' => 0,
                    'out_trade_no' => uniqid()
                ]);
                $order_id = $order->id;
                $goods_str='';
                foreach ($cart as $val) {
                    OrderGood::create([
                        'order_id' => $order_id,
                        'goods_id' => $val->goods_id,
                        'amount' => $val->amount,
                        'goods_name' => $val->menu->goods_name,
                        'goods_img' => $val->menu->goods_img,
                        'goods_price' => $val->menu->goods_price,
                    ]);
                    Menu::where('id','=',$val->goods_id)->increment('month_sales',$val->amount);
                    $goods_str.=$val->menu->goods_name.',';
                }
                return ['order_id'=>$order_id,'goods_str'=>$goods_str];
            });
        }
        //发送短信
        $params = array();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIZ7U2WP9J7IGN";
        $accessKeySecret = "dLF5yzjj9XDmCDARp8e7nnniKWNVlV";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = auth()->user()->tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "刘云鹏";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_141190244";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array(
            "shop" => $shop_name,
            "menus"=>substr($data['goods_str'],0,-1)
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        //发送完毕
        //发送邮件
        Mail::raw('你有一条新订单',function ($message) use($shop_email){
            $message->subject('订单通知');
            $message->to($shop_email);
        });
        //发送完毕
        return json_encode(['status'=>$status,'message'=>$message,'order_id'=>$data['order_id']]);
    }
    //返回需要用的订单格式数组,供其他方法使用
    public function get_order($order_id){
        $order=Order::where('id','=',$order_id)->first();
        $order_status='';
        if ($order->status==-1){$order_status='已取消';}
        if ($order->status==0){$order_status='待付款';}
        if ($order->status==1){$order_status='待发货';}
        if ($order->status==2){$order_status='待确认';}
        if ($order->status==3){$order_status='完成';}
        $data=[];
        $data['id']=$order_id;
        $data['order_code']=$order->sn;
        $data['order_birth_time']=substr($order->created_at,0,16);
        $data['order_status']=$order_status;
        $data['shop_id']=$order->shop_id;
        $data['shop_name']=$order->shop->shop_name;
        $data['shop_img']=$order->shop->shop_img;
        $order_price=0;
        $order_goods=OrderGood::where('order_id','=',$order_id)->get();
        foreach ($order_goods as $key=>$value){
//            $data['goods_list'][$key]['goods_id']=$value->goods_id;
//            $data['goods_list'][$key]['goods_name']=$value->goods_name;
//            $data['goods_list'][$key]['goods_img']=$value->goods_img;
//            $data['goods_list'][$key]['amount']=$value->amount;
//            $data['goods_list'][$key]['goods_price']=$value->goods_price;
            $order_price+=$value->amount*$value->goods_price;
            unset($value['id'],$value['order_id'],$value['created_at'],$value['updated_at']);
        }
        $data['goods_list']=$order_goods;
        $data['order_price']=$order_price;
        $data['order_address']=$order->province.'-'.$order->city.'-'.$order->county.'-'.$order->address;
        return $data;
    }
    //查看订单
    public function order(Request $request){
        return json_encode($this->get_order($request->id)) ;
    }
    //订单列表
    public function order_list(Request $request){
        $orders=Order::where('user_id','=',auth()->user()->id)->get();
        $data=[];
        foreach ($orders as $value){
            $data[]=$this->get_order($value->id);
        }
        return json_encode($data);
    }

}
