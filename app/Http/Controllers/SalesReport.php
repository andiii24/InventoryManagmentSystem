<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product_Sale;
use App\Sale;
use App\Purchase;
use App\Quotation;
use App\Transfer;
use App\Returns;
use App\ProductReturn;
use App\ReturnPurchase;
use App\Warehouse;
use App\User;
use App\Customer;
use DB;
use Auth;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SalesReport extends Controller
{  
    public function ProSalesReport(Request $request)
    {
            $product_sale_id = Sale::where('type',1)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.product_sales_report',compact('product_sale_id','lims_warehouse_list'));
    }
    public function sortedByDateProduct(Request $request){
        //  Start and End Data range
        if ($request->start_date != null && $request->end_date != null) {
            $start=$request->start_date;
            $end=$request->end_date;
             if($request->warehouse != 0)
             { 
                $product_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)   
                                  ->where ('warehouse_id',$request->warehouse)-> where('type',1)->get();

             }
             else
             {
                $product_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)   
                -> where('type',1)->get();
             }
            

        }
        // Only start date is recived and generate until today
        elseif($request->start_date != null && $request->end_date == null){
            $start=$request->start_date;
            if($request->warehouse == 0)
            {
                $product_sale_id=Sale::whereBetween('created_at',[$start, Carbon::today()])->where('type',1)->get();
            }
            else
            {
                $product_sale_id=Sale::whereBetween('created_at',[$start, Carbon::today()])->where('type',1)->where('warehouse_id', $request->warehouse)->get();
            }
                                       
        }  
       
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.product_sales_report',compact('product_sale_id','lims_warehouse_list'));
    }
    
        public function VehSalesReport(Request $request)
    {
            $vehicle_sale_id = Sale::where('type',2)->orderBy('created_at', 'desc')->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.vehicle_sales_report',compact('vehicle_sale_id','lims_warehouse_list'));
    }
    
    
    
    public function sortedByDateVehicle(Request $request)
    {
               //  Start and End Data range
        if ($request->start_date != null && $request->end_date != null) {
            $start=$request->start_date;
            $end=$request->end_date;
               if($request->warehouse == 0)
               {
                $vehicle_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->   
                where('type',2)->get();
               }
               else
               {
                $vehicle_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->   
                where('type',2)->where('warehouse_id', $request->warehouse)->get();
               }
        }
        // Only start date is recived and generate until today
        elseif($request->start_date != null && $request->end_date == null ){
            $start=$request->start_date;
            $today = Carbon::today();
            if($request->warehouse == 0)
            {

                $vehicle_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=',$today)->
                                     where('type',2)->get();
            }
            else
            {
                $vehicle_sale_id=Sale::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=',$today)->
                                     where('type',2)->where('warehouse_id', $request->warehouse)->get();
            }
           
            
        }  
        
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.vehicle_sales_report',compact('vehicle_sale_id','lims_warehouse_list'));
    }
}
