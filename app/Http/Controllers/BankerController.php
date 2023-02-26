<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banker;
use DB;  
use Auth;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class BankerController extends Controller
{
    //
    public function index()
    { 
        $banks = Banker::where('is_active', 1)->get(); 
        return view('purchase.bank',compact('banks')); 

    }
    public function bankData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'name',
            2=> 'user',
            3=> 'date'
        );
         
        $totalData = Banker::where('is_active', true)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
            $categories = Banker::offset($start)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Banker::where([
                            ['name', 'LIKE', "%{$search}%"],
                            ['is_active', true]
                        ])->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Banker::where([
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
                $nestedData['user'] = User::find($category->user_id)->name;
                $nestedData['date'] = Carbon::parse($category->created_at)->format('M, d-Y H:i');
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="'.$category->id.'" data-name="'.$category->name.'" class="open-EditCategoryDialog btn btn-link" data-toggle="modal"  ><i class="dripicons-document-edit"></i> '.trans("file.edit").'</button>
                                </li>
                                <li class="divider"></li>
                                <li><button type="button" data-id="'.$category->id.'" data-name="'.$category->name.'"  id="kt-delete"  class="DeleteCategoryDialog btn btn-link"   ><i class="dripicons-trash"></i> '.trans("file.delete").'</button></li>                   
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
    public function store(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $Brand = new Banker();
        $Brand->name = $myvalue['bank_name'];
        $Brand->user_id = Auth::user()->id;
        $Brand->is_active = true;
        $Brand->save();
        return response()->json(['Success'=>'Bank Added Successfully']);
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $id = $myvalue['edit_id'];
        $Brand = Banker::find($id);
        $Brand->name = $myvalue['edit_bank_name'];
        $Brand->user_id = Auth::user()->id;
        $Brand->updated_at = Carbon::now();
        $Brand->save();
        return response()->json(['success'=>'Bank updated Successfully']);
    }
    public function delete($id)
    {
        $Export = Banker::findOrFail($id);
        $Export->is_active = false;
        $Export->save();
            $success = true;
        return response()->json([
            'success' => $success
        ]);
 
    }
    public function deleteBySelection(Request $request)
    { 
        $category_id = $request['categoryIdArray'];
        foreach ($category_id as $id) {
            $lims_category_data = Banker::findOrFail($id);
            $lims_category_data->is_active = false;
            $lims_category_data->save();
        }
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
}
