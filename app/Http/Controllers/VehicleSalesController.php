<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Customer;
use App\Bank;
use App\CustomerGroup;
use App\Warehouse;
use App\Brand;
use App\Category;
use App\Product;
use App\VehicleProduct;
use App\VehicleBrand;
use App\VehicleCategory;
use App\Unit; 
use App\Tax;
use App\Sale;
use App\Delivery;
use App\PosSetting;
use App\Product_Sale;
use App\Product_Warehouse;

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

class VehicleSalesController extends Controller
{

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





    public function getProduct($id)
    {
        $lims_product_warehouse_data = VehicleProduct::
        where([
            ['is_active', true],
            ['warehouse_id', $id],
            ['qty', '>', 0]
        ])->orderBy('created_at','asc')->get();
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_data = [];
        $product_id = [];
        $product_unit = [];
        $product_piece = [];
        $vehicle_cha = [];
        $vehicle_eng = [];
        //product without variant
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_code[] =  $product_warehouse->code;
            $product_name[] = htmlspecialchars($product_warehouse->name);
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $product_id[] = $product_warehouse->id;
            $product_unit[] = 'piece';
            $product_piece[] = '1';
            $vehicle_cha[] = $product_warehouse->chassis_no;
            $vehicle_eng[] = $product_warehouse->engine_no;
        }

        $product_data = [$product_code, $product_name, $product_qty, $product_id, $product_unit, $product_price , $vehicle_cha, $vehicle_eng ];
        return $product_data;
    }


    public function getProductByFilter($category_id, $brand_id)
    {
        $data = [];
        $kasu=Auth::user()->warehouse_id;
        if(($category_id != 0) && ($brand_id != 0) && ($kasu != null) ){
            $lims_product_list = DB::table('vehicle_products')
                                ->join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                                ->where([
                                    ['vehicle_products.is_active', true],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.category_id', $category_id],
                                    ['vehicle_products.warehouse_id', $kasu],
                                    ['brand_id', $brand_id]
                                ])->orWhere([
                                    ['vehiclecategory.parent_id', $category_id],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.is_active', true],
                               
                                    ['vehicle_products.warehouse_id', $kasu],
                                    ['brand_id', $brand_id]
                                ])->select('vehicle_products.name', 'vehicle_products.code','vehicle_products.chassis_no', 'vehicle_products.engine_no')->get();
        }
        elseif(($category_id != 0) && ($brand_id != 0) && ($kasu == null) ){
            $lims_product_list = DB::table('vehicle_products')
                                ->join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                                ->where([
                                    ['vehicle_products.is_active', true],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.category_id', $category_id],
                                    ['brand_id', $brand_id]
                                ])->orWhere([
                                    ['vehiclecategory.parent_id', $category_id],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.is_active', true],
                                   
                                    ['brand_id', $brand_id]
                                ])->select('vehicle_products.name', 'vehicle_products.code','vehicle_products.chassis_no', 'vehicle_products.engine_no')->get();
        }
        elseif(($category_id != 0) && ($brand_id == 0) && ($kasu != null)){
            $lims_product_list = DB::table('vehicle_products')
                                ->join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                                ->where([
                                    ['vehicle_products.is_active', true],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.warehouse_id', $kasu],
                                    ['vehicle_products.category_id', $category_id],
                                ])->orWhere([
                                    ['vehiclecategory.parent_id', $category_id],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.warehouse_id', $kasu],
                                  
                                    ['vehicle_products.is_active', true]
                                ])->select('vehicle_products.id', 'vehicle_products.name', 'vehicle_products.code', 'vehicle_products.chassis_no', 'vehicle_products.engine_no')->get();
        }
        elseif(($category_id != 0) && ($brand_id == 0) && ($kasu == null)){
            $lims_product_list = DB::table('vehicle_products')
                                ->join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                                ->where([
                                    ['vehicle_products.is_active', true],
                                    ['vehicle_products.qty','>', 0],
                                
                                    ['vehicle_products.category_id', $category_id],
                                ])->orWhere([
                                    ['vehiclecategory.parent_id', $category_id],
                                    ['vehicle_products.qty','>', 0],
                                    ['vehicle_products.is_active', true]
                                ])->select('vehicle_products.id', 'vehicle_products.name', 'vehicle_products.code','vehicle_products.chassis_no', 'vehicle_products.engine_no')->get();
        }
        elseif(($category_id == 0) && ($brand_id != 0) && ($kasu != null)){
            $lims_product_list = VehicleProduct::where([
                                ['brand_id', $brand_id],
                                ['warehouse_id', $kasu],
                                ['qty', '>',0],
                                ['is_active', true]
                            ])
                            ->select('vehicle_products.id', 'vehicle_products.name', 'vehicle_products.code', 'vehicle_products.chassis_no', 'vehicle_products.engine_no')
                            ->get();
        }
        elseif(($category_id == 0) && ($brand_id != 0) && ($kasu == null)){
            $lims_product_list =VehicleProduct::where([
                ['brand_id', $brand_id],
                ['qty', '>',0],
                ['is_active', true]
            ])
            ->select('vehicle_products.id', 'vehicle_products.name', 'vehicle_products.code', 'vehicle_products.chassis_no', 'vehicle_products.engine_no')
            ->get();
        }
        elseif($kasu != null){
            $lims_product_list = VehicleProduct::where([
                                ['warehouse_id', $kasu],
                                ['qty', '>',0],
                                ['is_active', true]
                            ]) ->get();                          
        }
        else
        {
            $lims_product_list = VehicleProduct::where([
                ['qty', '>',0],
                ['is_active', true]
            ]) ->get();  
        }

        $index = 0;
        
        foreach ($lims_product_list as $product) {
                $data['name'][$index] = $product->name;
                $data['code'][$index] = $product->code;
                $data['chassis'][$index] = $product->chassis_no;
                $data['engine'][$index] = $product->engine_no;
               
                $index++;
            
        }
        //dd($data);
        return $data;
    }


    public function limsProductSearch(Request $request)
    {   
        $todayDate = date('Y-m-d'); 
        $data = $request->all();
        $product_code = explode("(", $request['data']);
        $chassis_no = $request['chassis'];
        $engine_no = $request['engine'];
        $product_info = explode("?", $request['data']);
        $customer_id = $product_info[1];
        $product_code[0] = rtrim($product_code[0], " ");
        $qty = $product_info[2];
        $product_variant_id = null;
        $lims_product_data = VehicleProduct::where([
                ['chassis_no', $chassis_no], 
                ['is_active', true]])
            ->orWhere([
                ['engine_no', $engine_no],
                ['is_active', true]])->first();
            $product[] = $lims_product_data->name;
            $product[] = $lims_product_data->code;
            $product[] = $lims_product_data->price;
            $product[] = $lims_product_data->id;
            $product[] = $qty;
            $product[] = "Piece";
            $product[] = 15;
            $product[] = 'vat@15';
            $product[] = $lims_product_data->tax_method;
            $product[] = 1;
            $product[] = $lims_product_data->chassis_no;
            $product[] = $lims_product_data->engine_no;
        
      
        return $product;

    }



    public function productSaleData($id)
    {
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        foreach ($lims_product_sale_data as $key => $product_sale_data) {
            $product = Product::find($product_sale_data->product_id);
            if($product_sale_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_sale_data->product_id, $product_sale_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }
            $unit_data = Unit::find($product_sale_data->sale_unit_id);
            if($unit_data){
                $unit = $unit_data->unit_code;
            }
            else
                $unit = '';
            if($product_sale_data->product_batch_id) {
                $product_batch_data = ProductBatch::select('batch_no')->find($product_sale_data->product_batch_id);
                $product_sale[7][$key] = $product_batch_data->batch_no;
            }
            else
                $product_sale[7][$key] = 'N/A';
            $product_sale[0][$key] = $product->name . ' [' . $product->code . ']';
            if($product_sale_data->imei_number)
                $product_sale[0][$key] .= '<br>IMEI or Serial Number: '. $product_sale_data->imei_number;
            $product_sale[1][$key] = $product_sale_data->qty;
            $product_sale[2][$key] = $unit;
            $product_sale[3][$key] = $product_sale_data->tax;
            $product_sale[4][$key] = $product_sale_data->tax_rate;
            $product_sale[5][$key] = $product_sale_data->discount;
            $product_sale[6][$key] = $product_sale_data->total;
        }
        return $product_sale;
    }


    public function deleteBySelection(Request $request)
    {
        $sale_id = $request['saleIdArray'];
        foreach ($sale_id as $id) {
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $lims_delivery_data = Delivery::where('sale_id',$id)->first();
            if($lims_sale_data->sale_status == 3)
                $message = 'Draft deleted successfully';
            else
                $message = 'Sale deleted successfully';
            foreach ($lims_product_sale_data as $product_sale) {
                $lims_product_data = Product::find($product_sale->product_id);
                //adjust product quantity
                if( ($lims_sale_data->sale_status == 1) && ($lims_product_data->type == 'combo') ){
                    $product_list = explode(",", $lims_product_data->product_list);
                    if($lims_product_data->variant_list)
                        $variant_list = explode(",", $lims_product_data->variant_list);
                    else
                        $variant_list = [];
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        if(count($variant_list) && $variant_list[$index]) {
                            $child_product_variant_data = ProductVariant::where([
                                ['product_id', $child_id],
                                ['variant_id', $variant_list[$index] ]
                            ])->first();

                            $child_warehouse_data = Product_Warehouse::where([
                                ['product_id', $child_id],
                                ['variant_id', $variant_list[$index] ],
                                ['warehouse_id', $lims_sale_data->warehouse_id ],
                            ])->first();

                             $child_product_variant_data->qty += $product_sale->qty * $qty_list[$index];
                             $child_product_variant_data->save();
                        }
                        else {
                            $child_warehouse_data = Product_Warehouse::where([
                                ['product_id', $child_id],
                                ['warehouse_id', $lims_sale_data->warehouse_id ],
                            ])->first();
                        }

                        $child_data->qty += $product_sale->qty * $qty_list[$index];
                        $child_warehouse_data->qty += $product_sale->qty * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                elseif(($lims_sale_data->sale_status == 1) && ($product_sale->sale_unit_id != 0)){
                    $lims_sale_unit_data = Unit::find($product_sale->sale_unit_id);
                    if ($lims_sale_unit_data->operator == '*')
                        $product_sale->qty = $product_sale->qty * $lims_sale_unit_data->operation_value;
                    else
                        $product_sale->qty = $product_sale->qty / $lims_sale_unit_data->operation_value;
                    if($product_sale->variant_id) {
                        $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_sale->variant_id)->first();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($lims_product_data->id, $product_sale->variant_id, $lims_sale_data->warehouse_id)->first();
                        $lims_product_variant_data->qty += $product_sale->qty;
                        $lims_product_variant_data->save();
                    }
                    elseif($product_sale->product_batch_id) {
                        $lims_product_batch_data = ProductBatch::find($product_sale->product_batch_id);
                        $lims_product_warehouse_data = Product_Warehouse::where([
                            ['product_batch_id', $product_sale->product_batch_id],
                            ['warehouse_id', $lims_sale_data->warehouse_id]
                        ])->first();

                        $lims_product_batch_data->qty -= $product_sale->qty;
                        $lims_product_batch_data->save();
                    }
                    else {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $lims_sale_data->warehouse_id)->first();
                    }

                    $lims_product_data->qty += $product_sale->qty;
                    $lims_product_warehouse_data->qty += $product_sale->qty;
                    $lims_product_data->save();
                    $lims_product_warehouse_data->save();
                }
                $product_sale->delete();
            }
            $lims_payment_data = Payment::where('sale_id', $id)->get();
            foreach ($lims_payment_data as $payment) {
                if($payment->paying_method == 'Gift Card'){
                    $lims_payment_with_gift_card_data = PaymentWithGiftCard::where('payment_id', $payment->id)->first();
                    $lims_gift_card_data = GiftCard::find($lims_payment_with_gift_card_data->gift_card_id);
                    $lims_gift_card_data->expense -= $payment->amount;
                    $lims_gift_card_data->save();
                    $lims_payment_with_gift_card_data->delete();
                }
                elseif($payment->paying_method == 'Cheque'){
                    $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $payment->id)->first();
                    $lims_payment_cheque_data->delete();
                }
                elseif($payment->paying_method == 'Credit Card'){
                    $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment->id)->first();
                    $lims_payment_with_credit_card_data->delete();
                }
                elseif($payment->paying_method == 'Paypal'){
                    $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $payment->id)->first();
                    if($lims_payment_paypal_data)
                        $lims_payment_paypal_data->delete();
                }
                elseif($payment->paying_method == 'Deposit'){
                    $lims_customer_data = Customer::find($lims_sale_data->customer_id);
                    $lims_customer_data->expense -= $payment->amount;
                    $lims_customer_data->save();
                }
                $payment->delete();
            }
            if($lims_delivery_data)
                $lims_delivery_data->delete();
            if($lims_sale_data->coupon_id) {
                $lims_coupon_data = Coupon::find($lims_sale_data->coupon_id);
                $lims_coupon_data->used -= 1;
                $lims_coupon_data->save();
            }
            $lims_sale_data->delete();
        }
        return 'Sale deleted successfully!';
    }
    
    public function destroy($id)
    {
        $url = url()->previous();
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_delivery_data = Delivery::where('sale_id',$id)->first();
        if($lims_sale_data->sale_status == 3)
            $message = 'Draft deleted successfully';
        else
            $message = 'Sale deleted successfully';

        foreach ($lims_product_sale_data as $product_sale) {
            $lims_product_data = Product::find($product_sale->product_id);
            //adjust product quantity
            if( ($lims_sale_data->sale_status == 1) && ($lims_product_data->type == 'combo') ) {
                $product_list = explode(",", $lims_product_data->product_list);
                $variant_list = explode(",", $lims_product_data->variant_list);
                $qty_list = explode(",", $lims_product_data->qty_list);
                if($lims_product_data->variant_list)
                    $variant_list = explode(",", $lims_product_data->variant_list);
                else
                    $variant_list = [];
                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    if(count($variant_list) && $variant_list[$index]) {
                        $child_product_variant_data = ProductVariant::where([
                            ['product_id', $child_id],
                            ['variant_id', $variant_list[$index] ]
                        ])->first();

                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['variant_id', $variant_list[$index] ],
                            ['warehouse_id', $lims_sale_data->warehouse_id ],
                        ])->first();

                         $child_product_variant_data->qty += $product_sale->qty * $qty_list[$index];
                         $child_product_variant_data->save();
                    }
                    else {
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $lims_sale_data->warehouse_id ],
                        ])->first();
                    }

                    $child_data->qty += $product_sale->qty * $qty_list[$index];
                    $child_warehouse_data->qty += $product_sale->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif(($lims_sale_data->sale_status == 1) && ($product_sale->sale_unit_id != 0)) {
                $lims_sale_unit_data = Unit::find($product_sale->sale_unit_id);
                if ($lims_sale_unit_data->operator == '*')
                    $product_sale->qty = $product_sale->qty * $lims_sale_unit_data->operation_value;
                else
                    $product_sale->qty = $product_sale->qty / $lims_sale_unit_data->operation_value;
                if($product_sale->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_sale->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($lims_product_data->id, $product_sale->variant_id, $lims_sale_data->warehouse_id)->first();
                    $lims_product_variant_data->qty += $product_sale->qty;
                    $lims_product_variant_data->save();
                }
                elseif($product_sale->product_batch_id) {
                    $lims_product_batch_data = ProductBatch::find($product_sale->product_batch_id);
                    $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_sale->product_batch_id],
                        ['warehouse_id', $lims_sale_data->warehouse_id]
                    ])->first();

                    $lims_product_batch_data->qty -= $product_sale->qty;
                    $lims_product_batch_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $lims_sale_data->warehouse_id)->first();
                }
                    
                $lims_product_data->qty += $product_sale->qty;
                $lims_product_warehouse_data->qty += $product_sale->qty;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            if($product_sale->imei_number) {
                if($lims_product_warehouse_data->imei_number)
                    $lims_product_warehouse_data->imei_number .= ',' . $product_sale->imei_number;
                else
                    $lims_product_warehouse_data->imei_number = $product_sale->imei_number;
                $lims_product_warehouse_data->save();
            }
            $product_sale->delete();
        }

        $lims_payment_data = Payment::where('sale_id', $id)->get();
        foreach ($lims_payment_data as $payment) {
            if($payment->paying_method == 'Gift Card'){
                $lims_payment_with_gift_card_data = PaymentWithGiftCard::where('payment_id', $payment->id)->first();
                $lims_gift_card_data = GiftCard::find($lims_payment_with_gift_card_data->gift_card_id);
                $lims_gift_card_data->expense -= $payment->amount;
                $lims_gift_card_data->save();
                $lims_payment_with_gift_card_data->delete();
            }
            elseif($payment->paying_method == 'Cheque'){
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $payment->id)->first();
                $lims_payment_cheque_data->delete();
            }
            elseif($payment->paying_method == 'Credit Card'){
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment->id)->first();
                $lims_payment_with_credit_card_data->delete();
            }
            elseif($payment->paying_method == 'Paypal'){
                $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $payment->id)->first();
                if($lims_payment_paypal_data)
                    $lims_payment_paypal_data->delete();
            }
            elseif($payment->paying_method == 'Deposit'){
                $lims_customer_data = Customer::find($lims_sale_data->customer_id);
                $lims_customer_data->expense -= $payment->amount;
                $lims_customer_data->save();
            }
            $payment->delete();
        }
        if($lims_delivery_data)
            $lims_delivery_data->delete();
        if($lims_sale_data->coupon_id) {
            $lims_coupon_data = Coupon::find($lims_sale_data->coupon_id);
            $lims_coupon_data->used -= 1;
            $lims_coupon_data->save();
        }
        $lims_sale_data->delete();
        return Redirect::to($url)->with('not_permitted', $message);
    }
}
