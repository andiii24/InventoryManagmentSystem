<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banker;
use App\Proforma;
use App\ProformaItem;
use DB; 
use Auth;
use App\User;
use Carbon\Carbon;
use GeniusTS\HijriDate\Date;
use Illuminate\Support\Facades\Validator; 
class ProformaController extends Controller
{ 
    // 
    public function index()
    {  
        $banks = Banker::where('is_active', true)->orderBy('name', 'asc')->get(); 
        $proformas = Proforma::where('is_active', true)->where('status',1)->get();
        return view('purchase.proforma',compact('banks','proformas')); 

    } 
    public function proformaData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'pfi_date',
            2 =>'pfi_number',
            3=> 'order_number',
            4=> 'supplier_name',
            5=> 'buyer_name'
        );
          
        $totalData = Proforma::where('is_active', true)->where('status',1)->count();
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
                        ->where('status',1)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $categories =  Proforma::where([
                            ['pfi_date', 'LIKE', "%{$search}%"],
                            ['status', 1],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 1],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['order_number', 'LIKE', "%{$search}%"],
                            ['status', 1],
                            ['is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Proforma::where([
                            ['pfi_date','LIKE',"%{$search}%"],
                            ['status', 1],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['pfi_number', 'LIKE', "%{$search}%"],
                            ['status', 1],
                            ['is_active', true]
                        ])
                        ->orWhere([
                            ['order_number', 'LIKE', "%{$search}%"],
                            ['status', 1],
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
                $nestedData['pfi_date'] =$category->pfi_date; 
                $nestedData['pfi_number'] = $category->pfi_number; 
                $nestedData['order_number'] = $category->order_number;
                $nestedData['supplier_name'] = $category->supplier_name;
                if($category->buyer_name != "")
                  $nestedData['buyer_name'] = $category->buyer_name;
                else
                   $nestedData['buyer_name'] = "N/A";
                $nestedData['no_of_item'] = ProformaItem::where('proforma_id',$category->id)->count();
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
                                    <button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'" class="AddItemDialog btn btn-link" data-toggle="modal"  ><i class="fa fa-plus"></i> Add Item</button>
                                </li>';
                $felig = ProformaItem::where('proforma_id',$category->id)->first();
                if($felig)
                {
                    $nestedData['options'] .= 
                    '<li>
                        <button type="button" class="get-items btn btn-link" data-id = "'.$category->id.'"><i class="fa fa-list-alt"></i> View Items </button>
                    </li>';
                }
                $felig = ProformaItem::where('proforma_id',$category->id)->first();
                if($felig)
                {
                    $nestedData['options'] .= 
                    '<li>
                        <button type="button" class="pro-bank-submit btn btn-link" data-id = "'.$category->id.'" data-name="'.$category->pfi_number.'"><i class="fa fa-bank"></i> Add To Bank Submit </button>
                    </li>';
                }
                $nestedData['options'] .= '<li>
                                <button type="button" data-id="'.$category->id.'" class="open-EditCategoryDialog btn btn-link" data-toggle="modal"  ><i class="dripicons-document-edit"></i> '.trans("file.edit").'</button>
                                </li>
                                <li class="divider"></li>
                                <li><button type="button" data-id="'.$category->id.'" data-name="'.$category->pfi_number.'"  id="kt-delete"  class="DeleteCategoryDialog btn btn-link"   ><i class="dripicons-trash"></i> '.trans("file.delete").'</button></li>                   
                            </ul>
                        </div>'; 
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
                        $nestedData['proforma'] = array( '[ "'.Carbon::parse($category->created_at)->format('d-m-Y H:i:s').'"', ' "'.$category->id.'"', ' "'.$category->supplier_name.'"', ' "'.$category->buyer_name.'"', ' "'.$category->order_number.'"', ' "'.$category->pfi_date.'"', ' "'.$category->pfi_number.'"', ' "'.$category->bank_name.'"', ' "'.$category->payment_term.'"',  ' "'.$namu.'"', ' "'.$emailu.'"]'
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
    public function edit($id)
    {
        $lims_category_data = Proforma::findOrFail($id);
        return $lims_category_data;
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $Brand = new Proforma();
        $Brand->supplier_name = $myvalue['supplier_name'];
        if($myvalue['buyer_name'] != "" )
        {
            $Brand->buyer_name = $myvalue['buyer_name'];
        }
        $Brand->order_number = $myvalue['order_number'];
        $Brand->pfi_number = $myvalue['pfi_number'];
        $Brand->pfi_date = $myvalue['pfi_date'];
        $Brand->bank_name = $myvalue['bank_name'];
        if($myvalue['payment_term'] != "")
        {
            $Brand->payment_term = $myvalue['payment_term'];
        }
        $Brand->user_id = Auth::user()->id;
        $Brand->is_active = true;
        $Brand->save();
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $id = $myvalue['edit_id'];
        $Brand = Proforma::find($id);
        $Brand->supplier_name = $myvalue['edit_supplier_name'];
        $Brand->buyer_name = $myvalue['edit_buyer_name'];
        $Brand->order_number = $myvalue['edit_order_number'];
        $Brand->pfi_number = $myvalue['edit_pfi_number'];
        $Brand->pfi_date = $myvalue['edit_pfi_date'];
        $Brand->bank_name = $myvalue['edit_bank_name'];
        $Brand->payment_term = $myvalue['edit_payment_term'];
        $Brand->user_id = Auth::user()->id;
        $Brand->save();
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function delete($id)
    {
        $Export = Proforma::findOrFail($id);
        $Export->is_active = false;
        ProformaItem::where('proforma_id',$id)->delete();
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
            $lims_category_data = Proforma::findOrFail($id);
            $lims_category_data->is_active = false;
            $lims_category_data->save();
            ProformaItem::where('proforma_id',$id)->delete();
        }
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function addItem(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $desc = $myvalue['item_description'];
        $Check = ProformaItem::where('description', $desc)->where('proforma_id', $myvalue['proforma_id'])->where('is_active', true)->first();
        if($Check)
        {
            $Item = ProformaItem::find($Check->id);
            $Item->description = $myvalue['item_description'];
            $Item->qty += $myvalue['item_qty'];
            $Item->unit_price = $myvalue['unit_price'];
            $Item->total_amount += $myvalue['total_amount'];
            $Item->user_id = Auth::user()->id;
            $Item->is_active = true;
            $Item->save();
        }
        else
        {
            $Item = new ProformaItem();
            $Item->proforma_id = $myvalue['proforma_id'];
            $Item->description = $myvalue['item_description'];
            $Item->qty = $myvalue['item_qty'];
            $Item->unit_price = $myvalue['unit_price'];
            $Item->total_amount = $myvalue['total_amount'];
            $Item->user_id = Auth::user()->id;
            $Item->is_active = true;
            $Item->save(); 
        }
        $success = true;
        return response()->json([
            'success' => $success
        ]);
    }
    public function import(Request $request)
    {   
        //get file
        $upload=$request->file('file');
        $proId = $request->import_proforma_id;
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
           if($data['description'] == '' || $data['quantity'] == "" || $data['unitprice'] == "" || $data['totalamount'] == "")
           {
            return redirect('purchase/proforma-invoices')->with('not_permitted', ' Records not include all required fields are not inserted !');
           }
           else
           {
            $product = ProformaItem::firstOrNew([ 'proforma_id'=>$proId, 'description'=>$data['description'], 'is_active'=>true ]);
            $product->proforma_id = $proId;
            $product->description = $data['description'];
            $product->qty = str_replace(",","",$data['quantity']);
            $product->unit_price = str_replace(",","",$data['unitprice']);
            $product->total_amount = str_replace(",","",$data['totalamount']);
            $product->user_id = Auth::user()->id;
            $product->is_active = true;
            $product->save();
           }
         }
         return redirect('purchase/proforma-invoices')->with('message', 'Items imported to proforma successfully !');
    }

    public function ProformaItems($id)
    {   $check = ProformaItem::where('proforma_id', $id)->first();
        if($check)
        {
            $lims_product_sale_data = ProformaItem::where('proforma_id', $id)->orderBy('description', 'asc')->get();
            foreach ($lims_product_sale_data as $key => $kasu) {
                $product_sale[0][$key] = $kasu->description;
                $product_sale[1][$key] = (int)$kasu->qty;
                $product_sale[2][$key] = $kasu->unit_price;
                $product_sale[3][$key] = (int)$kasu->total_amount;
                $product_sale[4][$key] = (int)($kasu->qty * $kasu->unit_price);
                $user = User::find($kasu->user_id);
                $product_sale[5][$key] = $user->name;
            }
        }
        else
        {
            $product_sale = "";  
        }
       
        return $product_sale;
    }
    public function getItem($id)
    {  
        $lims_product_sale_data = ProformaItem::where('proforma_id', $id)->orderBy('description', 'asc')->get();
            foreach ($lims_product_sale_data as $key => $kasu) {
                $product_sale[0][$key] = $kasu->description;
                $product_sale[1][$key] = $kasu->qty;
                $product_sale[2][$key] = $kasu->unit_price;
                $product_sale[3][$key] = $kasu->total_amount;
                $product_sale[4][$key] = $kasu->id;
            }
            $user = Proforma::find($id);
            $product_sale['proforma'] = $user->pfi_number;

        return $product_sale;
    }
    public function updateItem(Request $request)
    {
        $data = $request->all(); 
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
            $id = $myvalue['kasu_id'];
            $Item = ProformaItem::find($id);
            $Item->description = $myvalue['kasu_description'];
            $Item->qty = $myvalue['kasu_qty'];
            $Item->unit_price = $myvalue['kasu_price'];
            $Item->total_amount = $myvalue['kasu_amount'];
            $Item->updated_at = Carbon::now();
            $Item->save(); 
            $success = true;
            return response()->json([
                'success' => $success
            ]);
    }
    public function itemDelete($id)
    {
        $Export = ProformaItem::findOrFail($id);
        $Export->delete();
            $success = true;
        return response()->json([
            'success' => $success
        ]);
 
    }
    public function submitBank(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        $proID = $myvalue['submit_id'];
        $Proforma = Proforma::find($proID);
        $Proforma->status = 2;
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
