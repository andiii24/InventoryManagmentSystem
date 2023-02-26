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
class BankSubmitController extends Controller
{ 
    //
    public function index()
    {  
        $proformas = Proforma::where('is_active', true)->get();
        return view('purchase.bank-submit',compact('proformas'));  
    }
    public function submitData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_date',
            2 =>'pfi_number',
            3=> 'bank_name',
            4=> 'permit_number',
            5=> 'payment_method',
            6=> 'payment_number'
        );
          
        $totalData = Proforma::where('is_active', true)->where('status',2)->count();
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
                        ->where('status',2)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Proforma::where([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 2],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['bank_name', 'LIKE', "%{$search}%"],
                            ['status', 2],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['permit_number', 'LIKE', "%{$search}%"],
                            ['status', 2],
                            ['is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Proforma::where([
                            ['pfi_number','LIKE',"%{$search}%"],
                            ['status', 2],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['bank_name', 'LIKE', "%{$search}%"],
                            ['status', 2],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['permit_number', 'LIKE', "%{$search}%"],
                            ['status', 2],
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
                $nestedData['bank_name'] = $category->bank_name;
                $nestedData['permit_number'] = $category->permit_number;
                $nestedData['payment_method'] = $category->payment_method;
                $nestedData['payment_number'] = $category->payment_number;

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
                                <button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'" class="AddBookingDialog btn btn-link" data-toggle="modal"  ><i class="fa fa-book"></i> Add Booking</button>
                            </li>
                            <li> 
                                <button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'" class="edit-bank btn btn-link" data-toggle="modal"  ><i class="fa fa-edit"></i> Edit bank submit</button>
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
                            $user_email = $users->email;
                        }
                        else
                        {
                            $user_name = "N/A";
                            $user_email = "N/A";
                        }
                        $nestedData['proforma'] = array( '[ "'.Carbon::parse($category->created_at)->format('Y-m-d H:i:s').'"', ' "'.$category->id.'"', ' "'.$category->supplier_name.'"', ' "'.$category->buyer_name.'"', ' "'.$category->order_number.'"', ' "'.$category->pfi_date.'"', ' "'.$category->pfi_number.'"', ' "'.$category->bank_name.'"', ' "'.$category->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"', '"'.$category->permit_number.'"','"'.$category->payment_method.'"','"'.$category->payment_number.'"', '"'.Carbon::parse($category->submit_date)->format('Y-m-d H:i').'"', ' "'.$user_name.'"', ' "'.$user_email.'"]'
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
    public function addBooking(Request $request)
    {
        $data = $request->all();
       // return dd($data);
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $idd = $myvalue['booking_id'];
        $Proforma = Proforma::find($idd);
        $Proforma->status = 3;
        $Proforma->bill_number = $myvalue['bill_number'];
        $Proforma->commercial_invoice = $myvalue['commercial_invoice'];
        $Proforma->container_no = $myvalue['total_container'];
        $Proforma->booked_at = Carbon::now();
        $Proforma->booked_by = Auth::user()->id;
        $Proforma->save();
        foreach($myvalue['container_number'] as $container){
            $ExportDoc = new ProformaContainer();
            $ExportDoc->proforma_id = $Proforma->id;
            $ExportDoc->container_number = $container;
            $ExportDoc->user_id = Auth::user()->id;
            $ExportDoc->save();
       }
        //Ajax return
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function getSubmit($id)
    {
        $lims_category_data = Proforma::find($id);
        return $lims_category_data;
    }
    public function Update(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $proID = $myvalue['submit_id'];
        $Proforma = Proforma::find($proID);
        $Proforma->permit_number =   $myvalue['permit_number'];
        $Proforma->payment_method =  $myvalue['payment_method']; 
        $Proforma->payment_number =  $myvalue['payment_number']; 
        $Proforma->submit_date = Carbon::now();
        $Proforma->submitted_by = Auth::user()->id;
        $Proforma->save();
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
}
