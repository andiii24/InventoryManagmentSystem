<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Keygen;
use DB;
use Auth;
use App\User;
use App\Vehicle;
use App\VehicleInfo;
use App\VehicleBrand;
use App\Manufacture;
use App\VehicleProduct;
use App\VehicleCategory;
use Carbon\Carbon;
use App\Tax;
use App\Warehouse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class VehicleProductController extends Controller
{
     //
     public function index()
     {
       $warehouse = Warehouse::where('is_active', true)->orderBy('name', 'asc')->get();
             return view('Vehicles.product', compact('warehouse'));
        
     }
     public function store(Request $request)
     {
         $data = $request->all();
         $myvalue = array();
         parse_str($data['data'],$myvalue); 
         //dd($myvalue['reference']);
        $car_id= $myvalue['pos_id'];
        $manufacture = Manufacture::find($car_id);
        // $check = Vehicle::where('id', $manufacture->vehicle_id)->first(); 
         $vehicle = VehicleInfo::find($manufacture->vehicle_id);
        $kasu = new VehicleProduct();
        $kasu->vehicle_id = $vehicle->id;
        $kasu->pfi_number = $manufacture->pfi_number;
        $kasu->name = $vehicle->name;
        $kasu->code = $vehicle->code;
        $kasu->brand_id = $vehicle->brand_id;
        $kasu->category_id = $vehicle->category_id;
        $kasu->chassis_no = $manufacture->chassis_no;
        $kasu->engine_no = $manufacture->engine_no;
        $kasu->cost = $vehicle->cost;
        $kasu->price = $myvalue['price'];
        $kasu->warehouse_id = $myvalue['warehouse_id'];
        $kasu->is_active = 1;
        $kasu->user_id = Auth::user()->id;
        $kasu->save();
        $manufacture->delete();

        return response()->json(['Success'=>'Product Added Successfully']);
   
     }
     public function update(Request $request)
     {
         $data = $request->all();
         $myvalue = array();
         parse_str($data['data'],$myvalue); 
         //dd($myvalue['reference']);
        $car_id= $myvalue['edit_id'];
        $manufacture = Manufacture::find($car_id);
        $manufacture->chassis_no = $myvalue['chassis'];
        $manufacture->engine_no = $myvalue['engine'];
        $manufacture->save();
        return response()->json(['Success'=>'Product Added Successfully']);
   
     }
     public function vehicleGet($id)
     {  
             $allchassis = Manufacture::where('is_active', true)->where('id', '!=', $id)->get();
             foreach ($allchassis as $key => $kasuu) {
                 $product_sale[0][$key] = $kasuu->chassis_no;
                 $product_sale[1][$key] = $kasuu->engine_no;
              }
            $All = Vehicle::where('is_active',true)->get();
            foreach ($All as $key => $kasu) {
                $product_sale[2][$key] = $kasu->chassis_no;
                $product_sale[3][$key] = $kasu->engine_no;
             }
             $hopa = VehicleProduct::where('is_active',true)->get();
             foreach ($hopa as $key => $lil) {
                 $product_sale[4][$key] = $lil->chassis_no;
                 $product_sale[5][$key] = $lil->engine_no;
              }
            $Input = Manufacture::find($id);
            $product_sale['edit_ch'] = $Input->chassis_no;
            $product_sale['edit_en'] = $Input->engine_no; 
         return $product_sale;
     }
     public function productData(Request $request)
     {
         $columns = array( 
             0 => 'id', 
             1 => 'name', 
             2 => 'code',
             3 => 'chassis_no',
             4 => 'engine_no',
             5 => 'cost',
             6 => 'price',
             7 => 'warehouse'
         ); 
         
         $totalData = VehicleProduct::where('is_active', true)->where('qty', '>',0)->count();
         $totalFiltered = $totalData; 
 
         if($request->input('length') != -1)
             $limit = $request->input('length');
         else
             $limit = $totalData;
         $start = $request->input('start');
         $order = 'vehicle_products.'.$columns[$request->input('order.0.column')];
         $dir = $request->input('order.0.dir');
         if(empty($request->input('search.value'))){
             $products = VehicleProduct::with('category', 'brand','warehouse')->offset($start)
                         ->where('is_active', true)
                         ->where('qty','>', 0)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
         }
         else
         {
             $search = $request->input('search.value'); 
             $products =  VehicleProduct::select('vehicle_products.*')
                         ->with('category', 'brand','warehouse')
                         ->join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                         ->leftjoin('vehiclebrand', 'vehicle_products.brand_id', '=', 'vehiclebrand.id')
                         ->rightjoin('warehouses', 'vehicle_products.warehouse_id', '=', 'warehouses.id')
                         ->where([
                             ['vehicle_products.name', 'LIKE', "%{$search}%"],
                             ['vehicle_products.qty', '>', 0],
                             ['vehicle_products.is_active', true]
                         ]) 
                         ->orWhere([
                             ['vehicle_products.code', 'LIKE', "%{$search}%"],
                             ['vehicle_products.qty', '>', 0],
                             ['vehicle_products.is_active', true]
                         ])
                         ->orWhere([
                            ['vehicle_products.chassis_no', 'LIKE', "%{$search}%"],
                            ['vehicle_products.qty', '>', 0],
                            ['vehicle_products.is_active', true]
                         ])
                         ->orWhere([
                            ['vehicle_products.engine_no', 'LIKE', "%{$search}%"],
                            ['vehicle_products.qty', '>', 0],
                            ['vehicle_products.is_active', true]
                         ])
                         ->orWhere([
                             ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                             ['vehicle_products.qty', '>', 0],
                             ['vehicle_products.is_active', true]
                         ])
                         ->orWhere([
                             ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                             ['vehicle_products.qty', '>', 0],
                             ['vehicle_products.is_active', true]
                         ])
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)->get();
 
             $totalFiltered = VehicleProduct::
                             join('vehiclecategory', 'vehicle_products.category_id', '=', 'vehiclecategory.id')
                             ->leftjoin('vehiclebrand', 'vehicle_products.brand_id', '=', 'vehiclebrand.id')
                             ->rightjoin('warehouses', 'vehicle_products.warehouse_id', '=', 'warehouses.id')
                             ->where([
                                 ['vehicle_products.name','LIKE',"%{$search}%"],
                                 ['vehicle_products.qty', '>', 0],
                                 ['vehicle_products.is_active', true]
                             ])
                             ->orWhere([
                                 ['vehicle_products.code', 'LIKE', "%{$search}%"],
                                 ['vehicle_products.qty', '>', 0],
                                 ['vehicle_products.is_active', true]
                             ])
                             ->orWhere([
                                ['vehicle_products.chassis_no', 'LIKE', "%{$search}%"],
                                ['vehicle_products.qty', '>', 0],
                                ['vehicle_products.is_active', true]
                             ])
                             ->orWhere([
                                ['vehicle_products.engine_no', 'LIKE', "%{$search}%"],
                                ['vehicle_products.qty', '>', 0],
                                ['vehicle_products.is_active', true]
                             ])
                             ->orWhere([
                                 ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                                 ['vehiclecategory.is_active', true],
                                 ['vehicle_products.qty', '>', 0],
                                 ['vehicle_products.is_active', true]
                             ])
                             ->orWhere([
                                 ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                                 ['vehicle_products.qty', '>', 0],
                                 ['vehicle_products.is_active', true]
                             ])
                             ->count();
         } 
         $data = array();

         if(!empty($products))
         {
             foreach ($products as $key=>$product)
             {
                 $nestedData['id'] = $product->id;
                 $nestedData['key'] = $key+1;
                 $nestedData['name'] = $product->name;
                 $nestedData['code'] = $product->code;
                 $nestedData['chassis'] = $product->chassis_no;
                 $nestedData['engine'] = $product->engine_no;
                 $nestedData['cost'] = number_format($product->cost, 2);
                 $nestedData['price'] = number_format($product->price, 2);
                 $nestedData['warehouse'] = Warehouse::find($product->warehouse_id)->name;
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
}
