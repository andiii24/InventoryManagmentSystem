<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Banker;
use App\Proforma;
use App\ProformaContainer;
use DB; 
use Auth;
use App\User; 
use Carbon\Carbon;
use GeniusTS\HijriDate\Date; 
use Illuminate\Support\Facades\Validator; 
class BookingController extends Controller
{
    //
    public function index()
    {  
        $proformas = Proforma::where('is_active', true)->get();
        return view('purchase.booking',compact('proformas'));  
    }
    public function bookingData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_date',
            2 =>'pfi_number',
            3=> 'bill_number',
            4=> 'commercial_invoice',
            5=> 'container_no'
        );
        $totalData = Proforma::where('is_active', true)->where('status',3)->count();
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
                        ->where('status',3)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Proforma::where([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 3],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['bill_number', 'LIKE', "%{$search}%"],
                            ['status', 3],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['commercial_invoice', 'LIKE', "%{$search}%"],
                            ['status', 3],
                            ['is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Proforma::where([
                            ['pfi_number','LIKE',"%{$search}%"],
                            ['status', 3],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['bill_number', 'LIKE', "%{$search}%"],
                            ['status', 3],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['commercial_invoice', 'LIKE', "%{$search}%"],
                            ['status', 3],
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
                $nestedData['bill_number'] = $category->bill_number;
                $nestedData['commercial_invoice'] = $category->commercial_invoice;
                $nestedData['container_no'] = $category->container_no;
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                            <button type="button" class="btn btn-link pro-view"><i class="fa fa-eye"></i> View Details</button>
                             </li>
                                <li> 
                                    <button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'" class="AddTransitDialog btn btn-link" data-toggle="modal"  ><i class="fa fa-truck"></i> Add Transition</button>
                                </li>
                                </ul>
                                </div>
                                ';
                        $user = User::find($category->user_id);
                        if($user)
                        {
                            $namu = $user->name;
                            $emailu = $user->email;
                        }
                        else
                        {
                            $namu = "N/A";
                            $emailu = "N/A";
                        }
                        $users = User::find($category->submitted_by);
                        if($users)
                        {
                            $user_name = $users->name; 
                        }
                        else
                        {
                            $user_name = "N/A";
                        }
                        $user1 = User::find($category->booked_by);
                        if($user1)
                        {
                            $user_name1 = $user1->name; 
                        }
                        else
                        {
                            $user_name1 = "N/A";
                        }
                        $nestedData['proforma'] = array( '[ "'.Carbon::parse($category->created_at)->format('Y-m-d H:i:s').'"', ' "'.$category->id.'"', ' "'.$category->supplier_name.'"', ' "'.$category->buyer_name.'"', ' "'.$category->order_number.'"', ' "'.$category->pfi_date.'"', ' "'.$category->pfi_number.'"', ' "'.$category->bank_name.'"', ' "'.$category->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"', '"'.$category->permit_number.'"','"'.$category->payment_method.'"','"'.$category->payment_number.'"', '"'.Carbon::parse($category->submit_date)->format('Y-m-d H:i').'"', ' "'.$user_name.'"',  ' "'.$category->bill_number.'"',' "'.$category->commercial_invoice.'"', '"'.Carbon::parse($category->booked_at)->format('Y-m-d').'"', ' "'.$user_name1.'"]'
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
    public function containers($id)
    {   
        $lims_product_sale_data = ProformaContainer::where('proforma_id', $id)->get();
            foreach ($lims_product_sale_data as $key => $kasu) {
                $product_sale[0][$key] = $kasu->container_number;
            }      
         return $product_sale;
    }
    public function addTransit(Request $request)
    {
        $data = $request->all();
       // return dd($data);
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $idd = $myvalue['transit_id'];
        $Proforma = Proforma::find($idd);
        $Proforma->status = 4;
        $Proforma->transitor_name = $myvalue['transitor_name'];
        $Proforma->operation_number = $myvalue['operation_number'];
        $Proforma->transited_at = Carbon::now();
        $Proforma->transited_by = Auth::user()->id;
        $Proforma->save();
        //Ajax return
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function transitionIndex()
    {  
        $proformas = Proforma::where('is_active', true)->get();
        return view('purchase.transition',compact('proformas'));  
    }
    public function transitionData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_date',
            2 =>'pfi_number',
            3=> 'transitor_name',
            4=> 'operation_number'
        );
        $totalData = Proforma::where('is_active', true)->where('status',4)->count();
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
                        ->where('status',4)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Proforma::where([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 4],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['transitor_name', 'LIKE', "%{$search}%"],
                            ['status', 4],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['operation_number', 'LIKE', "%{$search}%"],
                            ['status', 4],
                            ['is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Proforma::where([
                            ['pfi_number','LIKE',"%{$search}%"],
                            ['status', 4],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['transitor_name', 'LIKE', "%{$search}%"],
                            ['status', 4],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['operation_number', 'LIKE', "%{$search}%"],
                            ['status', 4],
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
                $nestedData['transitor_name'] = $category->transitor_name;
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
                                <li> 
                                    <button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'" class="AddCustomDialog btn btn-link" data-toggle="modal"  ><i class="fa fa-bullhorn"></i> Add To Custom</button>
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
                        $nestedData['proforma'] = array( '[ "'.Carbon::parse($category->created_at)->format('d-m-Y H:i:s').'"', ' "'.$category->id.'"', ' "'.$category->supplier_name.'"', ' "'.$category->buyer_name.'"', ' "'.$category->order_number.'"', ' "'.$category->pfi_date.'"', ' "'.$category->pfi_number.'"', ' "'.$category->bank_name.'"', ' "'.$category->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"', '"'.$category->permit_number.'"','"'.$category->payment_method.'"','"'.$category->payment_number.'"', '"'.Carbon::parse($category->submit_date)->format('d-m-Y H:i').'"', ' "'.$user_name.'"',  ' "'.$category->bill_number.'"',' "'.$category->commercial_invoice.'"', '"'.Carbon::parse($category->booked_at)->format('d-m-Y').'"', ' "'.$user_name1.'"', ' "'.$category->transitor_name.'"', ' "'.$category->operation_number.'"', '"'.Carbon::parse($category->transited_at)->format('d-m-Y').'"', ' "'.$user_name12.'"]'
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
    public function addCustom(Request $request)
    {
        $data = $request->all();
       // return dd($data);
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $idd = $myvalue['custom_id'];
        $Proforma = Proforma::find($idd);
        $Proforma->status = 5;
        $Proforma->declaration_no = $myvalue['declaration_no'];
        $Proforma->declared_at = Carbon::now();
        $Proforma->declared_by = Auth::user()->id;
        $Proforma->save();
        //Ajax return
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    } 
}
