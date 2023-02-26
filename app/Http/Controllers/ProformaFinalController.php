<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Banker;
use App\Proforma;
use App\ProformaContainer;
use App\ProformaItem;
use App\ProformaCount;
use App\Vehicle;
use App\VehicleInfo;
use App\Manufacture;
use App\Product;
use App\VehicleProduct;
use DB; 
use Auth;
use App\User; 
use Carbon\Carbon;
use GeniusTS\HijriDate\Date; 
use Illuminate\Support\Facades\Validator; 
class ProformaFinalController extends Controller
{
    //
    public function customIndex()
    {  
        return view('purchase.custom-stage');  
    }
    public function customData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_date',
            2 =>'pfi_number',
            3=> 'declaration_no',
            4=> 'permit_number',
            5=> 'bill_number',
            6=> 'commercial_invoice',
            7=> 'operation_number'
        );
        $totalData = Proforma::where('is_active', true)->where('status',5)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else  
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
            $categories = Proforma::offset($start)
                        ->where('is_active', true)
                        ->where('status',5)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Proforma::where([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 5],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['declaration_no', 'LIKE', "%{$search}%"],
                            ['status', 5],
                            ['is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Proforma::where([
                            ['pfi_number','LIKE',"%{$search}%"],
                            ['status', 5],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['declaration_no', 'LIKE', "%{$search}%"],
                            ['status', 5],
                            ['is_active', true]
                        ])->count();
                        
        }
        $data = array();
        if(!empty($categories))
        {
            foreach ($categories as $key=>$category)
            {
                $nestedData['id'] = $category->id;
                $nestedData['key'] = $key+1;
                $nestedData['pfi_date'] =$category->pfi_date; 
                $nestedData['pfi_number'] = $category->pfi_number; 
                $nestedData['declaration_no'] = $category->declaration_no;
                $nestedData['permit_number'] = $category->permit_number;
                $nestedData['bill_number'] = $category->bill_number;
                $nestedData['commercial_invoice'] = $category->commercial_invoice;
                $nestedData['operation_number'] = $category->operation_number;
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                            <button type="button" class="btn btn-link pro-view"><i class="fa fa-eye"></i> View Details</button>
                             </li>
                            </ul>
                            </div>
                                ';
                    $user = User::find($category->user_id);
                    $namu = $user->name;
                    $emailu = $user->email;
                    $users = User::find($category->submitted_by);
                    $user_name = $users->name; 
                    $user1 = User::find($category->booked_by);
                    $user_name1 = $user1->name; 
                    $kasuu = User::find($category->transited_by);
                    $user_name12 = $kasuu->name; 
                    $transitoru = User::find($category->declared_by);
                    $USR_NAME = $transitoru->name;
                    $nestedData['proforma'] = array( '[ "'.Carbon::parse($category->created_at)->format('d-m-Y H:i').'"', ' "'.$category->id.'"', ' "'.$category->supplier_name.'"', ' "'.$category->buyer_name.'"', ' "'.$category->order_number.'"', ' "'.$category->pfi_date.'"', ' "'.$category->pfi_number.'"', ' "'.$category->bank_name.'"', ' "'.$category->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"', '"'.$category->permit_number.'"','"'.$category->payment_method.'"','"'.$category->payment_number.'"', '"'.Carbon::parse($category->submit_date)->format('d-m-Y H:i').'"', ' "'.$user_name.'"',  ' "'.$category->bill_number.'"',' "'.$category->commercial_invoice.'"', '"'.Carbon::parse($category->booked_at)->format('d-m-Y').'"', ' "'.$user_name1.'"', ' "'.$category->transitor_name.'"', ' "'.$category->operation_number.'"', '"'.Carbon::parse($category->transited_at)->format('d-m-Y').'"', ' "'.$user_name12.'"',  ' "'.$category->declaration_no.'"',  ' "'.Carbon::parse($category->declared_at)->format('d-m-Y').'"', '"'.$USR_NAME.'"]'
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
    public function index()
    {  
        return view('purchase.proforma-count'); 
    } 
    public function countData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_number',
            2=> 'purchase_items'
        );
        $totalData = ProformaCount::where('product_qty', '>', 0)->orWhere('vehicle_qty', '>', 0)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else  
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
            $categories = ProformaCount::with('proforma')->offset($start)
                        ->where('product_qty', '>', 0)
                        ->orWhere('vehicle_qty', '>', 0)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  ProformaCount::select('proformacounts.*')
                                    ->with('proforma')
                                    ->join('proformas', 'proformacounts.pro_id', '=', 'proformas.id')
                                    ->where([
                                        ['proformas.pfi_number', 'LIKE', "%{$search}%"],
                                        ['product_qty', '>', 0],
                                        ['is_active', true]
                                    ])
                                    ->orWhere([
                                        ['proformas.pfi_number', 'LIKE', "%{$search}%"],
                                        ['vehicle_qty', '>', 0],
                                        ['is_active', true]
                                    ])
                                    ->offset($start)
                                    ->limit($limit)
                                    ->orderBy($order,$dir)->get();

            $totalFiltered =ProformaCount::select('proformacounts.*')
                                    ->with('proforma')
                                    ->join('proformas', 'proformacounts.pro_id', '=', 'proformas.id')
                                    ->where([
                                        ['proformas.pfi_number', 'LIKE', "%{$search}%"],
                                        ['product_qty', '>', 0],
                                        ['is_active', true]
                                    ])
                                    ->orWhere([
                                        ['proformas.pfi_number', 'LIKE', "%{$search}%"],
                                        ['vehicle_qty', '>', 0],
                                        ['is_active', true]
                                    ])->count();
                           //here  
        }
        $data = array();
        if(!empty($categories))
        {
            foreach ($categories as $key=>$category)
            {
                $nestedData['id'] = $category->id;
                $nestedData['key'] = $key+1;
                $nestedData['pfi_number'] = Proforma::find($category->pro_id)->pfi_number; 
                $nestedData['purchase_items'] = $category->purchase_items;
                $vehicle = $category->product_qty + $category->vehicle_qty;
                if($category->purchase_qty > $vehicle)
                {
                    $nestedData['quantity'] = $category->purchase_qty;
                    $nestedData['product'] = $category->product_qty;
                    $nestedData['vehicle'] = $category->vehicle_qty;
                } 
                else
                { 
                    if($category->purchase_qty == $vehicle)
                    {
                        $nestedData['quantity'] = '<div class="badge badge-success">'.$category->purchase_qty.'</div>';
                        $nestedData['product'] = '<div class="badge badge-success">'.$category->product_qty.'</div>';
                        $nestedData['vehicle'] = '<div class="badge badge-success">'.$category->vehicle_qty.'</div>';
                    }
                    else
                    {
                        $nestedData['quantity'] = '<div class="badge badge-danger">'.$category->purchase_qty.'</div>';
                        $nestedData['product'] = '<div class="badge badge-danger">'.$category->product_qty.'</div>';
                        $nestedData['vehicle'] = '<div class="badge badge-danger">'.$category->vehicle_qty.'</div>';
                    }
                }
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                            <button type="button" class="btn btn-link pro-view"><i class="fa fa-eye"></i> View proforma Detail</button>
                            </li>';
                $nestedData['options'] .= '<li>
                            <a href="'.route('proforma.count.detail', $category->pro_id).'" class="btn btn-link"><i class="fa fa-eye"></i> View stock detail </a>
                            </li>
                            <li> 
                            <button type="button" data-id="'.$category->id.'" data-name="'.Proforma::find($category->pro_id)->pfi_number.'" class="delete-count btn btn-link" data-toggle="modal"  ><i class="fa fa-trash"></i> Delete</button>
                            </li>
                            </ul>
                            </div>
                                ';
                    $category1 = Proforma::find($category->pro_id);
                    $user = User::find($category1->user_id);
                    $namu = $user->name;
                    $emailu = $user->email;
                    $users = User::find($category1->submitted_by);
                    $user_name = $users->name; 
                    $user1 = User::find($category1->booked_by);
                    $user_name1 = $user1->name; 
                    $kasuu = User::find($category1->transited_by);
                    $user_name12 = $kasuu->name; 
                    $transitoru = User::find($category1->declared_by);
                    $USR_NAME = $transitoru->name;
                    $nestedData['proforma'] = array( '[ "'.Carbon::parse($category1->created_at)->format('d-m-Y H:i').'"', ' "'.$category1->id.'"', ' "'.$category1->supplier_name.'"', ' "'.$category1->buyer_name.'"', ' "'.$category1->order_number.'"', ' "'.$category1->pfi_date.'"', ' "'.$category1->pfi_number.'"', ' "'.$category1->bank_name.'"', ' "'.$category1->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"', '"'.$category1->permit_number.'"','"'.$category1->payment_method.'"','"'.$category1->payment_number.'"', '"'.Carbon::parse($category1->submit_date)->format('d-m-Y H:i').'"', ' "'.$user_name.'"',  ' "'.$category1->bill_number.'"',' "'.$category1->commercial_invoice.'"', '"'.Carbon::parse($category1->booked_at)->format('d-m-Y').'"', ' "'.$user_name1.'"', ' "'.$category1->transitor_name.'"', ' "'.$category1->operation_number.'"', '"'.Carbon::parse($category1->transited_at)->format('d-m-Y').'"', ' "'.$user_name12.'"',  ' "'.$category1->declaration_no.'"',  ' "'.Carbon::parse($category1->declared_at)->format('d-m-Y').'"', '"'.$USR_NAME.'"]'
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
    public function stockDetail($id)
    {
        $PP = Proforma::find($id);
        $Raws = Vehicle::where('pfi_number', $PP->pfi_number)->where('is_active', true)->get();
        $Manufactures = Manufacture::where('pfi_number', $PP->pfi_number)->where('status', 1)->where('is_active', true)->get();
        $Finished_Goods = Manufacture::where('pfi_number', $PP->pfi_number)->where('status', 2)->where('is_active', true)->get();
        $Vehicles = VehicleProduct::where('pfi_number', $PP->pfi_number)->where('is_active', true)->get();
        $Products = Product::where('pfi_number', $PP->pfi_number)->where('is_active', true)->get();
        return view('purchase.detail', compact('Products', 'Vehicles', 'Manufactures', 'Finished_Goods', 'Raws','PP'));  
 
    }
    public function delete($id)
    {
        
        $Count = ProformaCount::find($id);
        $Export= Proforma::find($Count->pro_id);
        $Export->is_active = false;
        $Export->save();
        $Count->delete();
            $success = true;
        return response()->json([
            'success' => $success
        ]);
 
    }
    
}
