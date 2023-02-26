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
use App\VehicleCategory;
use App\Manufacture;
use App\Proforma;
use App\ProformaCount;
use App\ProformaItem;
use App\VehicleProduct;
use Carbon\Carbon;
use App\Tax;
use App\Warehouse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class VehicleController extends Controller
{
    //
    public function index()  
    {  
        $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
        $warehouse = Warehouse::where('is_active', true)->get();
            return view('Vehicles.index', compact('warehouse','proformas')); 
       
    } 
    public function vehicleData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 => 'name',  
            2 => 'code',
            3 => 'brand_id',
            4 => 'category_id',
            5 => 'qty'
        );
        
        $totalData = VehicleInfo::where('is_active', true)->where('qty','>',0)->count();
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
                        ->where('qty', '>',0)
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
                            ['vehicleinfo.qty', '>', 0],
                            ['vehicleinfo.is_active', true]
                        ])
                        ->orWhere([
                            ['vehicleinfo.code', 'LIKE', "%{$search}%"],
                            ['vehicleinfo.qty', '>', 0],
                            ['vehicleinfo.is_active', true]
                        ])
                        ->orWhere([
                            ['vehicleinfo.pfi_number', 'LIKE', "%{$search}%"],
                            ['vehicleinfo.qty', '>', 0],
                            ['vehicleinfo.is_active', true]
                        ])
                        ->orWhere([
                            ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                            ['vehicleinfo.qty', '>', 0],
                            ['vehicleinfo.is_active', true]
                        ])
                        ->orWhere([
                            ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                            ['vehicleinfo.qty', '>', 0],
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
                                ['vehicleinfo.qty', '>', 0],
                                ['vehicleinfo.is_active', true]
                            ])
                            ->orWhere([
                                ['vehicleinfo.code', 'LIKE', "%{$search}%"],
                                ['vehicleinfo.qty', '>', 0],
                                ['vehicleinfo.is_active', true]
                            ]) 
                            ->orWhere([
                                ['vehicleinfo.pfi_number', 'LIKE', "%{$search}%"],
                                ['vehicleinfo.qty', '>', 0],
                                ['vehicleinfo.is_active', true]
                            ])
                            ->orWhere([
                                ['vehiclecategory.name', 'LIKE', "%{$search}%"],
                                ['vehiclecategory.is_active', true],
                                ['vehicleinfo.qty', '>', 0],
                                ['vehicleinfo.is_active', true]
                            ])
                            ->orWhere([
                                ['vehiclebrand.name', 'LIKE', "%{$search}%"],
                                ['vehicleinfo.qty', '>', 0],
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
                $nestedData['key'] = $key;
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
                $nestedData['qty'] = $product->qty;
                if($product->cost != "")
                $nestedData['cost'] = $product->cost;
                else 
                $nestedData['cost'] = "N/A";
                if($product->alert_quantity != "")
                $nestedData['alert'] = $product->alert_quantity;
                else
                $nestedData['alert'] = "N/A";
                $nestedData['warehouse'] = Warehouse::find($product->warehouse_id)->name;
                $nestedData['raw_qty'] = Vehicle::where('vehicle_id', $product->id)->where('is_active', true)->count();        
                $nestedData['manufacture'] = Manufacture::where('vehicle_id', $product->id)->where('status',1)->where('is_active', true)->count();  
                $nestedData['finished'] = Manufacture::where('vehicle_id', $product->id)->where('status',2)->where('is_active', true)->count(); 
                $nestedData['product'] = VehicleProduct::where('vehicle_id', $product->id)->where('is_active', true)->count();              
                //$nestedData['stock_worth'] = ($product->qty * $product->price).'/'.($product->qty * $product->cost);

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                            <button type="button" class="btn btn-link view-vehicle"><i class="fa fa-eye"></i> View Details</button> 
                            </li>'; 
                    $nestedData['options'] .= '<li>
                            <a href="'.route('vehicle.edit', $product->id).'" class="btn btn-link"><i class="fa fa-edit"></i> '.trans('file.edit').'</a>
                        </li>';
                        $count = Vehicle::where('vehicle_id', $product->id)->where('is_active', true)->count();
                        if($count > 0)  
                        { 
                    $nestedData['options'] .= '<li>
                       <button type="submit" data-id="'.$product->id.'" data-name="'.$product->name.'"  class="AddtoManufacture btn btn-link"  ><i class="fa fa-industry" aria-hidden="true"></i> Add to Manufacture</button>
                        </li>';
                        } 
                    $nestedData['options'] .= '
                            <li>
                              <button type="submit"  data-id="'.$product->id.'"  data-name="'.$product->name.'" id="DeleteVehicle" class="DeleteVehicle btn btn-link" ><i class="fa fa-trash"></i> '.trans("file.delete").'</button> 
                            </li>
                        </ul>
                    </div>';
                    $user = User::find($product->user_id)->name;
                $nestedData['vehicle'] = array( '["'.$product->id.'"', '"'.$product->name.'"', ' "'.$product->code.'"', ' "'.$nestedData['brand'].'"', ' "'.$nestedData['category'].'"', ' "'.$nestedData['cost'].'"', ' "'.$nestedData['alert'].'"', ' "'.$nestedData['warehouse'].'"', ' "'.$user.'"', '"'.Carbon::parse($product->created_at)->format('d-m-Y H:i').'"', ' "'.$product->detail.'"]'
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
    public function getForManufacture($id)
    {
        $lims_product_sale_data = Vehicle::where('vehicle_id', $id)->where('is_active', true)->orderBy('created_at', 'desc')->get();
        foreach ($lims_product_sale_data as $key => $kasu) {
            $product_sale[0][$key] = $kasu->id;
            $product_sale[1][$key] = $kasu->chassis_no;
            $product_sale[2][$key] = $kasu->engine_no;
        } 
      $product_sale['warehouse'] = VehicleInfo::find($id)->warehouse_id;
      return $product_sale;
    }
    public function getRawVehicle($id)
    {  
        $lims_product_sale_data = Vehicle::where('vehicle_id', $id)->where('is_active', true)->orderBy('name', 'asc')->get();
            foreach ($lims_product_sale_data as $key => $kasu) {
                $product_sale[0][$key] = $kasu->id;
                $product_sale[1][$key] = $kasu->chassis_no;
                $product_sale[2][$key] = $kasu->engine_no;
                $product_sale[3][$key] = User::find($kasu->user_id)->name;
            }
            $allchassis = Vehicle::where('is_active', true)->get();
            foreach ($allchassis as $key => $kasuu) {
                $product_sale[4][$key] = $kasuu->chassis_no;
                $product_sale[5][$key] = $kasuu->engine_no;
             }
             $chassis1 = Manufacture::where('is_active', true)->get();
             foreach ($chassis1 as $key => $ab) {
                 $product_sale[21][$key] = $ab->chassis_no;
                 $product_sale[22][$key] = $ab->engine_no;
              }
              $chassis13 = VehicleProduct::where('is_active', true)->get();
              foreach ($chassis13 as $key => $dago) {
                  $product_sale[23][$key] = $dago->chassis_no;
                  $product_sale[24][$key] = $dago->engine_no;
               }
        $manufacture_list = Manufacture::where('vehicle_id',$id)->where('status',1)->where('is_active', true)->get();
            foreach ($manufacture_list as $key => $progress) {
                $product_sale[6][$key] = $progress->id;
                $product_sale[7][$key] = $progress->chassis_no;
                $product_sale[8][$key] = $progress->engine_no;
                $product_sale[9][$key] = User::find($progress->user_id)->name;
                $product_sale[10][$key] = Carbon::parse($progress->created_at)->format('l, d-m-Y');
            }
        $finished_list = Manufacture::where('vehicle_id',$id)->where('status', '=', 2)->where('is_active', true)->get();
            foreach ($finished_list as $key => $complete) {
                $product_sale[11][$key] = $complete->id;
                $product_sale[12][$key] = $complete->chassis_no;
                $product_sale[13][$key] = $complete->engine_no;
                $product_sale[14][$key] = User::find($complete->completed_by)->name;
                $product_sale[15][$key] = Carbon::parse($complete->complete_date)->format('l, d-m-Y');
            }
        $vehicle_product_list = VehicleProduct::where('vehicle_id',$id)->where('is_active', true)->get();
            foreach ($vehicle_product_list as $key => $pdct) {
                $product_sale[16][$key] = $pdct->id;
                $product_sale[17][$key] = $pdct->chassis_no;
                $product_sale[18][$key] = $pdct->engine_no;
                $product_sale[19][$key] = User::find($pdct->user_id)->name;
                $product_sale[20][$key] = Carbon::parse($pdct->created_at)->format('l, d-m-Y');
            }
        return $product_sale;
    }
    public function rawUpdate(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $id=$myvalue['edit_id'];
        $Brand = Vehicle::find($id);
        $Brand->chassis_no = $myvalue['chassis_num'];
        $Brand->engine_no = $myvalue['engine_num'];
        $Brand->user_id = Auth::user()->id;
        $Brand->save();
        return response()->json(['Success'=>'Vehicle Updated Successfully']);
    } 
    public function rawDelete($id)
    {
        $Export = Vehicle::findOrFail($id);
        $expp = VehicleInfo::find($Export->vehicle_id);
        $expp->qty -= 1;
        $expp->save();
        $Export->delete();
            $success = true;
        return response()->json([
            'success' => $success
        ]);

    } 
    public function importVehicle(Request $request)
    {   
        //get file
        $upload=$request->file('file');
        $proforma_id = $request->pfi_id;
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        //open and read
        $file=fopen($filePath, 'r');
        $header= fgetcsv($file);
        $escapedHeader=[];
        //validate
        foreach ($header as $key => $value) {
            $lheader=strtolower($value);
            $escapedItem=preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }
        //looping through other columns
        while($columns=fgetcsv($file))
        {
            foreach ($columns as $key => $value) {
                $value=preg_replace('/\D/','',$value);
            }
           $data= array_combine($escapedHeader, $columns);
           if($data['code'] =="" || $data['name'] == "" || $data['chassisno'] == "" || $data['engineno'] == "" || $data['warehouse'] =="" )
           {
            return redirect('vehicles')->with('not_permitted', 'Imported CSV must include all required fields, The rows before these row are stored !');
           }
           $find = Manufacture::where('chassis_no', $data['chassisno'])->where('is_active', true)->first();
           $find1 = VehicleProduct::where('chassis_no', $data['chassisno'])->where('is_active', true)->first();
           $find_raw = Vehicle::where('chassis_no', $data['chassisno'])->where('is_active', true)->first();
           if($find || $find1 || $find_raw)
           {
            return redirect('vehicles')->with('not_permitted', 'Chassis No : '.$data["chassisno"].' Already Exist, Please add unique one. The rows before these row are stored !');
           }
           $Kas_Ab = Manufacture::where('engine_no', $data['engineno'])->where('is_active', true)->first();
           $Kas_Ab1 = VehicleProduct::where('engine_no', $data['engineno'])->where('is_active', true)->first();
           $Kas_Ab2 = Vehicle::where('engine_no', $data['engineno'])->where('is_active', true)->first();
           if($Kas_Ab || $Kas_Ab1 || $Kas_Ab2)
           {
            return redirect('vehicles')->with('not_permitted', 'Engine No : '.$data["engineno"].' Already Exist, Please add unique one. The rows before these row are stored !');
           }
           $lims_warehouse_data = Warehouse::firstOrCreate(['name' => $data['warehouse'], 'is_active' => true]);
            $CountCheck = ProformaCount::where('pro_id', $proforma_id)->first();
            if($CountCheck)
            {
            $Dag_Ab = ProformaCount::find($CountCheck->id);
            $Dag_Ab->vehicle_qty += 1;
            $Dag_Ab->save();
            }
            else
            {
            $Orginal = Proforma::find($proforma_id);
            $Orginal->status = 6;
            $Orginal->save();
            $proItem = ProformaItem::where('proforma_id', $Orginal->id)->count();
            $ItemQty = ProformaItem::where('proforma_id', $Orginal->id)->sum('qty');
            $Dag_Ab = new ProformaCount();
            $Dag_Ab->pro_id = $Orginal->id;
            $Dag_Ab->purchase_items = $proItem;
            $Dag_Ab->purchase_qty = $ItemQty;
            $Dag_Ab->vehicle_qty = 1;
            $Dag_Ab->save();
            }
           
           $info = VehicleInfo::where('name', $data['name'])->where('code', $data['code'])->where('is_active', true)->first();
           if($info)
           {
            $lilkas = VehicleInfo::find($info->id);
            $lilkas->qty += 1; 
            if($data['purchaseprice'] != "")
            {
            $lilkas->cost = str_replace(",","",$data['purchaseprice']);
            }
            if($data['productdetails'] != "")
            {
                $details = str_replace('"', '@', $data['productdetails']);
                $lilkas->detail = $details;
            }
            $lilkas->save();
            $product = Vehicle::firstOrNew([ 'chassis_no'=>$data['chassisno'], 'engine_no'=>$data['engineno'], 'is_active'=>true ]);
            $product->vehicle_id = $info->id; 
            $product->pfi_number = Proforma::find($proforma_id)->pfi_number;
            $product->name = $info->name;
            $product->code = $info->code;
            $product->vehicle_brand_id = $info->brand_id;
            $product->vehicle_category_id = $info->category_id;
            $product->chassis_no = $data['chassisno'];
            $product->engine_no = $data['engineno'];
            $product->cost = $info->cost;
            $product->warehouse_id = $lims_warehouse_data->id;
            $details = str_replace('"', '@', $data['productdetails']);
            $product->product_details = $details;
            $product->is_active = true;
            $product->user_id = Auth::user()->id;
            $product->save();
           }
           else
           {
            if($data['brand'] != 'N/A' && $data['brand'] != ''){
                $lims_brand_data = VehicleBrand::firstOrCreate(['name' => $data['brand'], 'is_active' => true]);
                $brand_id = $lims_brand_data->id;
               }
            else 
                $brand_id = null;
           $lims_category_data = VehicleCategory::firstOrCreate(['name' => $data['category'], 'is_active' => true]);
            $newCar = new VehicleInfo();
            $newCar->pfi_number = Proforma::find($proforma_id)->pfi_number;
            $newCar->name = $data['name'];
            $newCar->code = $data['code'];
            $newCar->brand_id = $brand_id;
            $newCar->category_id = $lims_category_data->id;
            $newCar->qty = 1;
            $birru =  str_replace(",","",$data['purchaseprice']);
            $newCar->cost = $birru;
            $newCar->warehouse_id = $lims_warehouse_data->id;
            $newCar->user_id = Auth::user()->id;
            $newCar->is_active = true;
            $newCar->save(); 
            $product = Vehicle::firstOrNew([ 'chassis_no'=>$data['chassisno'], 'engine_no'=>$data['engineno'], 'is_active'=>true ]);
            $product->vehicle_id = $newCar->id;
            $product->pfi_number = Proforma::find($proforma_id)->pfi_number;
            $product->name = $newCar->name; 
            $product->code = $newCar->code;
            $product->vehicle_brand_id = $newCar->brand_id;
            $product->vehicle_category_id = $newCar->category_id;
            $product->chassis_no = $data['chassisno'];
            $product->engine_no = $data['engineno'];
            if($data['purchaseprice'] != "")
            {
            $product->cost = str_replace(",","",$data['purchaseprice']);
            }
            $product->warehouse_id = $lims_warehouse_data->id;
            $details = str_replace('"', '@', $data['productdetails']);
            $product->product_details = $details;
            $product->is_active = true;
            $product->user_id = Auth::user()->id;
            $product->save();
           }

         }
         return redirect('vehicles')->with('import_message', 'Vehicles imported successfully');
    } 
    public function edit($id)
    {
            $brand = VehicleBrand::where('is_active', true)->get();
            $category = VehicleCategory::where('is_active', true)->get();
            $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
            $vehicle = VehicleInfo::where('id', $id)->first();
            $vehiclePro = Proforma::where('pfi_number', $vehicle->pfi_number)->first();
            $warehouse = Warehouse::where('is_active', true)->get();
            return view('Vehicles.edit',compact(  'brand', 'category', 'proformas', 'vehiclePro', 'vehicle','warehouse'));
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $name = $myvalue['name'];
        $id=$myvalue['edit_id']; 
        $idp = $myvalue['old_pfi'];
        if($myvalue['old_pfi'] != $myvalue['pfi_id'])
        {
            $Pro_Old = ProformaCount::where('pro_id',$myvalue['old_pfi'])->first();
            $Update = ProformaCount::find($Pro_Old->id);
            $Update->vehicle_qty -= 1;
            $Update->save();
            $ProFind = ProformaCount::where('pro_id',$myvalue['pfi_id'])->first();
            if($ProFind)
            {
                $MyPro = ProformaCount::find($myvalue['pfi_id']);
                $MyPro->vehicle_qty += 1;
                $MyPro->save();
            }
            else
            {
                $Orginal = Proforma::find($myvalue['pfi_id']);
                $Orginal->status = 6;
                $Orginal->save();
                $proItem = ProformaItem::where('proforma_id', $Orginal->id)->count();
                $ItemQty = ProformaItem::where('proforma_id', $Orginal->id)->sum('qty');
                $ProCount = new ProformaCount(); 
                $ProCount->pro_id = $Orginal->id;
                $ProCount->purchase_items = $proItem;
                $ProCount->purchase_qty = $ItemQty;
                $ProCount->vehicle_qty = 1;
                $ProCount->save();
    
            }
        }
        $Brand = VehicleInfo::find($id);
        if($myvalue['old_pfi'] != $myvalue['pfi_id'])
        {
            $Brand->pfi_number = Proforma::find($myvalue['pfi_id'])->pfi_number;
        }
        $Brand->name = $name;
        $Brand->code = $myvalue['code'];
        if($myvalue['vehicle_brand_id']!="" || $myvalue['vehicle_brand_id']!= null) {
            $Brand->brand_id = $myvalue['vehicle_brand_id'];
            }
        $Brand->category_id = $myvalue['vehicle_category_id'];
        $Brand->cost = $myvalue['cost'];
        if($myvalue['alert_quantity']!="" || $myvalue['alert_quantity']!= null) {
        $Brand->alert_quantity = $myvalue['alert_quantity'];
            }
        $Brand->warehouse_id = $myvalue['warehouse_id'];
        if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
                $detail = str_replace('"', '@', $myvalue['product_details']);
                $Brand->detail = $detail;
            }
        $Brand->user_id = Auth::user()->id;
        $Brand->save();
        Vehicle::where('vehicle_id', $Brand->id)
            ->update([
                'pfi_number' => $Brand->pfi_number,
                'name' => $Brand->name,
                'code' => $Brand->code,
                'vehicle_brand_id' => $Brand->brand_id,
                'vehicle_category_id' => $Brand->category_id,
                'cost' => $Brand->cost
                ]);
        Manufacture::where('vehicle_id', $Brand->id)
                ->update([
                'pfi_number' => $Brand->pfi_number,
                'name' => $Brand->name,
                'code' => $Brand->code,
                'brand_id' => $Brand->brand_id,
                'category_id' => $Brand->category_id,
                'cost' => $Brand->cost
                ]);
        return response()->json(['Success'=>'Vehicle Updated Successfully']);
    } 
    public function destroy($id)
    {
        $lims_product_data3 = Manufacture::where('vehicle_id', $id)->where('is_active', true)->get();
        foreach ($lims_product_data3 as $product_data) {
            $product_data->is_active = false;
            $product_data->save();
         } 
        $lims_product_data5 = VehicleProduct::where('vehicle_id', $id)->where('is_active', true)->get();
        foreach ($lims_product_data5 as $product_data) {
            $product_data->is_active = false;
            $product_data->save();
        }
        $list_raws = Vehicle::where('vehicle_id', $id)->where('is_active', true)->get();
        foreach ($list_raws as $product_data) {
            $product_data->is_active = false;
            $product_data->save();
        } 
        $Export = VehicleInfo::findOrFail($id);
        $Export->is_active = false;
        $Export->save();
            $success = true;
        return response()->json([
            'success' => $success
        ]);

    }
    public function DeleteBySelection(Request $request)
    {
       foreach ($request['brandIdArray'] as $id) {
        $lims_product_data1 = Manufacture::where('vehicle_id', $id)->get();
        foreach ($lims_product_data1 as $product_data) {
            $product_data->is_active = false;
            $product_data->save();
         } 
         $lims_product_data2 = VehicleProduct::where('vehicle_id', $id)->get();
         foreach ($lims_product_data2 as $product_data) {
             $product_data->is_active = false;
             $product_data->save();
          } 
        $lims_product_data10 = Vehicle::where('vehicle_id', $id)->get();
          foreach ($lims_product_data10 as $product_data) {
              $product_data->is_active = false;
              $product_data->save();
           } 
       $cat= VehicleInfo::findOrFail($id);
       $cat->is_active = false;
       $cat->save();
       }
       return response()->json(['Success'=>'Vehicles Deleted Successfully']);
    }
    public function create()  
    {
          //  $lims_product_list_without_variant = $this->productWithoutVariant();
           // $lims_product_list_with_variant = $this->productWithVariant();
            $lims_brand_list = VehicleBrand::orderBy('name', 'asc')->get();
            $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
            $vehicle = Vehicle::where('is_active', true)->get();
            $lims_category_list = VehicleCategory::where('is_active', true)->orderBy('name', 'asc')->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $list1 = Manufacture::where('is_active',true)->get();
            $list2 = VehicleProduct::where('is_active',true)->get();
            return view('Vehicles.create',compact( 'lims_brand_list', 'list1', 'list2','proformas', 'lims_category_list', 'vehicle', 'lims_warehouse_list'));
      
    }
    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }
    public function store(Request $request)
    {
      
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $name = $myvalue['name'];
        $code = $myvalue['code'];
        $detail = str_replace('"', '@', $myvalue['product_details']);
        $ProFind = ProformaCount::where('pro_id',$myvalue['pfi_number'])->first();
        if($ProFind)
        {
            $MyPro = ProformaCount::find($ProFind->id);
            $MyPro->vehicle_qty += 1;
            $MyPro->save();
        }
        else
        {
            $Orginal = Proforma::find($myvalue['pfi_number']);
            $Orginal->status = 6;
            $Orginal->save();
            $proItem = ProformaItem::where('proforma_id', $Orginal->id)->count();
            $ItemQty = ProformaItem::where('proforma_id', $Orginal->id)->sum('qty');
            $ProCount = new ProformaCount();
            $ProCount->pro_id = $Orginal->id;
            $ProCount->purchase_items = $proItem;
            $ProCount->purchase_qty = $ItemQty;
            $ProCount->vehicle_qty = 1;
            $ProCount->save();

        }
        $Info = VehicleInfo::where('name',$name)->where('code',$code)->where('is_active',true)->first();
        if($Info) 
        {  
            $VHCL = VehicleInfo::find($Info->id);
            $VHCL->pfi_number = Proforma::find($myvalue['pfi_number'])->pfi_number;
            $VHCL->qty += 1;
            $VHCL->cost = $myvalue['cost']; 
            $VHCL->detail = $detail; 
            $VHCL->save();
            $Brand = new Vehicle(); 
            $Brand->vehicle_id = $Info->id;
            $Brand->pfi_number =  Proforma::find($myvalue['pfi_number'])->pfi_number;
            $Brand->name = $name;
            $Brand->code = $code;
            if($Info->brand_id !="") 
            { 
                $Brand->vehicle_brand_id = $Info->brand_id;
            } 
            $Brand->vehicle_category_id = $Info->category_id;
            $Brand->chassis_no = $myvalue['chassis_no'];
            $Brand->engine_no = $myvalue['engine_no'];
            $Brand->cost = $myvalue['cost']; 
            $Brand->warehouse_id = $Info->warehouse_id;
                if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
                    $detail = str_replace('"', '@', $myvalue['product_details']);
                    $Brand->product_details = $detail;
                }
            $Brand->is_active = 1;
            $Brand->user_id = Auth::user()->id;
            $Brand->save();
            
        }
        else
        {
            $kasu = new VehicleInfo();
            $kasu->pfi_number =  Proforma::find($myvalue['pfi_number'])->pfi_number;
            $kasu->name = $myvalue['name'];
            $kasu->code = $myvalue['code'];
            if($myvalue['vehicle_brand_id']!="" || $myvalue['vehicle_brand_id']!= null)
                {
            $kasu->brand_id = $myvalue['vehicle_brand_id'];
                 }
            $kasu->category_id = $myvalue['vehicle_category_id'];
            $kasu->qty = 1 ;
            $kasu->cost = $myvalue['cost'];
            $kasu->warehouse_id = $myvalue['warehouse_id'];
            if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
                $detail = str_replace('"', '@', $myvalue['product_details']);
                $kasu->detail = $detail;
            } 
            $kasu->user_id = Auth::user()->id;
            $kasu->is_active = true ;
            $kasu->save();
            $Brand = new Vehicle(); 
            $Brand->vehicle_id = $kasu->id;
            $Brand->pfi_number =  Proforma::find($myvalue['pfi_number'])->pfi_number;
            $Brand->name = $name;
            $Brand->code = $code;
            if($kasu->brand_id !="") {
               $Brand->vehicle_brand_id = $kasu->brand_id;
                }
            $Brand->vehicle_category_id = $kasu->category_id;
            $Brand->chassis_no = $myvalue['chassis_no'];
            $Brand->engine_no = $myvalue['engine_no'];
            $Brand->cost = $myvalue['cost'];
            $Brand->warehouse_id = $myvalue['warehouse_id'];
                if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
                    $detail = str_replace('"', '@', $myvalue['product_details']);
                    $Brand->product_details = $detail;
                } 
            $Brand->is_active = 1;
            $Brand->user_id = Auth::user()->id;
            $Brand->save();
        }
        return response()->json(['Success'=>'Vehicle Added Successfully']);

    } 
}
 