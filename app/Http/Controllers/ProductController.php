<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Keygen;
use App\Brand;
use App\Category;
use App\Unit;
use App\Tax;
use App\Warehouse;
use App\Supplier;
use App\Product;
use App\ProductBatch; 
use App\Product_Warehouse;
use App\Product_Supplier;
use Auth;
use DNS1D;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use DB;
use App\Variant;
use App\ProductVariant;
use App\Proforma;
use App\ProformaCount;
use App\ProformaItem;

class ProductController extends Controller
{
    public function index() 
    {
        $role = Role::find(Auth::user()->role_id);
        $permissions = Role::findByName($role->name)->permissions;
        foreach ($permissions as $permission)
            $all_permission[] = $permission->name;
        if(empty($all_permission))
            $all_permission[] = 'dummy text';
        $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
        return view('product.index', compact('all_permission','proformas'));
    }

    public function productData(Request $request)
    {
        $columns = array( 
            0 => 'id', 
            1 => 'name', 
            2 => 'code',
            3 => 'brand_id',
            4 => 'category_id',
            5 => 'qty',
            6 => 'unit_id',
            7 => 'price',
            8 => 'cost',
            9 => 'warehouse'
        );
        
        $totalData = Product::where('is_active', true)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'products.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))){
            $products = Product::with('category', 'brand')->offset($start)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }
        else
        {
            $search = $request->input('search.value'); 
            $products =  Product::select('products.*')
                        ->with('category', 'brand')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->leftjoin('brands', 'products.brand_id', '=', 'brands.id')
                        ->where([
                            ['products.name', 'LIKE', "%{$search}%"],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['products.code', 'LIKE', "%{$search}%"],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['products.pfi_number', 'LIKE', "%{$search}%"],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['categories.name', 'LIKE', "%{$search}%"],
                            ['categories.is_active', true],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['brands.title', 'LIKE', "%{$search}%"],
                            ['brands.is_active', true],
                            ['products.is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Product::
                            join('categories', 'products.category_id', '=', 'categories.id')
                            ->leftjoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->where([
                                ['products.name','LIKE',"%{$search}%"],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['products.code', 'LIKE', "%{$search}%"],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['products.pfi_number', 'LIKE', "%{$search}%"],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['categories.name', 'LIKE', "%{$search}%"],
                                ['categories.is_active', true],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['brands.title', 'LIKE', "%{$search}%"],
                                ['brands.is_active', true],
                                ['products.is_active', true]
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
                    $nestedData['brand'] = $product->brand->title;
                else
                    $nestedData['brand'] = "N/A";
                $nestedData['category'] = $product->category->name;
                $nestedData['qty'] = $product->qty;
                if($product->unit_id == 1)
                {
                    $nestedData['unit_id'] = "Piece";
                }
                elseif($product->unit_id == 2)
                {
                    $nestedData['unit_id'] = "Dozen";
                }
                else
                {
                    $nestedData['unit_id'] = "Carton";
                }
                   
                
                $nestedData['price'] = $product->price;
                $nestedData['cost'] = $product->cost;
                if($product->warehouse_id)
                {
                    $nestedData['warehouse'] = Warehouse::find($product->warehouse_id)->name;
                }

             $nestedData['price_worth'] = ($product->qty * $product->price).' '.config('currency');
             $nestedData['cost_worth'] = ($product->qty * $product->cost).' '.config('currency');
                //$nestedData['price_worth'] = ($product->qty * $product->price).'/'.($product->qty * $product->cost);
 
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                            <button="type" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                        </li>  ';
                           
               
                    $nestedData['options'] .= '<li>
                            <a href="'.route('products.edit', $product->id).'" class="btn btn-link"><i class="fa fa-edit"></i> '.trans('file.edit').'</a>
                        </li>';
                
                    $nestedData['options'] .= \Form::open(["route" => ["products.destroy", $product->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="fa fa-trash"></i> '.trans("file.delete").'</button> 
                            </li>'.\Form::close().'
                        </ul>
                    </div>';
                // data for product details by one click
                if($product->tax_id)
                    $tax = $product->tax_id;
                else
                    $tax = "N/A";

                if($product->tax_method == 1)
                    $tax_method = trans('file.Exclusive');
                else
                    $tax_method = trans('file.Inclusive');
              
               
              
                $nestedData['product'] = array( '[ "'.$product->type.'"', ' "'.$product->name.'"', ' "'.$product->code.'"', ' "'.$nestedData['brand'].'"', ' "'.$nestedData['category'].'"', ' "'.$nestedData['unit_id'].'"',  ' "'.$product->cost.'"', ' "'.$product->price.'"', ' "'.$tax.'"', ' "'.$tax_method.'"',  ' "'.$product->alert_quantity.'"', ' "'.preg_replace('/\s+/S', " ", $product->product_details).'"', ' "'.$product->id.'"',  ' "'.$product->product_list.'"', ' "'.$product->variant_list.'"', ' "'.$product->qty_list.'"', ' "'.$product->price_list.'"', ' "'.$product->qty.'"', ' "'.$nestedData['price_worth'].'" ',' "'.$nestedData['cost_worth'].'"]'
                );
                //$nestedData['imagedata'] = DNS1D::getBarcodePNG($product->code, $product->barcode_symbology);
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
     
    public function create() 
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('products-add')){ 
            $lims_brand_list = Brand::where('is_active', true)->get();
            $product = Product::get();
            $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('product.create',compact('product', 'proformas', 'lims_brand_list', 'lims_category_list', 'lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $name = preg_replace('/[\n\r]/', "<br>", htmlspecialchars(trim($myvalue['name'])));
        $ProFind = ProformaCount::where('pro_id',$myvalue['pfi_number'])->first();
        if($ProFind)
        { 
            $MyPro = ProformaCount::find($ProFind->id);
            if($myvalue['product_unit'] == '1') 
            {
               $MyPro->product_qty += $myvalue['piece_no'];
            }
            if($myvalue['product_unit'] == '2')
            {
                $MyPro->product_qty += $myvalue['dozen_no'];
            }
            if($myvalue['product_unit'] == '3') 
            {
                $MyPro->product_qty += $myvalue['carton_no'];
            }
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
            if($myvalue['product_unit'] == '1') 
            {
               $ProCount->product_qty += $myvalue['piece_no'];
            }
            if($myvalue['product_unit'] == '2')
            {
                $ProCount->product_qty += $myvalue['dozen_no'];
            }
            if($myvalue['product_unit'] == '3') 
            {
                $ProCount->product_qty += $myvalue['carton_no'];
            }
            $ProCount->save();

        }
        $Brand = new Product();
        $Brand->pfi_number = Proforma::find($myvalue['pfi_number'])->pfi_number; 
        $Brand->name = $name;
        $Brand->code = $myvalue['code'];
        if($myvalue['brand_id']!="" || $myvalue['brand_id']!= null) {
            $Brand->brand_id = $myvalue['brand_id'];
        }
        $Brand->category_id = $myvalue['category_id'];
        $Brand->unit_id = $myvalue['product_unit'];
        if($myvalue['product_unit'] == '1') {
        $Brand->qty = $myvalue['piece_no'];
        }
        if($myvalue['product_unit'] == '2') {
            $Brand->dozen_no = $myvalue['dozen_no'];
            $no = $myvalue['dozen_no'];
            $Brand->qty = $no; 
            }
        if($myvalue['product_unit'] == '3') {
            $Brand->carton_no = $myvalue['carton_no'];
            $Brand->piece_in_carton = $myvalue['piece_in_carton'];
            $Brand->qty = $myvalue['carton_no'];
            }
        $Brand->cost = $myvalue['cost'];
        $Brand->price = $myvalue['price'];
        if($myvalue['alert_quantity']!="" || $myvalue['alert_quantity']!= null) {
        $Brand->alert_quantity = $myvalue['alert_quantity'];
            }
        if($myvalue['featured']!="" || $myvalue['featured']!= null) {
            $Brand->featured = $myvalue['featured'];
        }
        $Brand->warehouse_id = $myvalue['warehouse_id'];
        if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
            $detail = str_replace('"', '@', $myvalue['product_details']);
            $Brand->product_details = $detail;
        }
        $Brand->is_active = 1;
        $Brand->user_id = Auth::user()->id;
        $Brand->save();
        return response()->json(['Success'=>'Product Added Successfully']);
       
      
    }

    public function edit($id)
    { 
            $brand = Brand::where('is_active', true)->get();
            $category = Category::where('is_active', true)->get();
            $product = Product::where('id', $id)->first(); 
            $vehiclePro = Proforma::where('pfi_number', $product->pfi_number)->first();
            $proformas = Proforma::where('status', '>=', 5)->where('is_active', true)->orderBy('declared_at', 'desc')->get();
            $products = Product::where('id', '!=', $id)->get();
          //  $lims_product_variant_data = $lims_product_data->variant()->orderBy('position')->get();
            $warehouse = Warehouse::where('is_active', true)->get();
            return view('product.edit',compact( 'brand', 'proformas', 'vehiclePro', 'category', 'product','products', 'warehouse'));
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
        //dd($myvalue['reference']);
        $name = preg_replace('/[\n\r]/', "<br>", htmlspecialchars(trim($myvalue['name'])));
        $pdct_qty ;
        if($myvalue['product_unit'] == '1') 
        {
           $pdct_qty = $myvalue['piece_no'];
        }
        if($myvalue['product_unit'] == '2')
        {
            $pdct_qty = $myvalue['dozen_no'];
        }
        if($myvalue['product_unit'] == '3') 
        {
            $pdct_qty = $myvalue['carton_no'];
        }
        if($myvalue['old_pfi'] == $myvalue['pfi_id'])
        {
           $Kas = ProformaCount::where('pro_id',$myvalue['old_pfi'])->first();
           $Qty_UPD = ProformaCount::find($Kas->id);
           $Qty_UPD->product_qty -= $myvalue['old_qty'];
           $Qty_UPD->product_qty += $pdct_qty;
           $Qty_UPD->save();
        }
        else
        {
            $Pro_Old = ProformaCount::where('pro_id',$myvalue['old_pfi'])->first();
            $Update = ProformaCount::find($Pro_Old->id);
            $Update->product_qty -= $myvalue['old_qty'];
            $Update->save();
            $ProFind = ProformaCount::where('pro_id',$myvalue['pfi_id'])->first();
            if($ProFind)
            {
                $MyPro = ProformaCount::find($ProFind->id);
                $MyPro->product_qty += $pdct_qty;
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
                $ProCount->product_qty = $pdct_qty;
                $ProCount->save();
    
            }
        }
        $Brand = Product::find($myvalue['edit_id']); 
        $Brand->pfi_number = Proforma::find($myvalue['pfi_id'])->pfi_number;
        $Brand->name = $name;
        $Brand->code = $myvalue['code'];
        if($myvalue['brand_id']!="" || $myvalue['brand_id']!= null) {
            $Brand->brand_id = $myvalue['brand_id'];
        }
        $Brand->category_id = $myvalue['category_id'];
        $Brand->unit_id = $myvalue['product_unit'];
        if($myvalue['product_unit'] == '1') {
        $Brand->qty = $myvalue['piece_no'];
        }
        if($myvalue['product_unit'] == '2') {
            $Brand->dozen_no = $myvalue['dozen_no'];
            $no = $myvalue['dozen_no'];
            $Brand->qty = $no; 
            }
            if($myvalue['product_unit'] == '3') {
                $Brand->carton_no = $myvalue['carton_no'];
                $Brand->piece_in_carton = $myvalue['piece_in_carton'];
                $Brand->qty = $myvalue['carton_no'];
                }
        $Brand->cost = $myvalue['cost'];
        $Brand->price = $myvalue['price'];
        if($myvalue['alert_quantity']!="" || $myvalue['alert_quantity']!= null) {
        $Brand->alert_quantity = $myvalue['alert_quantity'];
            }
            if($myvalue['featured']!="" || $myvalue['featured']!= null) {
                $Brand->featured = $myvalue['featured'];
            }
            $Brand->warehouse_id = $myvalue['warehouse_id'];
            if($myvalue['product_details']!="" || $myvalue['product_details']!= null) {
                $detail = str_replace('"', '@', $myvalue['product_details']);
                $Brand->product_details = $detail;
            }
        $Brand->is_active = 1;
        $Brand->user_id = Auth::user()->id;
        $Brand->save();
        return response()->json(['Success'=>'Product Added Successfully']);
  
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }

    public function search(Request $request)
    {
        $product_code = explode(" ", $request['data']);
        $lims_product_data = Product::where('code', $product_code[0])->first();

        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->qty;
        $product[] = $lims_product_data->price;
        $product[] = $lims_product_data->id;
        return $product;
    }

    public function saleUnit($id)
    {
        $unit = Unit::where("base_unit", $id)->orWhere('id', $id)->pluck('unit_name','id');
        return json_encode($unit);
    }

    public function getData($id, $variant_id)
    {
        if($variant_id) {
            $data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.name', 'product_variants.item_code')
                ->where([
                    ['products.id', $id],
                    ['product_variants.variant_id', $variant_id]
                ])->first();
            $data->code = $data->item_code;
        }
        else
            $data = Product::select('name', 'code')->find($id);
        return $data;
    }

    public function productWarehouseData($id)
    {
        $warehouse = [];
        $qty = [];
        $batch = [];
        $expired_date = [];
        $imei_number = [];
        $warehouse_name = [];
        $variant_name = [];
        $variant_qty = [];
        $product_warehouse = [];
        $product_variant_warehouse = [];
        $lims_product_data = Product::select('id', 'is_variant')->find($id);
        if($lims_product_data->is_variant) {
            $lims_product_variant_warehouse_data = Product_Warehouse::where('product_id', $lims_product_data->id)->orderBy('warehouse_id')->get();
            $lims_product_warehouse_data = Product_Warehouse::select('warehouse_id', DB::raw('sum(qty) as qty'))->where('product_id', $id)->groupBy('warehouse_id')->get();
            foreach ($lims_product_variant_warehouse_data as $key => $product_variant_warehouse_data) {
                $lims_warehouse_data = Warehouse::find($product_variant_warehouse_data->warehouse_id);
                $lims_variant_data = Variant::find($product_variant_warehouse_data->variant_id);
                $warehouse_name[] = $lims_warehouse_data->name;
                $variant_name[] = $lims_variant_data->name;
                $variant_qty[] = $product_variant_warehouse_data->qty;
            }
        }
        else {
            $lims_product_warehouse_data = Product_Warehouse::where('product_id', $id)->orderBy('warehouse_id', 'asc')->get();
        }
        foreach ($lims_product_warehouse_data as $key => $product_warehouse_data) {
            $lims_warehouse_data = Warehouse::find($product_warehouse_data->warehouse_id);
            if($product_warehouse_data->product_batch_id) {
                $product_batch_data = ProductBatch::select('batch_no', 'expired_date')->find($product_warehouse_data->product_batch_id);
                $batch_no = $product_batch_data->batch_no;
                $expiredDate = date(config('date_format'), strtotime($product_batch_data->expired_date));
            }
            else {
                $batch_no = 'N/A';
                $expiredDate = 'N/A';
            }
            $warehouse[] = $lims_warehouse_data->name;
            $batch[] = $batch_no;
            $expired_date[] = $expiredDate;
            $qty[] = $product_warehouse_data->qty;
            if($product_warehouse_data->imei_number)
                $imei_number[] = $product_warehouse_data->imei_number;
            else
                $imei_number[] = 'N/A';
        }

        $product_warehouse = [$warehouse, $qty, $batch, $expired_date, $imei_number];
        $product_variant_warehouse = [$warehouse_name, $variant_name, $variant_qty];
        return ['product_warehouse' => $product_warehouse, 'product_variant_warehouse' => $product_variant_warehouse];
    }

    public function printBarcode()
    {
        $lims_product_list_without_variant = $this->productWithoutVariant();
        $lims_product_list_with_variant = $this->productWithVariant();
        return view('product.print_barcode', compact('lims_product_list_without_variant', 'lims_product_list_with_variant'));
    }

    public function productWithoutVariant()
    {
        return Product::ActiveStandard()->select('id', 'name', 'code')
                ->whereNull('is_variant')->get();
    }

    public function productWithVariant()
    {
        return Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->ActiveStandard()
                ->whereNotNull('is_variant')
                ->select('products.id', 'products.name', 'product_variants.item_code')
                ->orderBy('position')->get();
    }

    public function limsProductSearch(Request $request)
    {
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $lims_product_data = Product::where([
            ['code', $product_code[0] ],
            ['is_active', true]
        ])->first();
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.item_code', 'product_variants.variant_id', 'product_variants.additional_price')
                ->where('product_variants.item_code', $product_code[0])
                ->first();

            $variant_id = $lims_product_data->variant_id;
            $additional_price = $lims_product_data->additional_price;
        }
        else {
            $variant_id = '';
            $additional_price = 0;
        }
        $product[] = $lims_product_data->name;
        if($lims_product_data->is_variant)
            $product[] = $lims_product_data->item_code;
        else
            $product[] = $lims_product_data->code;
        
        $product[] = $lims_product_data->price + $additional_price;
        $product[] = DNS1D::getBarcodePNG($lims_product_data->code, $lims_product_data->barcode_symbology);
        $product[] = $lims_product_data->promotion_price;
        $product[] = config('currency');
        $product[] = config('currency_position');
        $product[] = $lims_product_data->qty;
        $product[] = $lims_product_data->id;
        $product[] = $variant_id;
        return $product;
    }

    /*public function getBarcode()
    {
        return DNS1D::getBarcodePNG('72782608', 'C128');
    }*/

    public function checkBatchAvailability($product_id, $batch_no, $warehouse_id)
    {
        $product_batch_data = ProductBatch::where([
            ['product_id', $product_id],
            ['batch_no', $batch_no]
        ])->first();
        if($product_batch_data) {
            $product_warehouse_data = Product_Warehouse::select('qty')
            ->where([
                ['product_batch_id', $product_batch_data->id],
                ['warehouse_id', $warehouse_id]
            ])->first();
            if($product_warehouse_data) {
                $data['qty'] = $product_warehouse_data->qty;
                $data['product_batch_id'] = $product_batch_data->id;
                $data['expired_date'] = date(config('date_format'), strtotime($product_batch_data->expired_date));
                $data['message'] = 'ok';
            }
            else {
                $data['qty'] = 0;
                $data['message'] = 'This Batch does not exist in the selected warehouse!';
            }            
        }
        else {
            $data['message'] = 'Wrong Batch Number!';
        }
        return $data;
    }


    public function importproduct(Request $request)
    {   
        //get file
        $proforma_id = $request->pfi_id;
        $upload=$request->file('file');
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
           if($data['name']== "" || $data['code']=="" || $data['category']=="" || $data['unit']=="" || $data['quantity']=="" || $data['warehouse']=="" || $data['sellprice']=="")
           {
            return redirect('products')->with('not_permitted', 'Please fill all required fields in CSV File !');
           }
           if($data['brand'] != 'N/A' && $data['brand'] != ''){
                $lims_brand_data = Brand::firstOrCreate(['title' => $data['brand'], 'is_active' => true]);
                $brand_id = $lims_brand_data->id;
           }
            else 
                $brand_id = null;

           $lims_category_data = Category::firstOrCreate(['name' => $data['category'], 'is_active' => true]);
           $lims_warehouse_data = Warehouse::firstOrCreate(['name' => $data['warehouse'], 'is_active' => true]);
           $Imp_ch = Product::where('name', $data['name'])->where('code', $data['code'])->where('is_active', true)->first();
           if($Imp_ch)
           {
            return redirect('products')->with('not_permitted', "Name " . $data['name'] . " and Code " . $data['code'] . " Already Exist. Note: Rows before this are stored  !");
           }
           $Abekoo = ProformaCount::where('pro_id', $proforma_id)->first();
           if($Abekoo)
           { 
               $MyPro = ProformaCount::find($Abekoo->id);
               $MyPro->product_qty += $data['quantity'];
               $MyPro->save();
           }
           else
           {
               $Orginal = Proforma::find($proforma_id);
               $Orginal->status = 6;
               $Orginal->save();
               $proItem = ProformaItem::where('proforma_id', $Orginal->id)->count();
               $ItemQty = ProformaItem::where('proforma_id', $Orginal->id)->sum('qty');
               $ProCount = new ProformaCount();
               $ProCount->pro_id = $Orginal->id;
               $ProCount->purchase_items = $proItem;
               $ProCount->purchase_qty = $ItemQty; 
               $ProCount->product_qty += $data['quantity']; 
               $ProCount->save();
   
           }
           $product = Product::firstOrNew([ 'name'=>$data['name'], 'code'=>$data['code'], 'is_active'=>true ]);
           $product->pfi_number = Proforma::find($proforma_id)->pfi_number;
           $product->name = htmlspecialchars(trim($data['name']));
           $product->code = $data['code'];
           $product->brand_id = $brand_id;
           $product->category_id = $lims_category_data->id;
           if($data['unit']== "Piece" || $data['unit']== "piece")
           {
            $product->unit_id = 1;
           }
           if($data['unit']== "Dozen" || $data['unit']== "dozen")
           {
            $product->unit_id = 2;
            $product->dozen_no = str_replace(",","",$data['quantity']);
           }
           if($data['unit']== "Carton" || $data['unit']== "carton")
           {
            $product->unit_id = 3;
            $product->carton_no =str_replace(",","",$data['quantity']);
            $product->piece_in_carton = str_replace(",","",$data['pieceincarton']);

           }
           $product->cost = str_replace(",","",$data['purchaseprice']);
           $product->price = str_replace(",","",$data['sellprice']);
           $product->qty = str_replace(",","",$data['quantity']);
           $product->warehouse_id = $lims_warehouse_data->id;
           $product->featured = 1;
           $details = str_replace('"', '@', $data['productdetail']);
           $product->product_details = $details;
           $product->is_active = true;
           $product->user_id = Auth::user()->id;
           $product->save();

         }
         return redirect('products')->with('import_message', 'Products imported successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $product_id = $request['productIdArray'];
        foreach ($product_id as $id) {
            $lims_product_data = Product::findOrFail($id);
            $lims_product_data->is_active = false;
            $lims_product_data->save();
        }
        return 'Product deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_product_data = Product::findOrFail($id);
        $lims_product_data->is_active = false;
        $lims_product_data->save();
        return redirect('products')->with('message', 'Product deleted successfully');
    }
}
