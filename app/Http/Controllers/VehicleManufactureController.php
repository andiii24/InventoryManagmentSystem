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
use App\VehicleCategory;
use Carbon\Carbon;
use App\Tax; 
use App\Warehouse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class VehicleManufactureController extends Controller
{
  //
  public function store(Request $request)
  {
      $data = $request->all();
      $myvalue = array();
      parse_str($data['data'],$myvalue);
      $V_IDD= $myvalue['manufacture_id'];
      $vehicleInfo = VehicleInfo::find($V_IDD); 
      $vehicleInfo->manufacture_warehouse = $myvalue['warehouse_id'];
      $vehicleInfo->save();
      $Idd = $myvalue['vehicle_id'];
      foreach ($Idd as $i => $id) {
        $vehicleInfo = VehicleInfo::find($V_IDD);
        $vehicleInfo->man_qty += 1;
        $vehicleInfo->save();
        $Raw= Vehicle::find($id);
        $Mnfc = new Manufacture();
        $Mnfc->vehicle_id = $vehicleInfo->id;
        $Mnfc->pfi_number = $vehicleInfo->pfi_number;
        $Mnfc->name = $vehicleInfo->name;
        $Mnfc->code = $vehicleInfo->code;
        $Mnfc->brand_id = $vehicleInfo->brand_id;
        $Mnfc->category_id = $vehicleInfo->category_id;
        $Mnfc->chassis_no = $Raw->chassis_no;
        $Mnfc->engine_no = $Raw->engine_no;
        $Mnfc->cost = $vehicleInfo->cost;
        $Mnfc->warehouse_id = $myvalue['warehouse_id'];
        $Mnfc->is_active = true;
        $Mnfc->user_id = Auth::user()->id;
        $Mnfc->save();
        $Raw->delete();
    }

      return response()->json(['Success'=>'Manufacture Added Successfully']);

  }
  public function addComplete(Request $request)
  {
      $data = $request->all();
      $myvalue = array();
      parse_str($data['data'],$myvalue);
      $vid= $myvalue['finish_id'];
      $Iddd = $myvalue['vehicle_id'];
      foreach ($Iddd as $i => $Veh_Id) {
        $vehicleInfo = VehicleInfo::find($vid);
        $vehicleInfo->man_qty -= 1;
        $vehicleInfo->save();
        $Finished = Manufacture::find($Veh_Id);
        $Finished->status = 2;
        $Finished->complete_date = Carbon::now();
        $Finished->completed_by = Auth::user()->id;
        $Finished->save();
      }
      return response()->json(['Success'=>'Complete added Successfully']);

  }
  public function getInProgress($id)
  {
      $lims_product_sale_data = Manufacture::where('vehicle_id', $id)->where('status',1)->where('is_active', true)->orderBy('created_at', 'desc')->get();
      foreach ($lims_product_sale_data as $key => $kasu) {
          $product_sale[0][$key] = $kasu->id;
          $product_sale[1][$key] = $kasu->chassis_no;
          $product_sale[2][$key] = $kasu->engine_no;
      } 
    return $product_sale;
  }
  public function index()
  {
    $warehouse = Warehouse::where('is_active', true)->orderBy('created_at', 'desc')->get();
          return view('Vehicles.manufacture', compact('warehouse'));
     
  }
  public function manufactureData(Request $request)
  { 
      $columns = array( 
          0 =>'id',
          1 => 'name',  
          2 => 'code',
          3 => 'brand_id',
          4 => 'category_id',
          5 => 'qty',
          6 => 'warehouse'
      );
      
      $totalData = VehicleInfo::where('is_active', true)->where('man_qty','>',0)->count();
      $totalFiltered = $totalData; 

      if($request->input('length') != -1)
          $limit = $request->input('length');
      else
          $limit = $totalData;
      $start = $request->input('start'); 
      $order = 'vehicleinfo.'.$columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      if(empty($request->input('search.value'))){
          $products = VehicleInfo::with('category', 'brand')->offset($start)
                      ->where('is_active', true)
                      ->where('man_qty', '>',0)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();
      }
      else
      {
          $search = $request->input('search.value'); 
          $products =  VehicleInfo::select('vehicleinfo.*')
                      ->with('category', 'brand')
                      ->join('vehiclecategory', 'vehicleinfo.category_id', '=', 'vehiclecategory.id')
                      ->leftjoin('vehiclebrand', 'vehicleinfo.brand_id', '=', 'vehiclebrand.id')
                      ->where([
                          ['vehicleinfo.name', 'LIKE', "%{$search}%"],
                          ['vehicleinfo.man_qty', '>', 0],
                          ['vehicleinfo.is_active', true]
                      ])
                      ->orWhere([
                          ['vehicleinfo.code', 'LIKE', "%{$search}%"],
                          ['vehicleinfo.man_qty', '>', 0],
                          ['vehicleinfo.is_active', true]
                      ])
                      ->orWhere([
                          ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                          ['vehicleinfo.man_qty', '>', 0],
                          ['vehicleinfo.is_active', true]
                      ])
                      ->orWhere([
                          ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                          ['vehicleinfo.man_qty', '>', 0],
                          ['vehicleinfo.is_active', true]
                      ])
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)->get();

          $totalFiltered = VehicleInfo::
                            join('vehiclecategory', 'vehicleinfo.category_id', '=', 'vehiclecategory.id')
                          ->leftjoin('vehiclebrand', 'vehicleinfo.brand_id', '=', 'vehiclebrand.id')
                          ->where([
                              ['vehicleinfo.name','LIKE',"%{$search}%"],
                              ['vehicleinfo.man_qty', '>', 0],
                              ['vehicleinfo.is_active', true]
                          ])
                          ->orWhere([
                              ['vehicleinfo.code', 'LIKE', "%{$search}%"],
                              ['vehicleinfo.man_qty', '>', 0],
                              ['vehicleinfo.is_active', true]
                          ]) 
                          ->orWhere([
                              ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                              ['vehiclecategory.is_active', true],
                              ['vehicleinfo.man_qty', '>', 0],
                              ['vehicleinfo.is_active', true]
                          ])
                          ->orWhere([
                              ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                              ['vehicleinfo.man_qty', '>', 0],
                              ['vehicleinfo.is_active', true]
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
              if($product->brand_id)
              $nestedData['brand'] = VehicleBrand::find($product->brand_id)->name;
              else 
              $nestedData['brand'] = "N/A";
              if($product->category_id)
              $nestedData['category'] = VehicleCategory::find($product->category_id)->name;
              else
              $nestedData['category'] = "N/A";
              $nestedData['qty'] = $product->man_qty;
              $nestedData['warehouse'] = Warehouse::find($product->manufacture_warehouse)->name;
              $nestedData['options'] = '<div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                        '; 
                      $count = Manufacture::where('vehicle_id', $product->id)->where('is_active', true)->where('status',1)->count();
                      if($count > 0)  
                      { 
                  $nestedData['options'] .= '<li>
                     <button type="submit" data-id="'.$product->id.'" data-name="'.$product->name.'"  class="Add-Complete btn btn-link"  ><i class="fa fa-check" aria-hidden="true"></i> Add To Finished Good</button>
                      </li> </ul>
                      </div>';
                      } 
                 

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

  public function finishedIndex()
  {
          $warehouse = Warehouse::where('is_active', true)->orderBy('created_at', 'desc')->get();
          return view('Vehicles.finished', compact('warehouse'));
     
  } 
  public function finishedData(Request $request)
  {
      $columns = array( 
          0 =>'id',
          1 => 'name',  
          2 => 'code',
          3 => 'chassis_no',  
          4 => 'engine_no',
          5 => 'cost', 
          6 => 'warehouse'
      );
      
      $totalData = Manufacture::where('is_active', true)->where('status', 2)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'manufactures.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))){
            $products =Manufacture::with('category', 'brand','warehouse')->offset($start)
                        ->where('is_active', true)
                        ->where('status', 2)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }
        else
        {
            $search = $request->input('search.value'); 
            $products = Manufacture::select('manufactures.*')
                        ->with('category', 'brand','warehouse')
                        ->join('vehiclecategory', 'manufactures.category_id', '=', 'vehiclecategory.id')
                        ->leftjoin('vehiclebrand', 'manufactures.brand_id', '=', 'vehiclebrand.id')
                        ->rightjoin('warehouses', 'manufactures.warehouse_id', '=', 'warehouses.id')
                        ->where([
                            ['manufactures.name', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->orWhere([
                            ['manufactures.code', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->orWhere([
                            ['manufactures.chassis_no', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->orWhere([
                            ['manufactures.engine_no', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->orWhere([
                            ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->orWhere([
                            ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                            ['manufactures.status', '=', 2],
                            ['manufactures.is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered =Manufacture::
                            join('vehiclecategory', 'manufactures.category_id', '=', 'vehiclecategory.id')
                            ->leftjoin('vehiclebrand', 'manufactures.brand_id', '=', 'vehiclebrand.id')
                            ->rightjoin('warehouses', 'manufactures.warehouse_id', '=', 'warehouses.id')
                            ->where([
                                ['manufactures.name','LIKE',"%{$search}%"],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
                            ])
                            ->orWhere([
                                ['manufactures.code', 'LIKE', "%{$search}%"],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
                            ])
                            ->orWhere([
                                ['manufactures.chassis_no', 'LIKE', "%{$search}%"],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
                            ])
                            ->orWhere([
                                ['manufactures.engine_no', 'LIKE', "%{$search}%"],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
                            ])
                            ->orWhere([
                                ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                                ['vehiclecategory.is_active', true],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
                            ])
                            ->orWhere([
                                ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                                ['manufactures.status', '=', 2],
                                ['manufactures.is_active', true]
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
                if($product->warehouse_id)
                {
                    $nestedData['warehouse'] = Warehouse::find($product->warehouse_id)->name;
                }
              $nestedData['options'] = '<div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                          <li>
                             
                          </li>'; 
                  $nestedData['options'] .= '<li> 
                     <button type="button" data-id="'.$product->id.'" data-name="'.$product->warehouse_id.'"  class="AddProduct btn btn-link"><i class="dripicons-shopping-bag" aria-hidden="true"></i> Add Vehicle To POS (product)   </button>
                      </li> 
                      <li> 
                     <button type="button" data-id="'.$product->id.'"  class="edit-btn btn btn-link"><i class="fa fa-edit" aria-hidden="true"></i> Edit Vehicle </button>
                      </li>
                       </ul> </div>';
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
