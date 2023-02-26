<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth; 
use App\User;
use App\Product;
use App\Manufacture;
use App\Vehicle;
use App\VehicleInfo;
use App\VehicleProduct;
use App\VehicleBrand;
use App\VehicleCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class VehicleBrandController extends Controller
{
    //
    public function brand()
    {
            $brands = VehicleBrand::where('is_active',true)->get();
            return view('Vehicles.brand',  compact('brands'));

    }
    public function store(Request $request)
    {
        $time=Carbon::now();
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $Brand = new VehicleBrand();
        $Brand->name = $myvalue['brand_name'];
        $Brand->user_id = Auth::user()->id;
        $Brand->created_at = $time;
        $Brand->save();
        return response()->json(['Success'=>'Brand Added Successfully']);

    }
    public function update(Request $request)
    {
        $time=Carbon::now();
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $ref_id = $myvalue['edit_id'];
        //dd($myvalue['reference']);
        $Brand = VehicleBrand::find($ref_id);
        $Brand->name = $myvalue['edit_name'];
        $Brand->updated_at = $time;
        $Brand->save();
        return response()->json(['Success'=>'Brand Updated Successfully']);

    }
    public function delete($id)
    {
        $Export = VehicleBrand::findOrFail($id);
        $Export->is_active = false;
        $Export->save();
            $success = true;
        return response()->json([
            'success' => $success
        ]);

    }
    public function deleteBySelection(Request $request)
    {
        foreach ($request['brandIdArray'] as $id) {
           $cat= VehicleBrand::findOrFail($id);
           $cat->is_active = false;
           $cat->save();
           }
       return response()->json(['Success'=>'Brands Deleted Successfully']);
    }
    // Category Methods
    public function index()
    {
        $lims_categories = VehicleCategory::pluck('name', 'id');
        $lims_category_all = VehicleCategory::get();
        return view('Vehicles.category',compact('lims_categories', 'lims_category_all'));
    }
    
    public function categoryData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'name',
            2=> 'parent_id',
            3=> 'is_active',
            3=> 'user_id',
        );
         
        $totalData = VehicleCategory::where('is_active', true)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
            $categories = VehicleCategory::offset($start)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  VehicleCategory::where([
                            ['name', 'LIKE', "%{$search}%"],
                            ['is_active', true]
                        ])->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = VehicleCategory::where([
                            ['name','LIKE',"%{$search}%"],
                            ['is_active', true]
                        ])->count();
        }
        $data = array();
        if(!empty($categories))
        {
            foreach ($categories as $key=>$category)
            {
                $nestedData['id'] = $category->id;
                $nestedData['key'] = $key;

                $nestedData['name'] = $category->name;

                if($category->parent_id)
                    $nestedData['parent_id'] = VehicleCategory::find($category->parent_id)->name;
                else
                    $nestedData['parent_id'] = "N/A";

                $nestedData['number_of_product'] = $category->product()->where('is_active', true)->count();
                $nestedData['stock_qty'] = $category->product()->where('is_active', true)->sum('qty');
                $total_price = $category->product()->where('is_active', true)->sum(DB::raw('price * qty'));
                $total_cost = $category->product()->where('is_active', true)->sum(DB::raw('cost * qty'));
                 
                if(config('currency_position') == 'prefix')
                    $nestedData['stock_worth'] = config('currency').' '.$total_price.' / '.config('currency').' '.$total_cost;
                else
                    $nestedData['stock_worth'] = $total_price.' '.config('currency').' / '.$total_cost.' '.config('currency');

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li><button type="button" data-id="'.$category->id.'"  class="open-EditCategoryDialog btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i> '.trans("file.edit").'</button></li>
                            <li class="divider"></li>
                            <li>
                                <li><button type="button" data-id="'.$category->id.'" data-name="'.$category->name.'"  id="kt-delete"  class="DeleteCategoryDialog btn btn-link"   ><i class="dripicons-trash"></i> '.trans("file.delete").'</button></li>                   
                            </li>    
                            </ul>
                        </div>';
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
    public function create(Request $request)
    {
        $time=Carbon::now();
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $Brand = new VehicleCategory();
        $Brand->name = $myvalue['cat_name'];
        if( $myvalue['parent_id']!=null)
        {
            $Brand->parent_id = $myvalue['parent_id'];
        }
        else
        {
            $Brand->parent_id = 0;  
        }
        $Brand->is_active = 1;
        $Brand->user_id = Auth::user()->id;
        $Brand->created_at = $time;
        $Brand->save();
        return response()->json(['Success'=>'Category Added Successfully']);

    }
    public function edit($id)
    {
        $lims_category_data = VehicleCategory::findOrFail($id);
        $lims_parent_data = VehicleCategory::where('id', $lims_category_data['parent_id'])->first();
        if($lims_parent_data)
            $lims_category_data['parent'] = $lims_parent_data['name'];
        return $lims_category_data;
    }
    public function CatUpdate(Request $request)
    {
        $time=Carbon::now();
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $ref_id = $myvalue['edit_id'];
        //dd($myvalue['reference']);
        $Brand = VehicleCategory::find($ref_id);
        $Brand->name = $myvalue['edit_name'];
        $Brand->updated_at = $time;
        $Brand->save();
        return response()->json(['Success'=>'Category Updated Successfully']);

    }
    public function CatDelete($id)
    {
        VehicleInfo::where('category_id', $id)
             ->update([
             'is_active' => false
            ]);
        Vehicle::where('vehicle_category_id', $id)
            ->update([
            'is_active' => false
           ]);
        Manufacture::where('category_id', $id)
            ->update([
            'is_active' => false
            ]);
        VehicleProduct::where('category_id', $id)
            ->update([
            'is_active' => false
            ]);
        $Export = VehicleCategory::findOrFail($id);
        $Export->is_active = false;
        $Export->save();
      
            $success = true;
        return response()->json([
            'success' => $success
        ]);

    }
    public function CatDeleteBySelection(Request $request)
    {
       foreach ($request['brandIdArray'] as $id) {
        VehicleInfo::where('category_id', $id)
            ->update([
            'is_active' => false
            ]);
        Vehicle::where('vehicle_category_id', $id)
            ->update([
            'is_active' => false
            ]);
        Manufacture::where('category_id', $id)
            ->update([
            'is_active' => false
            ]);
        VehicleProduct::where('category_id', $id)
            ->update([
            'is_active' => false
            ]);
       $cat= VehicleCategory::findOrFail($id);
       $cat->is_active = false;
       $cat->save();
       }
       return response()->json(['Success'=>'Categories Deleted Successfully']);
    }
}
   