<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Customer;
use App\Bank;
use App\CustomerGroup;
use App\Warehouse;
use App\Biller;
use App\Brand;
use App\Category;
use App\Product;  
use App\VehicleProduct;
use App\VehicleInfo;
use App\VehicleBrand;
use App\VehicleCategory;
use App\Vehicle;
use App\Unit; 
use App\Tax;
use App\Sale;
use App\Delivery;
use App\PosSetting;
use App\Product_Sale;
use App\Product_Warehouse;
use App\Payment;
use App\Account;
use App\Coupon;
use App\GiftCard;
use App\PaymentWithCheque;
use App\PaymentWithGiftCard;
use App\PaymentWithCreditCard;
use App\PaymentWithPaypal;
use App\User;
use App\Variant;
use App\ProductVariant;
use App\CashRegister;
use App\Returns;
use App\Expense;
use App\ProductPurchase;
use App\ProductBatch;
use App\Purchase;
use App\RewardPointSetting;
use DB;
use App\GeneralSetting;
use Stripe\Stripe;
use Carbon\Carbon; 
use NumberToWords\NumberToWords;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;
use GeniusTS\HijriDate\Date;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if($request->input('warehouse_id'))
                $warehouse_id = $request->input('warehouse_id');
            else
                $warehouse_id = 0;

            if($request->input('sale_status'))
                $sale_status = $request->input('sale_status');
            else
                $sale_status = 0;

            if($request->input('payment_status'))
                $payment_status = $request->input('payment_status');
            else
                $payment_status = 0;

            if($request->input('starting_date')) {
                $starting_date = $request->input('starting_date');
                $ending_date = $request->input('ending_date');
            }
            else {
                $starting_date = date("Y-m-d", strtotime(date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d') )))));
                $ending_date = date("Y-m-d");
            }

            $lims_gift_card_list = GiftCard::where("is_active", true)->get();
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_reward_point_setting_data = RewardPointSetting::latest()->first();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_account_list = Account::where('is_active', true)->get();
            $banks = Bank::where('is_active', true)->orderBy('title','asc')->get();
     
            return view('sale.index',compact('starting_date', 'banks', 'ending_date', 'warehouse_id', 'sale_status', 'payment_status', 'lims_gift_card_list', 'lims_pos_setting_data', 'lims_reward_point_setting_data', 'lims_account_list', 'lims_warehouse_list', 'all_permission'));
            }
           else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    public function saleData(Request $request)
    {
        $columns = array( 
            1 => 'created_at',  
            2 => 'reference_no',
            7 => 'grand_total',
            8 => 'paid_amount',
        );
        
        $warehouse_id = $request->input('warehouse_id');
        $sale_status = $request->input('sale_status');
        $payment_status = $request->input('payment_status');

        $q = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))->whereDate('created_at', '<=' ,$request->input('ending_date'));

        if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $q = $q->where('user_id', Auth::id());
        if($sale_status)
            $q = $q->where('type', $sale_status);
        if($payment_status)
            $q = $q->where('payment_status', $payment_status);
        
        $totalData = $q->count();
        $totalFiltered = $totalData;

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'sales.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))) {
            $q = Sale::with('customer', 'warehouse', 'user')
                ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $q = $q->where('user_id', Auth::id());
            if($warehouse_id)
                $q = $q->where('warehouse_id', $warehouse_id);
            if($sale_status)
                $q = $q->where('type', $sale_status);
            if($payment_status)
                $q = $q->where('payment_status', $payment_status);
            $sales = $q->get();
        }
        else
          {
            $search = $request->input('search.value');
            $q = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
                ->whereDate('sales.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir);
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $sales =  $q->select('sales.*')
                            ->with('customer', 'warehouse', 'user')
                            ->where('sales.user_id', Auth::id())
                            ->orwhere([
                                ['sales.reference_no', 'LIKE', "%{$search}%"],
                                ['sales.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['customers.name', 'LIKE', "%{$search}%"],
                                ['sales.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['customers.phone_number', 'LIKE', "%{$search}%"],
                                ['sales.user_id', Auth::id()]
                            ])->get();
                $totalFiltered = $q->where('sales.user_id', Auth::id())
                                ->orwhere([
                                    ['sales.reference_no', 'LIKE', "%{$search}%"],
                                    ['sales.user_id', Auth::id()]
                                ])
                                ->orwhere([
                                    ['customers.name', 'LIKE', "%{$search}%"],
                                    ['sales.user_id', Auth::id()]
                                ])
                                ->orwhere([
                                    ['customers.phone_number', 'LIKE', "%{$search}%"],
                                    ['sales.user_id', Auth::id()]
                                ])->count();                               
            }
            else {
                $sales =  $q->select('sales.*')
                            ->with('customer', 'warehouse', 'user')
                            ->orwhere('sales.reference_no', 'LIKE', "%{$search}%")
                            ->orwhere('customers.name', 'LIKE', "%{$search}%")
                            ->orwhere('customers.phone_number', 'LIKE', "%{$search}%")
                            ->get();

                $totalFiltered = $q->orwhere('sales.reference_no', 'LIKE', "%{$search}%")
                                ->orwhere('customers.name', 'LIKE', "%{$search}%")
                                ->orwhere('customers.phone_number', 'LIKE', "%{$search}%")
                                ->count();
            }
        }
        $data = array();
        if(!empty($sales))
        {
            foreach ($sales as $key=>$sale)
            {
                $nestedData['id'] = $sale->id;
                $nestedData['key'] = $key;
                $nestedData['date'] = date(config('date_format'), strtotime($sale->created_at->toDateString()));
                $nestedData['reference_no'] = $sale->reference_no;
                if($sale->type == 2){
                    $nestedData['type'] = '<div class="justify-content-center"> <li style="color:#007bff; font-size:14px;" class="fa fa-car"> </i> </div>';
                }
                else
                {
                    $nestedData['type'] = '<div class="justify-content-center"> <li style="color:green; font-size:14px;" class="fa fa-product-hunt"> </i> </div>';
                }
                $nestedData['customer'] = $sale->customer->name;
                $nestedData['item'] = $sale->item;
                $nestedData['quantity'] = $sale->total_qty;

                if($sale->payment_status == 1){
                    $nestedData['payment_status'] = '<div class="badge badge-success">'.trans('file.Completed').'</div>';
                    $sale_status = trans('file.Completed');
                }
                else{ 
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                    $sale_status = trans('file.Pending');
                }

                $nestedData['grand_total'] = number_format($sale->grand_total, 2);
                $nestedData['paid_amount'] = number_format($sale->paid_amount, 2);
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                                <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                            </li>';
                            if(in_array("sale-payment-index", $request['all_permission']) && $sale->paid_amount > 0)
                            $nestedData['options'] .= 
                                '<li>
                                    <button type="button" class="get-payment btn btn-link" data-id = "'.$sale->id.'"><i class="fa fa-money"></i> '.trans('file.View Payment').'</button>
                                </li>';
                                if(in_array("sale-payment-add", $request['all_permission']) && $sale->paid_amount < $sale->grand_total)
                                $nestedData['options'] .= 
                                    '<li>
                                        <button type="button" class="add-payment btn btn-link" data-id = "'.$sale->id.'" data-toggle="modal" data-target="#add-payment"><i class="fa fa-plus"></i> '.trans('file.Add Payment').'</button>
                                    </li>'; 
                            if(in_array("sales-delete", $request['all_permission']) && $sale->paid_amount >= $sale->grand_total)
                            $nestedData['options'] .= \Form::open(["route" => ["sales.destroy", $sale->id], "method" => "DELETE"] ).'
                                    <li>
                                      <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button> 
                                    </li>'.\Form::close().'
                        </ul>
                    </div>';
                $nestedData['sale'] = array( '[ "'.date(config('date_format'), strtotime($sale->created_at->toDateString())).'"', ' "'.$sale->reference_no.'"', ' "'.$sale_status.'"', ' "'.$sale->customer->name.'"', ' "'.$sale->customer->phone_number.'"', ' "'.$sale->id.'"', ' "'.$sale->item.'"', ' "'.$sale->total_qty.'"', ' "'.$sale->total_tax.'"',  ' "'.$sale->total_price.'"',  ' "'.$sale->grand_total.'"', ' "'.$sale->paid_amount.'"', ' "'.preg_replace('/[\n\r]/', "<br>", $sale->sale_note).'"', ' "'.preg_replace('/[\n\r]/', "<br>", $sale->staff_note).'"', ' "'.$sale->user->name.'"', ' "'.$sale->user->email.'"', ' "'.$sale->user->phone.'"', ' "'.$sale->warehouse->name.'" ', ' "'.$sale->type.'"]'   
                );
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
            
        echo json_encode($json_data);
    }
 
    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')) {
            $lims_customer_list = Customer::where('is_active', true)->get();
            if(Auth::user()->role_id > 2) {
                $lims_warehouse_list = Warehouse::where([
                    ['is_active', true],
                    ['id', Auth::user()->warehouse_id]
                ])->get();
                $lims_biller_list = Biller::where([
                    ['is_active', true],
                    ['id', Auth::user()->biller_id]
                ])->get();
            }
            else {
                $lims_warehouse_list = Warehouse::where('is_active', true)->get();
                $lims_biller_list = Biller::where('is_active', true)->get();
            }

            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_reward_point_setting_data = RewardPointSetting::latest()->first();

            return view('sale.create',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_pos_setting_data', 'lims_tax_list', 'lims_reward_point_setting_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }                                                      
    public function store(Request $request)
    {   
        $data = $request->all();
      
       // dd($data);
        $data['user_id'] = Auth::id();
        $data['created_at'] = date("Y-m-d H:i:s");

    $kas='vh-' . date("Y-m-d") . '-'. date("hi-s");
            
    if($data['kasu_id']== 2)
     {
    $lims_sale_data = new Sale();
    $lims_sale_data->reference_no=$kas;
    $lims_sale_data->user_id=$data['user_id'];
    $lims_sale_data->customer_id=$data['customer_id'];
    $lims_sale_data->warehouse_id=$data['warehouse_id'];
    $lims_sale_data->type=2;
    if($data['deposit_bank_id'] != "")
    {
        $lims_sale_data->bank_id=$data['deposit_bank_id'];
    }
    $lims_sale_data->item=$data['item'];
    $lims_sale_data->total_qty=$data['total_qty'];
    $lims_sale_data->total_tax=$data['order_tax'];
    $lims_sale_data->total_price=$data['total_price'];
    $lims_sale_data->grand_total=$data['grand_total'];
    $lims_sale_data->sale_status=$data['paid_by_id'];
    if( $data['grand_total'] == $data['paid_amount'])
    {
        $lims_sale_data->payment_status=1;
    }
    else
    {
        $lims_sale_data->payment_status=2;
    }
   
    $lims_sale_data->paid_amount=$data['paid_amount'];
    $lims_sale_data->sale_note=$data['sale_note'];
    $lims_sale_data->staff_note=$data['payment_note'];
    $lims_sale_data->created_at=$data['created_at'];
    $lims_sale_data->save();
   

    $product_id = $data['product_id'];
    $product_code = $data['product_code'];
    $qty = $data['qty'];
    $product_unit = $data['product-unit1'];
    $sale_unit = $data['sell-unit1'];
    $price = $data['pro_price'];
    $tax_rate = $data['tax_rate'];
    $total = $data['subtotal'];
    $vhcl_cha = $data['vehicle_cha'];
    $vhcl_eng = $data['vehicle_eng'];

    foreach ($product_id as $i => $id) {
      
        $prod_sale= new Product_Sale();
        $prod_sale->sale_id=$lims_sale_data->id ;
        $prod_sale->product_id=$id ;
        $prod_sale->type=2 ; 
        $prod_sale->qty=$qty[$i];
        $prod_sale->chassis_no = $vhcl_cha[$i];
        $prod_sale->engine_no = $vhcl_eng[$i];
        $prod_sale->sell_unit=$sale_unit[$i];
        $prod_sale->product_unit=$product_unit[$i];
        $prod_sale->price=$price[$i];
        $prod_sale->net_unit_price=$price[$i];
        $lims_product_data = VehicleProduct::find($id);
        $lims_product_data->qty -= $qty[$i];
        $lims_product_data->save();
        $vehicle = VehicleInfo::find($lims_product_data->vehicle_id);
        $vehicle->qty -= 1;
        $vehicle->save();
        $prod_sale->tax_rate=$tax_rate[$i];
        $prod_sale->tax=$total[$i] * $tax_rate[$i]/100;
        $prod_sale->total=$total[$i];
        $prod_sale->save();
    }
      if($data['paid_amount'] > 0)
      {
        $lims_payment_data = new Payment();
        $lims_payment_data->user_id = Auth::id();

        $lims_payment_data = new Payment();
        $lims_payment_data->user_id = Auth::id();

        if($data['paid_by_id'] == 1)
            $paying_method = 'Cash';
        elseif ($data['paid_by_id'] == 2) {
            $paying_method = 'Deposit';
        }  
        elseif ($data['paid_by_id'] == 3)
        {
            $paying_method = 'Credit';
        }       
        elseif($data['paid_by_id'] == 4)
        {
            $paying_method = 'Partial Credit'; 
        }
        $lims_payment_data->sale_id = $lims_sale_data->id;
        if($data['deposit_bank_id'] != "")
        {
            $lims_payment_data->bank_id=$data['deposit_bank_id'];
        }
        $data['payment_reference'] = 'pmt-'.date("Ymd").'-'.date("his");
        $lims_payment_data->payment_reference = $data['payment_reference'];
        $lims_payment_data->amount = $data['paid_amount'];
        $lims_payment_data->keri = $data['paying_amount'] - $data['paid_amount'];
        $lims_payment_data->paying_method = $paying_method;
        $lims_payment_data->payment_note = $data['payment_note'];
        $lims_payment_data->payment_date = Carbon::now();
        $lims_payment_data->save();
      }
     
      //  return redirect('sales/gen_invoice/' . $lims_sale_data->id)->with('message', $message);
      return redirect('/pos' )->with('message', "Vehicle sale added successfully !");
    }
    else
     {
        $kasa='pd-' . date("Y-m-d") . '-'. date("hi-s");
    $lims_sale_data = new Sale();
    $lims_sale_data->reference_no=$kasa;
    $lims_sale_data->user_id=$data['user_id'];
    $lims_sale_data->customer_id=$data['customer_id'];
    $lims_sale_data->type=1;
    $lims_sale_data->warehouse_id=$data['warehouse_id'];
    $lims_sale_data->item=$data['item'];
    $lims_sale_data->total_qty=$data['total_qty'];
    $lims_sale_data->total_tax=$data['order_tax'];
    $lims_sale_data->total_price=$data['total_price'];
    $lims_sale_data->grand_total=$data['grand_total'];
    $lims_sale_data->sale_status=$data['paid_by_id'];
    if( $data['grand_total'] == $data['paid_amount'])
    {
        $lims_sale_data->payment_status=1;
    }
    else
    {
        $lims_sale_data->payment_status=2;
    }
   
    $lims_sale_data->paid_amount=$data['paid_amount'];
    $lims_sale_data->sale_note=$data['sale_note'];
    $lims_sale_data->staff_note=$data['payment_note'];
    $lims_sale_data->created_at=$data['created_at'];
    $lims_sale_data->save();
    $product_id = $data['product_id'];
    $type = $data['kasu_id'];
    $product_code = $data['product_code'];
    $qty = $data['qty'];
    $product_unit = $data['product-unit1'];
    $sale_unit = $data['sell-unit1'];
    $price = $data['pro_price'];
    $tax_rate = $data['tax_rate'];
    $total = $data['subtotal'];

    foreach ($product_id as $i => $id) {
      
        $prod_sale= new Product_Sale();
        $prod_sale->sale_id=$lims_sale_data->id ;
        $prod_sale->product_id=$id ;
        $prod_sale->type=1 ;
        $prod_sale->qty=$qty[$i];
        $prod_sale->sell_unit=$sale_unit[$i];
        $prod_sale->product_unit=$product_unit[$i];
        $prod_sale->price=$price[$i];
        if($product_unit[$i] == $sale_unit[$i])
        {
            $lims_product_data = Product::find($id);
            $prod_sale->net_unit_price=$price[$i];
            $lims_product_data->qty -= $qty[$i];
            $lims_product_data->save();
        }
        else if($product_unit[$i]=="Dozen" && $sale_unit[$i] == "Piece" )
        {
            $lims_product_data = Product::find($id);
            $prod_sale->net_unit_price=$price[$i]/12;
            $lims_product_data->qty -= $qty[$i]/12;
            $lims_product_data->dozen_no -= $qty[$i]/12;
            $lims_product_data->save();

        }
        else if($product_unit[$i]=="Carton" && $sale_unit[$i] == "Piece")
        {
            $lims_product_data = Product::find($id);
            $prod_sale->net_unit_price=$price[$i]/$lims_product_data->piece_in_carton;
            $lims_product_data->qty -= $qty[$i]/$lims_product_data->piece_in_carton;
            $lims_product_data->carton_no -= $qty[$i]/$lims_product_data->piece_in_carton;
            $lims_product_data->save();
        }
        $prod_sale->tax_rate=$tax_rate[$i];
        $prod_sale->tax=$total[$i] * 0.15;
        $prod_sale->total=$total[$i];
        $prod_sale->save();
      

      }
    if($data['paid_amount'] > 0)
    {
      $lims_payment_data = new Payment();
      $lims_payment_data->user_id = Auth::id();

      $lims_payment_data = new Payment();
      $lims_payment_data->user_id = Auth::id();

      if($data['paid_by_id'] == 1)
          $paying_method = 'Cash';
      elseif ($data['paid_by_id'] == 2) {
          $paying_method = 'Deposit';
      }
      elseif ($data['paid_by_id'] == 3)
      {
          $paying_method = 'Credit';
      }       
      elseif($data['paid_by_id'] == 4)
      {
          $paying_method = 'Partial Credit'; 
      }
      $lims_payment_data->sale_id = $lims_sale_data->id;
      if($data['deposit_bank_id'] != "")
      {
          $lims_payment_data->bank_id=$data['deposit_bank_id'];
      }
      $data['payment_reference'] = 'spr-'.date("Ymd").'-'.date("his");
      $lims_payment_data->payment_reference = $data['payment_reference'];
      $lims_payment_data->amount = $data['paid_amount'];
      $lims_payment_data->keri = $data['paying_amount'] - $data['paid_amount'];
      $lims_payment_data->paying_method = $paying_method;
      $lims_payment_data->payment_note = $data['payment_note'];
      $lims_payment_data->payment_date = Carbon::now();
      $lims_payment_data->save();
    }
      //  return redirect('sales/gen_invoice/' . $lims_sale_data->id)->with('message', $message);
      return redirect('/pos' )->with('message', "Product sale added successfully !");
     }
      
      
    }
    public function getProduct($id)
    {
        $lims_product_warehouse_data = Product::
        where([
            ['is_active', true],
            ['warehouse_id', $id],
            ['qty', '>', 0]
        ])->get();
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_unit=[];
        $product_piece =[];
        $product_id =[];
        $product_data = [];
        //product without variant
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
          
            $product_code[] =  $product_warehouse->code;
            $product_name[] = $product_warehouse->name;
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $product_id[] = $product_warehouse->id;
            $product_unit[] = $product_warehouse->unit_id;
            $product_piece[] = $product_warehouse->piece_in_carton;
        }
  
        $product_data = [$product_code, $product_name, $product_qty, $product_id, $product_unit, $product_price, $product_piece ];
        return $product_data;
    }

    public function posSale()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';

            $lims_customer_list = Customer::where('is_active', true)->orderBy('created_at','desc')->get();
            $vehicles_brand = VehicleBrand::where('is_active', true)->get();
            $vehicles_category = VehicleCategory::where('is_active', true)->get();
            $lims_customer_list1 = Bank::where('is_active', true)->orderBy('title', 'asc')->get();
            $lims_customer_group_all = CustomerGroup::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_reward_point_setting_data = RewardPointSetting::latest()->first();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $ware=Auth::user()->warehouse_id;
            if($ware != "" && $ware != null)
            {
                $lims_product_list = Product::select('id', 'name', 'code')->ActiveFeatured()->where('warehouse_id',$ware)->where('qty', '>',0)->whereNull('is_variant')->get();
            }
            else
            {
                $lims_product_list = Product::select('id', 'name', 'code')->ActiveFeatured()->where('qty', '>',0)->whereNull('is_variant')->get();
            }
            if($ware != "" && $ware != null)
            {
                $vehicles= VehicleProduct::select('id', 'name', 'chassis_no')->where('is_active', true)->where('warehouse_id',$ware)->where('qty', '>',0)->get();
            }
            else
            {
                $vehicles= VehicleProduct::select('id', 'name', 'chassis_no')->where('is_active', true)->where('qty', '>',0)->get();
            }
            $product_number = count($lims_product_list);
            $vehicle_number = count($vehicles);
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_brand_list = Brand::where('is_active',true)->get();
            $lims_category_list = Category::where('is_active',true)->get();
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $recent_sale = Sale::where([
                    ['sale_status', 1],
                    ['user_id', Auth::id()]
                ])->orderBy('id', 'desc')->take(10)->get();
                $recent_draft = Sale::where([
                    ['sale_status', 3],
                    ['user_id', Auth::id()]
                ])->orderBy('id', 'desc')->take(10)->get();
            }
            else {
                $recent_sale = Sale::where('sale_status', 1)->orderBy('id', 'desc')->take(10)->get();
                $recent_draft = Sale::where('sale_status', 3)->orderBy('id', 'desc')->take(10)->get();
            }
            $flag = 0;

            return view('sale.pos', compact('all_permission','vehicles_category','vehicles_brand', 'vehicles', 'lims_customer_list1', 'lims_customer_list', 'lims_customer_group_all', 'lims_warehouse_list',  'lims_product_list', 'product_number', 'vehicle_number', 'lims_tax_list',  'lims_pos_setting_data', 'lims_brand_list', 'lims_category_list', 'recent_sale', 'recent_draft', 'flag'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getProductByFilter($category_id, $brand_id)
    {
        $data = [];
        $kasu=Auth::user()->warehouse_id;
        if(($category_id != 0) && ($brand_id != 0) && ($kasu != null) ){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.featured', true],
                                    ['products.category_id', $category_id],
                                    ['products.warehouse_id', $kasu],
                                    ['brand_id', $brand_id]
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.is_active', true],
                                    ['products.featured', true],
                                    ['products.warehouse_id', $kasu],
                                    ['brand_id', $brand_id]
                                ])->select('products.name', 'products.code')->get();
        }
        elseif(($category_id != 0) && ($brand_id != 0) && ($kasu == null) ){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.featured', true],
                                    ['products.category_id', $category_id],
                                    ['brand_id', $brand_id]
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.featured', true],
                                    ['products.is_active', true],
                                    ['brand_id', $brand_id]
                                ])->select('products.name', 'products.code')->get();
        }
        elseif(($category_id != 0) && ($brand_id == 0) && ($kasu != null)){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.featured', true],
                                    ['products.warehouse_id', $kasu],
                                    ['products.category_id', $category_id],
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.warehouse_id', $kasu],
                                    ['products.featured', true],
                                    ['products.is_active', true]
                                ])->select('products.id', 'products.name', 'products.code')->get();
        }
        elseif(($category_id != 0) && ($brand_id == 0) && ($kasu == null)){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.featured', true],
                                    ['products.category_id', $category_id],
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.featured', true],
                                    ['products.is_active', true]
                                ])->select('products.id', 'products.name', 'products.code')->get();
        }
        elseif(($category_id == 0) && ($brand_id != 0) && ($kasu != null)){
            $lims_product_list = Product::where([
                                ['brand_id', $brand_id],
                                ['warehouse_id', $kasu],
                                ['featured', true],
                                ['is_active', true]
                            ])
                            ->select('products.id', 'products.name', 'products.code')
                            ->get();
        }
        elseif(($category_id == 0) && ($brand_id != 0) && ($kasu == null)){
            $lims_product_list = Product::where([
                                ['brand_id', $brand_id],
                                ['featured', true],
                                ['is_active', true]
                            ])
                            ->select('products.id', 'products.name', 'products.code')
                            ->get();
        }
        elseif($kasu != null){
            $lims_product_list = Product::where([
                                ['warehouse_id', $kasu],
                                ['featured', true],
                                ['is_active', true]
                            ]) ->get();                          
        }
        else
        {
            $lims_product_list = Product::where([
                ['featured', true],
                ['is_active', true]
            ]) ->get();  
        }

        $index = 0;
        foreach ($lims_product_list as $product) {
                $data['name'][$index] = $product->name;
                $data['code'][$index] = $product->code;
               
                $index++;
            
        }
        return $data;
    }

    public function getCustomerGroup($id)
    {
         $lims_customer_data = Customer::find($id);
         $lims_customer_group_data = CustomerGroup::find($lims_customer_data->customer_group_id);
         return $lims_customer_group_data->percentage;
    }

    public function limsProductSearch(Request $request)
    {
        $todayDate = date('Y-m-d');
        $product_code = explode("(", $request['data']);
        $product_info = explode("?", $request['data']);
        $customer_id = $product_info[1];
        if(strpos($request['data'], '|')) {
            $product_info = explode("|", $request['data']);
            $embeded_code = $product_code[0];
            $product_code[0] = substr($embeded_code, 0, 7);
            $qty = substr($embeded_code, 7, 5) / 1000;
        }
        else {
            $product_code[0] = rtrim($product_code[0], " ");
            $qty = $product_info[2];
        }
        $product_variant_id = null;
        $lims_product_data = Product::where([
            ['code', $product_code[0]],
            ['is_active', true]
        ])->first();

            $product[] = $lims_product_data->name;
            $product[] = $lims_product_data->code;
            $product[] = $lims_product_data->price;
            $product[] = $lims_product_data->id;
            $product[] = $qty;
            if($lims_product_data->unit_id == 1)
            {
                $product[] = 'Piece';
            }
            elseif($lims_product_data->unit_id == 2)
            {
                $product[] = 'Dozen';
            }
            else
            {
                $product[] = 'Carton';  
            }
            
        if($lims_product_data->tax_id != null && $lims_product_data->tax_id != 15) {
            $lims_tax_data = Tax::where('rate',$lims_product_data->tax_id)->first();
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        }
        else{
            $product[] = 15;
            $product[] = 'vat@15';
        }
        $product[] = $lims_product_data->tax_method;
        if($lims_product_data->unit_id==3)
        {
            $product[] = $lims_product_data->piece_in_carton;
        }
      
        return $product;

    }

    public function checkDiscount(Request $request)
    {
        $qty = $request->input('qty');
        $customer_id = $request->input('customer_id');
        $lims_product_data = Product::select('id', 'price')->find($request->input('product_id'));
            $price = $lims_product_data->price;
        $promotion='No Promotion';
        $data = [$price, $promotion];
        return $data;
    }

    public function getGiftCard()
    {
        $gift_card = GiftCard::where("is_active", true)->whereDate('expired_date', '>=', date("Y-m-d"))->get(['id', 'card_no', 'amount', 'expense']);
        return json_encode($gift_card);
    }

    public function productSaleData($id)
    {   $check = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        if($check->type == 1)
        {
            foreach ($lims_product_sale_data as $key => $product_sale_data) {
                $product = Product::find($product_sale_data->product_id);
                $product_sale[0][$key] = $product->name . ' [' . $product->code . ']';
                $product_sale[1][$key] = $product_sale_data->qty;
                $product_sale[2][$key] = $product_sale_data->product_unit;
                $product_sale[3][$key] = $product_sale_data->price;
                $product_sale[4][$key] = $product_sale_data->sell_unit;
                $product_sale[5][$key] = $product_sale_data->net_unit_price;
                $product_sale[6][$key] = $product_sale_data->tax . ' (' . $product_sale_data->tax_rate . '%)';
                $product_sale[7][$key] = $product_sale_data->total;
                $product_sale[8][$key] = $product_sale_data->type;
                $product_sale[9][$key] = $product_sale_data->chassis_no;
                $product_sale[10][$key] = $product_sale_data->engine_no;
            }
        }
        else
        {
            foreach ($lims_product_sale_data as $key => $product_sale_data) {
                $product = VehicleProduct::find($product_sale_data->product_id);
                $product_sale[0][$key] = $product->name . ' [' . $product->code . ']';
                $product_sale[1][$key] = $product_sale_data->qty;
                $product_sale[2][$key] = $product_sale_data->product_unit;
                $product_sale[3][$key] = $product_sale_data->price;
                $product_sale[4][$key] = $product_sale_data->sell_unit;
                $product_sale[5][$key] = $product_sale_data->net_unit_price;
                $product_sale[6][$key] = $product_sale_data->tax . ' (' . $product_sale_data->tax_rate . '%)';
                $product_sale[7][$key] = $product_sale_data->total;
                $product_sale[8][$key] = $product_sale_data->type;
                $product_sale[9][$key] = $product_sale_data->chassis_no;
                $product_sale[10][$key] = $product_sale_data->engine_no;
            } 
        }
       
        return $product_sale;
    }

    public function saleByCsv()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')){
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();

            return view('sale.import',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function importSale(Request $request)
    {
        //get the file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        $file_handle = fopen($filePath, 'r');
        $i = 0;
        //validate the file
        while (!feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if($current_line && $i > 0){
                $product_data[] = Product::where('code', $current_line[0])->first();
                if(!$product_data[$i-1])
                    return redirect()->back()->with('message', 'Product does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1] && $current_line[2] == 'n/a')
                    $unit[$i-1] = 'n/a';
                elseif(!$unit[$i-1]){
                    return redirect()->back()->with('message', 'Sale unit does not exist!');
                }
                if(strtolower($current_line[5]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[5])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $price[] = $current_line[3];
                $discount[] = $current_line[4];
            }
            $i++;
        }
        //return $unit;
        $data = $request->except('document');
        $data['reference_no'] = 'sr-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::user()->id;
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = $data['reference_no'] . '.' . $ext;
            $document->move('public/documents/sale', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        Sale::create($data);
        $lims_sale_data = Sale::latest()->first();
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        
        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_price = $price[$key] - $discount[$key];
                $product_tax = $net_unit_price * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_price * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_price = (100 / (100 + $tax[$key]['rate'])) * ($price[$key] - $discount[$key]);
                $product_tax = ($price[$key] - $discount[$key] - $net_unit_price) * $qty[$key];
                $total = ($price[$key] - $discount[$key]) * $qty[$key];
            }
            if($data['sale_status'] == 1 && $unit[$key]!='n/a'){
                $sale_unit_id = $unit[$key]['id'];
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product['qty'] -= $quantity;
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product->save();
                $product_warehouse->save();
            }
            else
                $sale_unit_id = 0;
            //collecting mail data
            $mail_data['products'][$key] = $product['name'];
            if($product['type'] == 'digital')
                $mail_data['file'][$key] = url('/public/product/files').'/'.$product['file'];
            else
                $mail_data['file'][$key] = '';
            if($sale_unit_id)
                $mail_data['unit'][$key] = $unit[$key]['unit_code'];
            else
                $mail_data['unit'][$key] = '';

            $product_sale = new Product_Sale();
            $product_sale->sale_id = $lims_sale_data->id;
            $product_sale->product_id = $product['id'];
            $product_sale->qty = $mail_data['qty'][$key] = $qty[$key];
            $product_sale->sale_unit_id = $sale_unit_id;
            $product_sale->net_unit_price = number_format((float)$net_unit_price, 2, '.', '');
            $product_sale->discount = $discount[$key] * $qty[$key];
            $product_sale->tax_rate = $tax[$key]['rate'];
            $product_sale->tax = number_format((float)$product_tax, 2, '.', '');
            $product_sale->total = $mail_data['total'][$key] = number_format((float)$total, 2, '.', '');
            $product_sale->save();
            $lims_sale_data->total_qty += $qty[$key];
            $lims_sale_data->total_discount += $discount[$key] * $qty[$key];
            $lims_sale_data->total_tax += number_format((float)$product_tax, 2, '.', '');
            $lims_sale_data->total_price += number_format((float)$total, 2, '.', '');
        }
        $lims_sale_data->item = $key + 1;
        $lims_sale_data->order_tax = ($lims_sale_data->total_price - $lims_sale_data->order_discount) * ($data['order_tax_rate'] / 100);
        $lims_sale_data->grand_total = ($lims_sale_data->total_price + $lims_sale_data->order_tax + $lims_sale_data->shipping_cost) - $lims_sale_data->order_discount;
        $lims_sale_data->save();
        $message = 'Sale imported successfully';
        if($lims_customer_data->email){
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_sale_data->reference_no;
            $mail_data['sale_status'] = $lims_sale_data->sale_status;
            $mail_data['payment_status'] = $lims_sale_data->payment_status;
            $mail_data['total_qty'] = $lims_sale_data->total_qty;
            $mail_data['total_price'] = $lims_sale_data->total_price;
            $mail_data['order_tax'] = $lims_sale_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
            $mail_data['order_discount'] = $lims_sale_data->order_discount;
            $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_sale_data->paid_amount;
            if($mail_data['email']){
                try{
                    Mail::send( 'mail.sale_details', $mail_data, function( $message ) use ($mail_data)
                    {
                        $message->to( $mail_data['email'] )->subject( 'Sale Details' );
                    });
                }
                
                catch(\Exception $e){
                    $message = 'Sale imported successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
                }
            }
        }
        return redirect('sales')->with('message', $message);
    }

    public function createSale($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-edit')){
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_customer_group_all = CustomerGroup::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $lims_product_list = Product::where([
                                    ['featured', 1],
                                    ['is_active', true]
                                ])->get();
            foreach ($lims_product_list as $key => $product) {
                $images = explode(",", $product->image);
                $product->base_image = $images[0];
            }
            $product_number = count($lims_product_list);
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_brand_list = Brand::where('is_active',true)->get();
            $lims_category_list = Category::where('is_active',true)->get();
            $lims_coupon_list = Coupon::where('is_active',true)->get();

            return view('sale.create_sale',compact('lims_biller_list', 'lims_customer_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_sale_data','lims_product_sale_data', 'lims_pos_setting_data', 'lims_brand_list', 'lims_category_list', 'lims_coupon_list', 'lims_product_list', 'product_number', 'lims_customer_group_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


    public function genInvoice($id)
    {
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

        return view('sale.invoice', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }

    public function addPayment(Request $request)
    {
        $data = $request->all();
        $lims_sale_data = Sale::find($data['add_sale_id']);
        $lims_sale_data->paid_amount = $data['paying_amount'];
        if($data['paying_amount'] >= $data['grand_amount'])
        {
            $lims_sale_data->payment_status = 1;
        }
        else
        {
            $lims_sale_data->payment_status = 2;
        }
        $lims_sale_data->save();
        if($data['paid_by_id'] == 1)
            $paying_method = 'Cash';
        elseif ($data['paid_by_id'] == 2)
            $paying_method = 'Deposit';
        $lims_payment_data = new Payment();
        $lims_payment_data->user_id = Auth::id();
        $lims_payment_data->sale_id = $lims_sale_data->id;
        if($data['paid_by_id'] == 2)
        {
            $lims_payment_data->bank_id = $data['bank_id'];
        }
        $data['payment_reference'] = 'spr-' . date("Ymd") . '-'. date("his");
        $lims_payment_data->payment_reference = $data['payment_reference'];
        $lims_payment_data->amount = $data['amount'];
        $lims_payment_data->keri = $data['grand_amount'] - $data['paying_amount'];
        $lims_payment_data->paying_method = $paying_method;
        if($data['payment_note'] != "")
        {
            $lims_payment_data->payment_note = $data['payment_note'];
        }
        $lims_payment_data->payment_date = Carbon::now();
        $lims_payment_data->save();
        $message = 'Payment Added successfully';
        return redirect('sales')->with('message', $message);
    }

    public function getPayment($id)
    {
        $lims_payment_list = Payment::where('sale_id', $id)->get();
        $date = [];
        $payment_reference = [];
        $paid_amount = []; 
        $paying_method = [];
        $bank = [];
        $Due = [];
        $user = []; 
        $note = [];      
        foreach ($lims_payment_list as $payment) {
            $date[] = date(config('date_format'), strtotime($payment->created_at->toDateString())) . ' '. $payment->created_at->toTimeString();
            $payment_reference[] = $payment->payment_reference;
            $paid_amount[] = $payment->amount; 
            $paying_method[] = $payment->paying_method;
            if(($payment->bank_id != null || $payment->bank_id != "") && $payment->paying_method == "Deposit")
            {
                $bank[] = Bank::find($payment->bank_id)->title;
            }
            else
            {
                $bank[] = "N/A";   
            }
            if($payment->keri != 0)
            {
                $Due[]= number_format($payment->keri, 2);
            }
            else
            {
                $Due[] = '0.00'; 
            }
            $user[] = User::find($payment->user_id)->name;
            $note[] = $payment->payment_note;
        }
        $payments[] = $date;
        $payments[] = $payment_reference;
        $payments[] = $paid_amount;
        $payments[] = $paying_method;
        $payments[] = $bank;
        $payments[] = $Due;
        $payments[] = $user;
        $payments[] = $note;
        return $payments;
    }

    public function updatePayment(Request $request)
    {
        $data = $request->all();
        //return $data;
        $lims_payment_data = Payment::find($data['payment_id']);
        $lims_sale_data = Sale::find($lims_payment_data->sale_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        //updating sale table
        $amount_dif = $lims_payment_data->amount - $data['edit_amount'];
        $lims_sale_data->paid_amount = $lims_sale_data->paid_amount - $amount_dif;
        $balance = $lims_sale_data->grand_total - $lims_sale_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_sale_data->payment_status = 2;
        elseif ($balance == 0)
            $lims_sale_data->payment_status = 4;
        $lims_sale_data->save();

        if($lims_payment_data->paying_method == 'Deposit') {
            $lims_customer_data->expense -= $lims_payment_data->amount;
            $lims_customer_data->save();
        }
        elseif($lims_payment_data->paying_method == 'Points') {
            $lims_customer_data->points += $lims_payment_data->used_points;
            $lims_customer_data->save();
            $lims_payment_data->used_points = 0;
        }
        if($data['edit_paid_by_id'] == 1)
            $lims_payment_data->paying_method = 'Cash';
        elseif ($data['edit_paid_by_id'] == 2){
            if($lims_payment_data->paying_method == 'Gift Card'){
                $lims_payment_gift_card_data = PaymentWithGiftCard::where('payment_id', $data['payment_id'])->first();

                $lims_gift_card_data = GiftCard::find($lims_payment_gift_card_data->gift_card_id);
                $lims_gift_card_data->expense -= $lims_payment_data->amount;
                $lims_gift_card_data->save();

                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['edit_amount'];
                $lims_gift_card_data->save();

                $lims_payment_gift_card_data->gift_card_id = $data['gift_card_id'];
                $lims_payment_gift_card_data->save(); 
            }
            else{
                $lims_payment_data->paying_method = 'Gift Card';
                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['edit_amount'];
                $lims_gift_card_data->save();
                PaymentWithGiftCard::create($data);
            }
        }
        elseif ($data['edit_paid_by_id'] == 3){
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            if($lims_payment_data->paying_method == 'Credit Card'){
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $lims_payment_data->id)->first();

                \Stripe\Refund::create(array(
                  "charge" => $lims_payment_with_credit_card_data->charge_id,
                ));

                $customer_id = 
                $lims_payment_with_credit_card_data->customer_stripe_id;

                $charge = \Stripe\Charge::create([
                    'amount' => $data['edit_amount'] * 100,
                    'currency' => 'usd',
                    'customer' => $customer_id
                ]);
                $lims_payment_with_credit_card_data->charge_id = $charge->id;
                $lims_payment_with_credit_card_data->save();
            }
            else{
                $token = $data['stripeToken'];
                $amount = $data['edit_amount'];
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('customer_id', $lims_sale_data->customer_id)->first();

                if(!$lims_payment_with_credit_card_data) {
                    $customer = \Stripe\Customer::create([
                        'source' => $token
                    ]);

                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'customer' => $customer->id,
                    ]);
                    $data['customer_stripe_id'] = $customer->id;
                }
                else {
                    $customer_id = 
                    $lims_payment_with_credit_card_data->customer_stripe_id;

                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'customer' => $customer_id
                    ]);
                    $data['customer_stripe_id'] = $customer_id;
                }
                $data['customer_id'] = $lims_sale_data->customer_id;
                $data['charge_id'] = $charge->id;
                PaymentWithCreditCard::create($data);
            }
            $lims_payment_data->paying_method = 'Credit Card';
        }
        elseif($data['edit_paid_by_id'] == 4){
            if($lims_payment_data->paying_method == 'Cheque'){
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $data['payment_id'])->first();
                $lims_payment_cheque_data->cheque_no = $data['edit_cheque_no'];
                $lims_payment_cheque_data->save(); 
            }
            else{
                $lims_payment_data->paying_method = 'Cheque';
                $data['cheque_no'] = $data['edit_cheque_no'];
                PaymentWithCheque::create($data);
            }
        }
        elseif($data['edit_paid_by_id'] == 5){
            //updating payment data
            $lims_payment_data->amount = $data['edit_amount'];
            $lims_payment_data->paying_method = 'Paypal';
            $lims_payment_data->payment_note = $data['edit_payment_note'];
            $lims_payment_data->save();

            $provider = new ExpressCheckout;
            $paypal_data['items'] = [];
            $paypal_data['items'][] = [
                'name' => 'Paid Amount',
                'price' => $data['edit_amount'],
                'qty' => 1
            ];
            $paypal_data['invoice_id'] = $lims_payment_data->payment_reference;
            $paypal_data['invoice_description'] = "Reference: {$paypal_data['invoice_id']}";
            $paypal_data['return_url'] = url('/sale/paypalPaymentSuccess/'.$lims_payment_data->id);
            $paypal_data['cancel_url'] = url('/sale');

            $total = 0;
            foreach($paypal_data['items'] as $item) {
                $total += $item['price']*$item['qty'];
            }

            $paypal_data['total'] = $total;
            $response = $provider->setExpressCheckout($paypal_data);
            return redirect($response['paypal_link']);
        }   
        elseif($data['edit_paid_by_id'] == 6){
            $lims_payment_data->paying_method = 'Deposit';
            $lims_customer_data->expense += $data['edit_amount'];
            $lims_customer_data->save();
        }
        elseif($data['edit_paid_by_id'] == 7) {
            $lims_payment_data->paying_method = 'Points';
            $lims_reward_point_setting_data = RewardPointSetting::latest()->first();
            $used_points = ceil($data['edit_amount'] / $lims_reward_point_setting_data->per_point_amount);
            $lims_payment_data->used_points = $used_points;
            $lims_customer_data->points -= $used_points;
            $lims_customer_data->save();
        }
        //updating payment data
        $lims_payment_data->account_id = $data['account_id'];
        $lims_payment_data->amount = $data['edit_amount'];
        $lims_payment_data->change = $data['edit_paying_amount'] - $data['edit_amount'];
        $lims_payment_data->payment_note = $data['edit_payment_note'];
        $lims_payment_data->save();
        $message = 'Payment updated successfully';
        //collecting male data
        if($lims_customer_data->email){
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['sale_reference'] = $lims_sale_data->reference_no;
            $mail_data['payment_reference'] = $lims_payment_data->payment_reference;
            $mail_data['payment_method'] = $lims_payment_data->paying_method;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_payment_data->amount;
            try{
                Mail::send( 'mail.payment_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Payment Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Payment updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('sales')->with('message', $message);
    }

    public function deletePayment(Request $request)
    {
        $lims_payment_data = Payment::find($request['id']);
        $lims_sale_data = Sale::where('id', $lims_payment_data->sale_id)->first();
        $lims_sale_data->paid_amount -= $lims_payment_data->amount;
        $balance = $lims_sale_data->grand_total - $lims_sale_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_sale_data->payment_status = 2;
        elseif ($balance == 0)
            $lims_sale_data->payment_status = 4;
        $lims_sale_data->save();

        if ($lims_payment_data->paying_method == 'Gift Card') {
            $lims_payment_gift_card_data = PaymentWithGiftCard::where('payment_id', $request['id'])->first();
            $lims_gift_card_data = GiftCard::find($lims_payment_gift_card_data->gift_card_id);
            $lims_gift_card_data->expense -= $lims_payment_data->amount;
            $lims_gift_card_data->save();
            $lims_payment_gift_card_data->delete();
        }
        elseif($lims_payment_data->paying_method == 'Credit Card'){
            $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $request['id'])->first();
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            \Stripe\Refund::create(array(
              "charge" => $lims_payment_with_credit_card_data->charge_id,
            ));

            $lims_payment_with_credit_card_data->delete();
        }
        elseif ($lims_payment_data->paying_method == 'Cheque') {
            $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $request['id'])->first();
            $lims_payment_cheque_data->delete();
        }
        elseif ($lims_payment_data->paying_method == 'Paypal') {
            $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $request['id'])->first();
            if($lims_payment_paypal_data){
                $provider = new ExpressCheckout;
                $response = $provider->refundTransaction($lims_payment_paypal_data->transaction_id);
                $lims_payment_paypal_data->delete();
            }
        }
        elseif ($lims_payment_data->paying_method == 'Deposit'){
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $lims_customer_data->expense -= $lims_payment_data->amount;
            $lims_customer_data->save();
        }
        elseif ($lims_payment_data->paying_method == 'Points'){
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $lims_customer_data->points += $lims_payment_data->used_points;
            $lims_customer_data->save();
        }
        $lims_payment_data->delete();
        return redirect('sales')->with('not_permitted', 'Payment deleted successfully');
    }

    public function todaySale()
    {
        $data['total_sale_amount'] = Sale::whereDate('created_at', date("Y-m-d"))->sum('grand_total');
        $data['total_payment'] = Payment::whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['cash_payment'] = Payment::where([
                                    ['paying_method', 'Cash']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['credit_card_payment'] = Payment::where([
                                    ['paying_method', 'Credit Card']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['gift_card_payment'] = Payment::where([
                                    ['paying_method', 'Gift Card']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['deposit_payment'] = Payment::where([
                                    ['paying_method', 'Deposit']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['cheque_payment'] = Payment::where([
                                    ['paying_method', 'Cheque']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['paypal_payment'] = Payment::where([
                                    ['paying_method', 'Paypal']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['total_sale_return'] = Returns::whereDate('created_at', date("Y-m-d"))->sum('grand_total');
        $data['total_expense'] = Expense::whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['total_cash'] = $data['total_payment'] - ($data['total_sale_return'] + $data['total_expense']);
        return $data;
    }

    public function todayProfit($warehouse_id)
    {
        if($warehouse_id == 0)
            $product_sale_data = Product_Sale::select(DB::raw('product_id, product_batch_id, sum(qty) as sold_qty, sum(total) as sold_amount'))->whereDate('created_at', date("Y-m-d"))->groupBy('product_id', 'product_batch_id')->get();
        else
            $product_sale_data = Sale::join('product_sales', 'sales.id', '=', 'product_sales.sale_id')
            ->select(DB::raw('product_sales.product_id, product_sales.product_batch_id, sum(product_sales.qty) as sold_qty, sum(product_sales.total) as sold_amount'))
            ->where('sales.warehouse_id', $warehouse_id)->whereDate('sales.created_at', date("Y-m-d"))
            ->groupBy('product_sales.product_id', 'product_sales.product_batch_id')->get();

        $product_revenue = 0;
        $product_cost = 0;
        $profit = 0;
        foreach ($product_sale_data as $key => $product_sale) {
            if($warehouse_id == 0) {
                if($product_sale->product_batch_id)
                    $product_purchase_data = ProductPurchase::where([
                        ['product_id', $product_sale->product_id],
                        ['product_batch_id', $product_sale->product_batch_id]
                    ])->get();
                else
                    $product_purchase_data = ProductPurchase::where('product_id', $product_sale->product_id)->get();
            }
            else {
                if($product_sale->product_batch_id) {
                    $product_purchase_data = Purchase::join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                    ->where([
                        ['product_purchases.product_id', $product_sale->product_id],
                        ['product_purchases.product_batch_id', $product_sale->product_batch_id],
                        ['purchases.warehouse_id', $warehouse_id]
                    ])->select('product_purchases.*')->get();
                }
                else
                    $product_purchase_data = Purchase::join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                    ->where([
                        ['product_purchases.product_id', $product_sale->product_id],
                        ['purchases.warehouse_id', $warehouse_id]
                    ])->select('product_purchases.*')->get();
            }

            $purchased_qty = 0;
            $purchased_amount = 0;
            $sold_qty = $product_sale->sold_qty;
            $product_revenue += $product_sale->sold_amount;
            foreach ($product_purchase_data as $key => $product_purchase) {
                $purchased_qty += $product_purchase->qty;
                $purchased_amount += $product_purchase->total;
                if($purchased_qty >= $sold_qty) {
                    $qty_diff = $purchased_qty - $sold_qty;
                    $unit_cost = $product_purchase->total / $product_purchase->qty;
                    $purchased_amount -= ($qty_diff * $unit_cost);
                    break;
                }
            }

            $product_cost += $purchased_amount;
            $profit += $product_sale->sold_amount - $purchased_amount;
        }
        
        $data['product_revenue'] = $product_revenue;
        $data['product_cost'] = $product_cost;
        if($warehouse_id == 0)
            $data['expense_amount'] = Expense::whereDate('created_at', date("Y-m-d"))->sum('amount');
        else
            $data['expense_amount'] = Expense::where('warehouse_id', $warehouse_id)->whereDate('created_at', date("Y-m-d"))->sum('amount');

        $data['profit'] = $profit - $data['expense_amount'];
        return $data;
    }

    public function deleteBySelection(Request $request)
    {
        $sale_id = $request['saleIdArray'];
        foreach ($sale_id as $id) {
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->delete();
            $lims_payment_data = Payment::where('sale_id', $id)->delete();
            $lims_sale_data->delete(); 
        }
        return response()->json(['Success'=>'sales deleted successfully']);
    }
    
    public function destroy($id)
    {
        $url = url()->previous();
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->delete();
        $lims_delivery_data = Payment::where('sale_id',$id)->delete();
        $lims_sale_data->delete();
        if($lims_sale_data && $lims_product_sale_data && $lims_delivery_data)
            $message = 'Sale deleted successfully';
        else 
            $message = 'Sale not deleted successfully';   
        return Redirect::to($url)->with('not_permitted', $message);
    } 
}
