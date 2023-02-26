<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\Product;
use App\Product_Warehouse;
use App\Tax;
use App\Unit;
use App\Transfer;
use App\ProductTransfer;
use App\ProductVariant;
use App\ProductBatch;
use Carbon\Carbon;
use Auth;
use App\User;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';

            if($request->input('from_warehouse_id'))
                $from_warehouse_id = $request->input('from_warehouse_id');
            else
                $from_warehouse_id = 0;

            if($request->input('to_warehouse_id'))
                $to_warehouse_id = $request->input('to_warehouse_id');
            else
                $to_warehouse_id = 0;

            if($request->input('starting_date')) {
                $starting_date = $request->input('starting_date');
                $ending_date = $request->input('ending_date');
            }
            else {
                $starting_date = date("Y-m-d", strtotime(date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d') )))));
                $ending_date = date("Y-m-d");
            }

            $lims_warehouse_list = Warehouse::select('name', 'id')->where('is_active', true)->get();
            return view('transfer.index',compact('starting_date', 'ending_date', 'from_warehouse_id', 'to_warehouse_id', 'all_permission', 'lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function transferData(Request $request)
    {
        $columns = array(
            1 => 'created_at',
            2 => 'reference_no',
        );

        $from_warehouse_id = $request->input('from_warehouse_id');
        $to_warehouse_id = $request->input('to_warehouse_id');
        $q = Transfer::whereDate('created_at', '>=' ,$request->input('starting_date'))
                     ->whereDate('created_at', '<=' ,$request->input('ending_date'));
        if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $q = $q->where('user_id', Auth::id());
        if($from_warehouse_id)
            $q = $q->where('from_warehouse_id', $from_warehouse_id);
        if($to_warehouse_id)
            $q = $q->where('to_warehouse_id', $to_warehouse_id);

        $totalData = $q->count();
        $totalFiltered = $totalData;

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'transfers.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))) {
            $q = Transfer::with('fromWarehouse', 'toWarehouse', 'user')
                ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $q = $q->where('user_id', Auth::id());
            if($from_warehouse_id)
                $q = $q->where('from_warehouse_id', $from_warehouse_id);
            if($to_warehouse_id)
                $q = $q->where('to_warehouse_id', $to_warehouse_id);
            $transfers = $q->get();
        }
        else
        {
            $search = $request->input('search.value');
            $q = Transfer::whereDate('transfers.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir);
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $transfers =  $q->select('transfers.*')
                                ->with('fromWarehouse', 'toWarehouse', 'user')
                                ->where('transfers.user_id', Auth::id())
                                ->orwhere([
                                    ['reference_no', 'LIKE', "%{$search}%"],
                                    ['user_id', Auth::id()]
                                ])
                                ->get();
                $totalFiltered = $q->where('transfers.user_id', Auth::id())->count();
            }
            else {
                $transfers =  $q->select('transfers.*')
                                ->with('fromWarehouse', 'toWarehouse', 'user')
                                ->orwhere('reference_no', 'LIKE', "%{$search}%")
                                ->get();

                $totalFiltered = $q->orwhere('transfers.reference_no', 'LIKE', "%{$search}%")->count();
            }
        }
        $data = array();
        if(!empty($transfers))
        {
            foreach ($transfers as $key=>$transfer)
            {
                $nestedData['id'] = $transfer->id;
                $nestedData['key'] = $key;
                $nestedData['date'] = date(config('date_format'), strtotime($transfer->created_at->toDateString()));
                $nestedData['reference_no'] = $transfer->reference_no;
                $nestedData['from_warehouse'] = $transfer->fromWarehouse->name;
                $nestedData['to_warehouse'] = $transfer->toWarehouse->name;
                $nestedData['user'] = user::find($transfer->user_id)->name;
                $status =  $nestedData['user'] ;           

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                                </li>';
                if(in_array("transfers-delete", $request['all_permission']))
                    $nestedData['options'] .= \Form::open(["route" => ["transfers.destroy", $transfer->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button>
                            </li>'.\Form::close().'
                        </ul>
                    </div>';
                // data for transfer details by one click

                $nestedData['transfer'] = array( '[ "'.date(config('date_format'), strtotime($transfer->created_at->toDateString())).'"', ' "'.$transfer->reference_no.'"', ' "'.$status.'"', ' "'.$transfer->id.'"', ' "'.$transfer->fromWarehouse->name.'"', ' "'.$transfer->fromWarehouse->phone.'"', ' "'.preg_replace('/\s+/S', " ", $transfer->fromWarehouse->address).'"', ' "'.$transfer->toWarehouse->name.'"', ' "'.$transfer->toWarehouse->phone.'"', ' "'.preg_replace('/\s+/S', " ", $transfer->toWarehouse->address).'"', ' "'.$transfer->total_tax.'"', ' "'.$transfer->total_cost.'"', ' "'.$transfer->shipping_cost.'"', ' "'.$transfer->grand_total.'"', ' "'.preg_replace('/[\n\r]/', "<br>", $transfer->note).'"', ' "'.$transfer->user->name.'"', ' "'.$transfer->user->email.'"]'
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
        //return dd($json_data);
        echo json_encode($json_data);
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-add')){
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('transfer.create', compact('lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getProduct($id)
    {

        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_data = [];
        $data = Product::select('name', 'code')->where([['warehouse_id', $id],['is_active', true]])->get();
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_data = [];
       return $data;

    }

    public function limsProductSearch(Request $request)
    {

        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");

        $lims_product_data = Product::where([
            ['code', $product_code[0]],
            ['is_active', true]
        ])->first();

            $product[] = $lims_product_data->name;
            $product[] = $lims_product_data->code;
            $product[] = $lims_product_data->price;
            $product[] = $lims_product_data->id;

            if($lims_product_data->unit_id == 1)
            {
                $product[] = 'Piece';
            }
            elseif($lims_product_data->unit_id == 2)
            {
                $product[] = 'Dozen';
            }
            else
            {
                $product[] = 'Carton';
            }

        if($lims_product_data->tax_id != null && $lims_product_data->tax_id != 15) {
            $lims_tax_data = Tax::where('rate',$lims_product_data->tax_id)->first();
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        }
        else{
            $product[] = 15;
            $product[] = 'vat@15';
        }
        $product[] = $lims_product_data->tax_method;
        if($lims_product_data->unit_id==3)
        {
            $product[] = $lims_product_data->piece_in_carton;
        }
        $product[] = $lims_product_data->qty;

       // return dd($product);
        return $product;

    }

    public function store(Request $request)
    {
         
          $data = $request->all();
          return dd($data);
        $data['user_id'] = Auth::id();
        $data['reference_no'] = 'tr-' . date("Ymd") . '-'. date("his");
        if(isset($data['created_at']))
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        else
            $data['created_at'] = date("Y-m-d H:i:s");
        // import document
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }
       $data['grand_total']=null;
        $lims_transfer_data = Transfer::create($data);

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];

        $product_transfer = [];



        foreach ($product_id as $i => $id) {

            //$lims_purchase_unit_data  = Unit::where('unit_name', $purchase_unit[$i])->first();
            $product_transfer['variant_id'] = null;
            $product_transfer['product_batch_id'] = null;
           $check_product_quantity_from_warehouse = Product::select('qty')
                                                            ->where([
                                                                ['code',$product_code[$i]],
                                                                ['warehouse_id',[$data['from_warehouse_id']]],
                                                                ['is_active', true]
                                                                ])->first();

           if($check_product_quantity_from_warehouse->qty >= $qty[$i]){
            //$check_product_quantity_from_warehouse->qty -= $qty[$i];
            $quantity_change = $check_product_quantity_from_warehouse->qty - $qty[$i];
            Product::where([
                    ['code',$product_code[$i]],
                    ['warehouse_id',[$data['from_warehouse_id']]],
                    ['is_active', true]
                    ])->update(['qty' => $quantity_change]);
           }

           $check_product_quantity_to_warehouse = Product::select('qty')
                                                            ->where([
                                                                ['code',$product_code[$i]],
                                                                ['warehouse_id', $data['to_warehouse_id']],
                                                                ['is_active', true]
                                                                ])->first();
             if(!empty($check_product_quantity_to_warehouse)){
             $quantity_change = $check_product_quantity_to_warehouse->qty + $qty[$i];

             Product::where([
                        ['code',$product_code[$i]],
                        ['warehouse_id',[$data['to_warehouse_id']]],
                        ['is_active', true]
                        ])->update(['qty' => $quantity_change]);
            }else{
               $retrieve_product = Product::where([
                                                    ['code',$product_code[$i]],
                                                    ['warehouse_id', $data['from_warehouse_id']],
                                                    ['is_active', true]
                                                    ])->first();

                $create_new_product_to_warehouse= $retrieve_product->replicate();
                $create_new_product_to_warehouse->qty = $qty[$i];
                $create_new_product_to_warehouse->warehouse_id = $data['to_warehouse_id'];
                $new_product_added = $create_new_product_to_warehouse->save();
            }

            $product_transfer['transfer_id'] = $lims_transfer_data->id ;
            $product_transfer['product_id'] = $id;
            //$product_transfer['imei_number'] = $imei_number[$i];
            $product_transfer['qty'] = $qty[$i];
  
            ProductTransfer::create($product_transfer);


        }
        return redirect('transfers')->with('message', 'Transfer created successfully');
    }

    public function productTransferData($id)
    {
        //return dd()
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();

        foreach ($lims_product_transfer_data as $key => $product_transfer_data) {
            $product = Product::find($product_transfer_data->product_id);
            //$unit = Unit::find($product_transfer_data->purchase_unit_id);
            /// remove variant
            $product_transfer[0][$key] = $product->name; 
            $product_transfer[1][$key] = $product->code; 
            $product_transfer[2][$key] = $product_transfer_data->qty;
            $product_transfer[3][$key] = Carbon::parse($product->created_at)->format('l, d-m-Y H:i');
            //return dd($product_transfer);
            return $product_transfer;
        }

    }

    public function transferByCsv()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-add')){
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('transfer.import', compact('lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function importTransfer(Request $request)
    {
        //get the file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        $file_handle = fopen($filePath, 'r');
        $i = 0;
        //validate the file
        while (!feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if($current_line && $i > 0){
                $product_data[] = Product::where('code', $current_line[0])->first();
                if(!$product_data[$i-1])
                    return redirect()->back()->with('message', 'Product does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1])
                    return redirect()->back()->with('message', 'Purchase unit does not exist!');
                if(strtolower($current_line[4]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[4])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $cost[] = $current_line[3];
            }
            $i++;
        }

        $data = $request->except('file');
        $data['reference_no'] = 'tr-' . date("Ymd") . '-'. date("his");
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = $data['reference_no'] . '.' . $ext;
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        $data['user_id'] = Auth::id();
        Transfer::create($data);
        $lims_transfer_data = Transfer::latest()->first();

        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_cost = $cost[$key];
                $product_tax = $net_unit_cost * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_cost * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_cost = (100 / (100 + $tax[$key]['rate'])) * $cost[$key];
                $product_tax = ($cost[$key] - $net_unit_cost) * $qty[$key];
                $total = $cost[$key] * $qty[$key];
            }
            if($data['status'] == 1){
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['to_warehouse_id']]
                ])->first();
                if($product_warehouse) {
                    $product_warehouse->qty += $quantity;
                    $product_warehouse->save();
                }
                else {
                    $product_warehouse = new Product_Warehouse();
                    $product_warehouse->product_id = $product['id'];
                    $product_warehouse->warehouse_id = $data['to_warehouse_id'];
                    $product_warehouse->qty = $quantity;
                    $product_warehouse->save();
                }
            }
            elseif ($data['status'] == 3) {
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
            }

            $product_transfer = new ProductTransfer();
            $product_transfer->transfer_id = $lims_transfer_data->id;
            $product_transfer->product_id = $product['id'];
            $product_transfer->qty = $qty[$key];
            $product_transfer->purchase_unit_id = $unit[$key]['id'];
            $product_transfer->net_unit_cost = number_format((float)$net_unit_cost, 2, '.', '');
            $product_transfer->tax_rate = $tax[$key]['rate'];
            $product_transfer->tax = number_format((float)$product_tax, 2, '.', '');
            $product_transfer->total = number_format((float)$total, 2, '.', '');
            $product_transfer->save();
            $lims_transfer_data->total_qty += $qty[$key];
            $lims_transfer_data->total_tax += number_format((float)$product_tax, 2, '.', '');
            $lims_transfer_data->total_cost += number_format((float)$total, 2, '.', '');
        }
        $lims_transfer_data->item = $key + 1;
        $lims_transfer_data->grand_total = $lims_transfer_data->total_cost + $lims_transfer_data->shipping_cost;
        $lims_transfer_data->save();
        return redirect('transfers')->with('message', 'Transfer imported successfully');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-edit')){
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $lims_transfer_data = Transfer::find($id);
            $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
            return view('transfer.edit', compact('lims_warehouse_list', 'lims_transfer_data', 'lims_product_transfer_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        return dd($data);
        $document = $request->document;
        $data['created_at'] = date("Y-m-d", strtotime(str_replace("/", "-", $data['created_at'])));
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }

        $lims_transfer_data = Transfer::find($id);
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        $product_id = $data['product_id'];
        $imei_number = $data['imei_number'];
        $product_batch_id = $data['product_batch_id'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_transfer = [];
        foreach ($lims_product_transfer_data as $key => $product_transfer_data) {
            $old_product_id[] = $product_transfer_data->product_id;
            $old_product_variant_id[] = null;
            $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $product_transfer_data->qty / $lims_transfer_unit_data->operation_value;
            }

            if($lims_transfer_data->status == 1){
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id ]
                    ])->first();

                    $lims_product_to_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->to_warehouse_id ]
                    ])->first();
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();
                }

                if($product_transfer_data->imei_number) {
                    //add imei number to from warehouse
                    if($lims_product_from_warehouse_data->imei_number)
                        $lims_product_from_warehouse_data->imei_number .= ',' . $product_transfer_data->imei_number;
                    else
                        $lims_product_from_warehouse_data->imei_number = $product_transfer_data->imei_number;
                    //deduct imei number from to warehouse
                    $imei_numbers = explode(",", $product_transfer_data->imei_number);
                    $all_imei_numbers = explode(",", $lims_product_to_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_to_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();

                $lims_product_to_warehouse_data->qty -= $quantity;
                $lims_product_to_warehouse_data->save();
            }
            elseif($lims_transfer_data->status == 3) {
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id ]
                    ])->first();
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                }
                if($product_transfer_data->imei_number) {
                    //add imei number to from warehouse
                    if($lims_product_from_warehouse_data->imei_number)
                        $lims_product_from_warehouse_data->imei_number .= ',' . $product_transfer_data->imei_number;
                    else
                        $lims_product_from_warehouse_data->imei_number = $product_transfer_data->imei_number;
                }
                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();
            }

            if($product_transfer_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_transfer_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) ){
                $product_transfer_data->delete();
            }
        }

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::select('is_variant')->find($pro_id);
            $lims_transfer_unit_data = Unit::where('unit_name', $purchase_unit[$key])->first();
            $variant_id = null;
            $product_transfer['product_batch_id'] = null;
            //unit conversion
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $qty[$key] * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $qty[$key] / $lims_transfer_unit_data->operation_value;
            }

            if($data['status'] == 1) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['to_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                elseif($product_batch_id[$key]) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['from_warehouse_id'] ]
                    ])->first();

                    $lims_product_to_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['to_warehouse_id'] ]
                    ])->first();
                    $product_transfer['product_batch_id'] = $product_batch_id[$key];
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['to_warehouse_id'])->first();
                }
                //deduct imei number if available
                if($imei_number[$key]) {
                    $imei_numbers = explode(",", $imei_number[$key]);
                    $all_imei_numbers = explode(",", $lims_product_from_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_from_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();

                if($lims_product_to_warehouse_data){
                    $lims_product_to_warehouse_data->qty += $quantity;
                }
                else{
                    $lims_product_to_warehouse_data = new Product_Warehouse();
                    $lims_product_to_warehouse_data->product_id = $pro_id;
                    $lims_product_to_warehouse_data->variant_id = $variant_id;
                    $lims_product_to_warehouse_data->product_batch_id = $product_transfer['product_batch_id'];
                    $lims_product_to_warehouse_data->warehouse_id = $data['to_warehouse_id'];
                    $lims_product_to_warehouse_data->qty = $quantity;
                }
                //add imei number if available
                if($imei_number[$key]) {
                    if($lims_product_to_warehouse_data->imei_number)
                        $lims_product_to_warehouse_data->imei_number .= ',' . $imei_number[$key];
                    else
                        $lims_product_to_warehouse_data->imei_number = $imei_number[$key];
                }
                $lims_product_to_warehouse_data->save();
            }
            elseif($data['status'] == 3) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                elseif($product_batch_id[$key]) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['from_warehouse_id'] ]
                    ])->first();
                    $product_transfer['product_batch_id'] = $product_batch_id[$key];
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                }
                //deduct imei number if available
                if($imei_number[$key]) {
                    $imei_numbers = explode(",", $imei_number[$key]);
                    $all_imei_numbers = explode(",", $lims_product_from_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_from_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();
            }

            $product_transfer['product_id'] = $pro_id;
            $product_transfer['variant_id'] = $variant_id;
            $product_transfer['imei_number'] = $imei_number[$key];
            $product_transfer['transfer_id'] = $id;
            $product_transfer['qty'] = $qty[$key];
            $product_transfer['purchase_unit_id'] = $lims_transfer_unit_data->id;
            $product_transfer['net_unit_cost'] = $net_unit_cost[$key];
            $product_transfer['tax_rate'] = $tax_rate[$key];
            $product_transfer['tax'] = $tax[$key];
            $product_transfer['total'] = $total[$key];

            if($lims_product_data->is_variant && in_array($product_variant_id[$key], $old_product_variant_id) ) {
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id],
                    ['variant_id', $variant_id]
                ])->update($product_transfer);
            }
            elseif($variant_id == null && in_array($pro_id, $old_product_id) ){
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id]
                ])->update($product_transfer);
            }
            else
                ProductTransfer::create($product_transfer);
        }

        $lims_transfer_data->update($data);
        return redirect('transfers')->with('message', 'Transfer updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $transfer_id = $request['transferIdArray'];
        foreach ($transfer_id as $id) {
            $lims_transfer_data =Transfer::find($id);
            $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
            foreach ($lims_product_transfer_data as $product_transfer_data) {
                $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
                if ($lims_transfer_unit_data->operator == '*') {
                    $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
                } else {
                    $quantity = $product_transfer_data / $lims_transfer_unit_data->operation_value;
                }

                if($lims_transfer_data->status == 1) {
                    //add quantity for from warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_warehouse_data->qty += $quantity;
                    $lims_product_warehouse_data->save();
                    //deduct quantity for to warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();

                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_warehouse_data->save();
                }
                elseif($lims_transfer_data->status == 3) {
                    //add quantity for from warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();

                    $lims_product_warehouse_data->qty += $quantity;
                    $lims_product_warehouse_data->save();
                }
                $product_transfer_data->delete();
            }
            $lims_transfer_data->delete();
        }
        return 'Transfer deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_transfer_data =Transfer::find($id);
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        foreach ($lims_product_transfer_data as $product_transfer_data) {
            $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $product_transfer_data / $lims_transfer_unit_data->operation_value;
            }

            if($lims_transfer_data->status == 1) {
                //add quantity for from warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id]
                    ])->first();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                //add imei number to from warehouse
                if($product_transfer_data->imei_number) {
                    if($lims_product_warehouse_data->imei_number)
                        $lims_product_warehouse_data->imei_number .= ',' . $product_transfer_data->imei_number;
                    else
                        $lims_product_warehouse_data->imei_number = $product_transfer_data->imei_number;
                }

                $lims_product_warehouse_data->qty += $quantity;
                $lims_product_warehouse_data->save();
                //deduct quantity for to warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id],
                        ['warehouse_id', $lims_transfer_data->to_warehouse_id]
                    ])->first();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();
                //deduct imei number if available
                if($product_transfer_data->imei_number) {
                    $imei_numbers = explode(",", $product_transfer_data->imei_number);
                    $all_imei_numbers = explode(",", $lims_product_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_warehouse_data->save();
            }
            elseif($lims_transfer_data->status == 3) {
                //add quantity for from warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id]
                    ])->first();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                //add imei number to from warehouse
                if($product_transfer_data->imei_number) {
                    if($lims_product_warehouse_data->imei_number)
                        $lims_product_warehouse_data->imei_number .= ',' . $lims_product_warehouse_data->imei_number;
                    else
                        $lims_product_warehouse_data->imei_number = $lims_product_warehouse_data->imei_number;
                }

                $lims_product_warehouse_data->qty += $quantity;
                $lims_product_warehouse_data->save();
            }
            $product_transfer_data->delete();
        }
        $lims_transfer_data->delete();
        return redirect('transfers')->with('not_permitted', 'Transfer deleted successfully');
    }
}
