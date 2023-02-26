@extends('layout.top-head')
@section('content')
@if($errors->has('phone_number'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('phone_number') }}</div>
@endif
@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<!-- Side Navbar -->
<nav class="side-navbar">
    <span class="brand-big">
        @if($general_setting->site_logo)
        <a href="{{url('/')}}"><img src="{{url('logo/', $general_setting->site_logo)}}" height="50" width="190"></a>
        @else 
        <a href="{{url('/')}}"><h1 class="d-inline">{{$general_setting->site_title}}</h1></a>
        @endif
    </span>
    <ul id="side-main-menu" class="side-menu list-unstyled">
        <li><a href="{{url('/')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>
        <?php
        $role = DB::table('roles')->find(Auth::user()->role_id);
        ?>
        <li><a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-card"></i><span>Purchases</span></a>
          <ul id="purchase" class="collapse list-unstyled ">
              <li id="purchase-bank-menu"><a href="{{route('purchases.bank')}}">Bank</a></li>
              <li id="purchase-proforma-menu"><a href="{{route('purchases.proforma')}}">Proforma Invoice</a></li>
              <li id="purchase-bank-stage-menu"><a href="{{route('purchases.bank-submit')}}">Bank Stage</a></li>
              <li id="purchase-booking-menu"><a href="{{route('purchases.booking')}}">Booking Stage</a></li>
              <li id="purchase-transition-menu"><a href="{{route('purchases.transition')}}">Transition Stage</a></li>
              <li id="purchase-custom-stage-menu"><a href="{{route('purchases.custom-stage')}}">Custom Stage</a></li>
          </ul> 
          </li> 
        <!-- Vehicle Menu --> 
        <li><a href="#vehicle" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-car"></i><span>Vehicles</span></a>
        <ul id="vehicle" class="collapse list-unstyled ">
            <li id="vehicle-brand-menu"><a href="{{route('vehicles.brand')}}">{{trans('file.Vehicle Brand')}}</a></li>
            <li id="vehicle-category-menu"><a href="{{route('vehicle.categories')}}">{{trans('file.Vehicle Category')}}</a></li>
            <li id="vehicle-add-menu"><a href="{{route('vehicle.create')}}">{{trans('file.Vehicle Add')}}</a></li>
            <li id="vehicle-list-menu"><a href="{{route('vehicle.index')}}">{{trans('file.Vehicle List')}}</a></li>
            <li id="vehicle-manufacture-menu"><a href="{{route('manufacture.index')}}">Manufacture List</a></li>
            <li id="vehicle-finished-menu"><a href="{{route('finished.index')}}">Finished Good List</a></li>
            <li id="vehicle-product-menu"><a href="{{route('vehicle.product.index')}}">Vehicle Product List</a></li>
        </ul>
        </li>


        <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-list"></i><span>Products</span><span></a>
        <ul id="product" class="collapse list-unstyled ">
            <li id="brand-menu"><a href="{{route('brand.index')}}">{{trans('file.Brand')}}</a></li>
            <li id="category-menu"><a href="{{route('category.index')}}">{{__('file.category')}}</a></li>
            <li id="product-list-menu"><a href="{{route('products.index')}}">Product List</a></li>
            <li id="product-create-menu"><a href="{{route('products.create')}}">Add Product</a></li>      
        </ul>
        </li>

       <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-cart"></i><span>{{trans('file.Sale')}}</span></a>
        <ul id="sale" class="collapse list-unstyled ">
            <li id="sale-list-menu"><a href="{{route('sales.index')}}">{{trans('file.Sale List')}}</a></li> 
            <li><a href="{{route('sale.pos')}}">POS</a></li>
        </ul>
        </li>  
        <li><a href="#transfer" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-export"></i><span>{{trans('file.Transfer')}}</span></a>
          <ul id="transfer" class="collapse list-unstyled ">
              <li id="transfer-list-menu"><a href="{{route('transfers.index')}}">{{trans('file.Transfer List')}}</a></li>
              <li id="transfer-create-menu"><a href="{{route('transfers.create')}}">{{trans('file.Add Transfer')}}</a></li>
          </ul>
          </li>
         <!-- Vehicle Menu 
         <li><a href="#report" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-file"></i><span>Reports</span></a>
          <ul id="report" class="collapse list-unstyled ">
              <li id="report-product-menu"><a href="{{route('sale.product_sales_report')}}">Product Sale Report</a></li>
              <li id="report-vehicle-menu"><a href="{{route('sale.vehicle_sales_report')}}">Vehicle Sale Report</a></li>
          </ul>  
          </li>   -->            
        <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
            <ul id="setting" class="collapse list-unstyled ">
                <li id="bank-menu"><a href="{{route('bank.index')}}">{{trans('file.banks')}}</a></li>
                <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">{{trans('file.Warehouse')}}</a></li>
            </ul>
        </li>
       
    </ul>
  </nav>
<section class="forms pos-section">
    <div class="container-fluid">
        <div class="row">
            <audio id="mysoundclip1" preload="auto">
                <source src="{{url('/beep/beep-timber.mp3')}}"></source>
            </audio>
            <audio id="mysoundclip2" preload="auto">
                <source src="{{url('/beep/beep-07.mp3')}}"></source>
            </audio>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0">
                        {!! Form::open(['route' => 'sales.store', 'method' => 'post', 'id' =>'posform', 'files' => true, 'class' => 'payment-form']) !!}
                        @php
                            if($lims_pos_setting_data)
                                $keybord_active = $lims_pos_setting_data->keybord_active;
                            else
                                $keybord_active = 0;

                            $customer_active = DB::table('permissions')
                              ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                              ->where([
                                ['permissions.name', 'customers-add'],
                                ['role_id', \Auth::user()->role_id] ])->first();
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                   
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="input-group pos">
                                                <select required name="customer_id" id="customer_id" class="selectpicker form-control" data-live-search="true" title="Select customer...">
                                                  
                                                    @foreach($lims_customer_list as $customer)
                                                    <option value="{{$customer->id}}">{{$customer->name . ' (' . $customer->phone_number . ')'}}</option>
                                                @endforeach
                                                </select>
                                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addCustomer"><i class="dripicons-plus"></i></button>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" name="kasu_id" value="0">
                                            <select required id="kasu_type"  name="kasu_type" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select product type...">
                                                <option value="1">Other Products</option>
                                                <option value="2">Vehicles</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($lims_pos_setting_data)
                                            <input type="hidden" name="warehouse_id_hidden" value="{{$lims_pos_setting_data->warehouse_id}}">
                                            @endif
                                            <select required id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                                @foreach($lims_warehouse_list as $warehouse)
                                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input  type="hidden" id="reference-no" name="reference_no" class="form-control" placeholder="Type reference number" value=""/>
                                        </div>
                                      </div>
                                    <div class="col-md-12">
                                        <div class="search-box form-group">
                                            <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Search Name/Code" class="form-control"  />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="table-responsive transaction-list">
                                        <table id="myTable" class="table table-hover table-striped order-list table-fixed">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-2">{{trans('file.product')}}</th>
                                                    <th id="sell-unit12" class="col-sm-2">{{trans('file.sellUnit')}}</th>
                                                    <th id="vehicle-engine-no" class="col-sm-2">Engine No</th>
                                                    <th class="col-sm-2">{{trans('file.Price')}}</th>
                                                    <th class="col-sm-3">{{trans('file.Quantity')}}</th>
                                                    <th class="col-sm-3">{{trans('file.Subtotal')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-id">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row" style="display: none;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" value="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" value="0.00"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" />
                                            <input type="hidden" name="order_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="grand_total" />
                                            <input type="hidden" name="used_points" />
                                            <input type="hidden" name="coupon_discount" />
                                            <input type="hidden" name="sale_status" value="1" />
                                            <input type="hidden" name="coupon_active">
                                            <input type="hidden" name="coupon_id">
                                            <input type="hidden" name="coupon_discount" />

                                            <input type="hidden" name="pos" value="1" />
                                            <input type="hidden" name="draft" value="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 totals" style="border-top: 2px solid #e4e6fc; padding-top: 10px;">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Items')}}</span><span id="item">0</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Total')}}</span><span id="subtotal">0.00</span>
                                        </div>
                                       <!-- <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Discount')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-discount-modal"> <i class="dripicons-document-edit"></i></button></span><span id="discount">0.00</span>
                                        </div> 
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Coupon')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#coupon-modal"><i class="dripicons-document-edit"></i></button></span><span id="coupon-text">0.00</span>
                                        </div> -->
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{trans('file.Tax')}} <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#1order-tax"></button></span><span id="tax">0.00</span>
                                        </div>
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-amount">
                        <h2>{{trans('file.grand total')}} <span id="grand-total">0.00</span></h2>
                    </div>
                    <div class="payment-options">
                        <div class="column-5">
                            <button style="background: #0984e3" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cash-btn"><i class="fa fa-money"></i> {{trans('file.Payment')}}</button>
                        </div>  
                    </div>
                </div>
            </div>
            <!-- payment modal -->
            <div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Finalize Sale')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6 mt-1">
                                            <label>{{trans('file.Recieved Amount')}} *</label>
                                            <input type="text" name="paying_amount" class="form-control numkey" readonly required step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <input type="hidden" name="deposit_bank_id">
                                            <label>{{trans('file.Paying Amount')}} *</label>
                                            <input type="text" name="paid_amount" readonly class="form-control numkey"  step="any">
                                        </div>
                                       
                                       
                                        <div class="col-md-6 mt-1">
                                            <input type="hidden" name="paid_by_id">
                                            <label>{{trans('file.Paid By')}}</label>
                                            <select name="paid_by_id_select" class="form-control selectpicker">
                                                <option value="1">Cash</option>
                                                <option value="2">Deposit</option>
                                                <option value="3">Credit</option>
                                                <option value="4">Partial Credit</option>
                                               
                                            </select>   
                                        </div>
                                        <div class="col-md-6 mt-1" id="credit_div">
                                            <label>Credit Amount *</label>
                                            <input type="text" name="credit_amount" class="form-control numkey"  step="any">
                                        </div>
                                        <div class="col-md-6 mt-1" id="bank_div">
                                           
                                            <label>{{trans('file.Deposited Bank')}}</label>
                                            <select name="bank_id" id="bank_id" required class="form-control selectpicker">
                                                @foreach($lims_customer_list1 as $customer)  
                                                    <option value="{{$customer->id}}">{{$customer->title}}</option>
                                                @endforeach
                                               
                                            </select>
                                        </div>
                                       
                                       
                                        <div class="form-group col-md-12">
                                            <label>{{trans('file.Payment Note')}}</label>
                                            <textarea id="payment_note" rows="2" class="form-control" name="payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-6 form-group">
                                            <label>{{trans('file.Sale Note')}}</label>
                                            <textarea rows="3" class="form-control" name="sale_note"></textarea>
                                        </div>
                                       
                                    </div>
                                    <div class="mt-3">
                                        <button id="submit-btn" type="button" class="btn btn-primary">{{trans('file.submit')}}</button>
                                    </div>
                                </div>
                                <div class="col-md-2 qc" data-initial="1">
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_discount modal -->
            <div id="order-discount-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Order Discount')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{trans('file.Order Discount Type')}}</label>
                                    <select id="order-discount-type" name="order_discount_type_select" class="form-control">
                                      <option value="Flat">{{trans('file.Flat')}}</option>
                                      <option value="Percentage">{{trans('file.Percentage')}}</option>
                                    </select>
                                    <input type="hidden" name="order_discount_type">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{trans('file.Value')}}</label>
                                    <input type="text" name="order_discount_value" class="form-control numkey" id="order-discount-val" onkeyup='saveValue(this);'>
                                    <input type="hidden" name="order_discount" class="form-control" id="order-discount" onkeyup='saveValue(this);'>
                                </div>
                            </div>
                            <button type="button" name="order_discount_btn" class="btn btn-primary" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>

           
            <div id="order-tax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{trans('file.Order Tax')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="order_tax_rate">
                                <select class="form-control" name="order_tax_rate_select" id="order-tax-rate-select">
                                    <option value="15">No Tax</option>
                                    @foreach($lims_tax_list as $tax)
                                    <option value="{{$tax->rate}}">{{$tax->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" name="order_tax_btn" class="btn btn-primary" data-dismiss="modal">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>   


            {!! Form::close() !!}
            <!-- product list -->
            <div class="col-md-6">
                <!-- navbar-->
                <header>
                    <nav class="navbar">
                        <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>

                        <div class="navbar-header">
                          <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <li class="nav-item"><a id="switch-theme" data-toggle="tooltip" title="{{trans('file.Switch Theme')}}"><i class="dripicons-brightness-max"></i></a></li>
                            <li class="nav-item"><a id="btnFullscreen" data-toggle="tooltip" title="Full Screen"><i class="dripicons-expand"></i></a></li>
                           
                            <li class="nav-item">
                                <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst(Auth::user()->name)}}</span> <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="right-sidebar">
                                    <li>
                                        <a href="{{route('user.profile', ['id' => Auth::id()])}}"><i class="dripicons-user"></i> {{trans('file.profile')}}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                                            {{trans('file.logout')}}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                          </ul>
                        </div>
                    </nav>
                </header>
                <div class="filter-window">
                    <div class="category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose category</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($lims_category_list as $category)
                            <div class="col-md-3 category-img text-center" data-category="{{$category->id}}">
                                <p class="text-center">{{$category->name}}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="vehicle-category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose category</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($vehicles_category as $category)
                            <div class="col-md-3 category-img text-center" data-category="{{$category->id}}">
                                <p class="text-center">{{$category->name}}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="brand mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose brand</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($lims_brand_list as $brand)
                           
                                <div class="col-md-3 brand-img text-center" data-brand="{{$brand->id}}">
                                   
                                    <p class="text-center">{{$brand->title}}</p>
                                </div>
                      
                            @endforeach
                        </div>
                    </div>
                    <div class="vehicle-brand mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose brand</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($vehicles_brand as $brand)
                           
                                <div class="col-md-3 brand-img text-center" data-brand="{{$brand->id}}">
                                   
                                    <p class="text-center">{{$brand->name}}</p>
                                </div>
                      
                            @endforeach
                        </div>
                    </div>
                </div>
                 <div class="row"> 
                    <div class="col-md-3">
                        <button class="btn btn-block btn-info" id="products-filter"> <i class="fa fa-product-hunt"></i>      Other Products </button>
                   </div>
                   <div class="col-md-3">
                    <button class="btn btn-block btn-info" id="vehicles-filter"><i class="fa fa-car"></i>      Vehicles</button>
                </div>
            </div>
                <!-- c & B -->
                <br>
                <div class="row" id="product-both">
                    <div class="col-md-3">
                        <button class="btn btn-link" id="category-filter"><i class="fa fa-list-alt" aria-hidden="true"></i>  {{trans('file.category')}}</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-link" id="brand-filter"><i class="fa fa-shopping-cart "></i>  {{trans('file.Brand')}}</button>
                    </div>
                    </div>
                    <div class="row"  id="vehicle-both" >
                    <div class="col-md-3">
                        <button class="btn btn-link" id="vehiclecategory-filter"><i class="fa fa-list-alt" aria-hidden="true"></i>  {{trans('file.category')}}</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-link" id="vehiclebrand-filter"><i class="fa fa-car"></i>   {{trans('file.Brand')}}</button>
                    </div>
                    </div>
                
                
                  <div class="row">
                   
                   
                    <div class="col-md-12 mt-1 table-container">
                      
                        <table id="vehicle-table" class="table no-shadow vehicle-list">
                            <thead class="d-none">
                                
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            @for ($i=0; $i < ceil($vehicle_number/5); $i++)
                                <tr>
                                    <td class="product-img sound-btn" title="{{$vehicles[0+$i*5]->name}}" data-product ="{{$vehicles[0+$i*5]->code . ' (' . $vehicles[0+$i*5]->engine_no .' , CHA.NO: '.$vehicles[0+$i*5]->chassis_no .')'}}">
                                        <p>{{$vehicles[0+$i*5]->name}}</p>
                                        <span>{{$vehicles[0+$i*5]->chassis_no}}</span>
                                    </td>
                                    @if(!empty($vehicles[1+$i*5]))
                                    <td class="product-img sound-btn" title="{{$vehicles[1+$i*5]->name}}" data-product ="{{$vehicles[1+$i*5]->code . ' (' . $vehicles[1+$i*5]->engine_no .' , CHA.NO: '.$vehicles[1+$i*5]->chassis_no .')'}}">
                                        <p>{{$vehicles[1+$i*5]->name}}</p>
                                        <span>{{$vehicles[1+$i*5]->chassis_no}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                    @if(!empty($vehicles[2+$i*5]))
                                    <td class="product-img sound-btn" title="{{$vehicles[2+$i*5]->name}}" data-product ="{{$vehicles[2+$i*5]->code . ' (' . $vehicles[2+$i*5]->engine_no .' , CHA.NO: '.$vehicles[2+$i*5]->chassis_no .')'}}">
                                        <p>{{$vehicles[2+$i*5]->name}}</p>
                                        <span>{{$vehicles[2+$i*5]->chassis_no}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                    @if(!empty($vehicles[3+$i*5]))
                                    <td class="product-img sound-btn" title="{{$vehicles[3+$i*5]->name}}" data-product ="{{$vehicles[3+$i*5]->code . ' (' . $vehicles[3+$i*5]->engine_no .' , CHA.NO: '.$vehicles[3+$i*5]->chassis_no .')'}}">
                                        <p>{{$vehicles[3+$i*5]->name}}</p>
                                        <span>{{$vehicles[3+$i*5]->chassis_no}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td> 
                                    @endif
                                    @if(!empty($vehicles[4+$i*5]))
                                    <td class="product-img sound-btn" title="{{$vehicles[4+$i*5]->name}}" data-product ="{{$vehicles[4+$i*5]->code . ' (' . $vehicles[4+$i*5]->engine_no .' , CHA.NO: '.$vehicles[4+$i*5]->chassis_no .')'}}">
                                        <p>{{$vehicles[4+$i*5]->name}}</p>
                                        <span>{{$vehicles[4+$i*5]->chassis_no}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                        <table id="product-table" class="table no-shadow product-list">
                          
                            <thead class="d-none">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            @for ($i=0; $i < ceil($product_number/5); $i++)
                                <tr>
                                    <td class="product-img sound-btn" title="{{$lims_product_list[0+$i*5]->name}}" data-product ="{{$lims_product_list[0+$i*5]->code . ' (' . $lims_product_list[0+$i*5]->name . ')'}}">
                                        <p>{{$lims_product_list[0+$i*5]->name}}</p>
                                        <span>{{$lims_product_list[0+$i*5]->code}}</span>
                                    </td>
                                    @if(!empty($lims_product_list[1+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[1+$i*5]->name}}" data-product ="{{$lims_product_list[1+$i*5]->code . ' (' . $lims_product_list[1+$i*5]->name . ')'}}">
                                        <p>{{$lims_product_list[1+$i*5]->name}}</p>
                                        <span>{{$lims_product_list[1+$i*5]->code}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                    @if(!empty($lims_product_list[2+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[2+$i*5]->name}}" data-product ="{{$lims_product_list[2+$i*5]->code . ' (' . $lims_product_list[2+$i*5]->name . ')'}}">
                                        <p>{{$lims_product_list[2+$i*5]->name}}</p>
                                        <span>{{$lims_product_list[2+$i*5]->code}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                    @if(!empty($lims_product_list[3+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[3+$i*5]->name}}" data-product ="{{$lims_product_list[3+$i*5]->code . ' (' . $lims_product_list[3+$i*5]->name . ')'}}">
                                        <p>{{$lims_product_list[3+$i*5]->name}}</p>
                                        <span>{{$lims_product_list[3+$i*5]->code}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                    @if(!empty($lims_product_list[4+$i*5]))
                                    <td class="product-img sound-btn" title="{{$lims_product_list[4+$i*5]->name}}" data-product ="{{$lims_product_list[4+$i*5]->code . ' (' . $lims_product_list[4+$i*5]->name . ')'}}">
                                        <p>{{$lims_product_list[4+$i*5]->name}}</p>
                                        <span>{{$lims_product_list[4+$i*5]->code}}</span>
                                    </td>
                                    @else
                                    <td style="border:none;"></td>
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
              </div>
            </div>
            <!-- product edit modal 
            <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="modal_header" class="modal-title"></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="row modal-element">
                                    <div class="col-md-4 form-group">
                                        <label>{{trans('file.Quantity')}}</label>
                                        <input type="text" name="edit_qty" class="form-control numkey">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{trans('file.Unit Discount')}}</label>
                                        <input type="text" name="edit_discount" class="form-control numkey">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{trans('file.Unit Price')}}</label>
                                        <input type="text" name="edit_unit_price" class="form-control numkey" step="any">
                                    </div>
                                    <?php
                                        $tax_name_all[] = 'No Tax';
                                        $tax_rate_all[] = 0;
                                        foreach($lims_tax_list as $tax) {
                                            $tax_name_all[] = $tax->name;
                                            $tax_rate_all[] = $tax->rate;
                                        }
                                    ?>
                                    <div class="col-md-4 form-group">
                                        <label>{{trans('file.Tax Rate')}}</label>
                                        <select name="edit_tax_rate" class="form-control selectpicker">
                                            @foreach($tax_name_all as $key => $name)
                                            <option value="{{$key}}">{{$name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="edit_unit" class="col-md-4 form-group">
                                        <label>{{trans('file.Product Unit')}}</label>
                                        <select name="edit_unit" class="form-control selectpicker">
                                        </select>
                                    </div>
                                </div>
                                <button type="button" name="update_btn" class="btn btn-primary">{{trans('file.update')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- add customer modal -->
            <div id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]) !!}
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Customer')}}</h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="form-group" style="display:none;">
                            <label>{{trans('file.Customer Group')}} *</strong> </label>
                            <select required class="form-control selectpicker" name="customer_group_id">
                                @foreach($lims_customer_group_all as $customer_group)
                                    <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.name')}} *</strong> </label>
                            <input type="text" name="customer_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.Email')}}</label>
                            <input type="text" name="email" placeholder="example@example.com" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{trans('file.Phone Number')}} *</label>
                            <input type="text" name="phone_number" required class="form-control">
                        </div>
                        <div class="form-group" style="display:none;">
                            <label>{{trans('file.Address')}} *</label>
                            <input type="text" name="address" value="addis" required class="form-control">
                        </div>
                        <div class="form-group" style="display:none;">
                            <label>{{trans('file.City')}} *</label>
                            <input type="text" name="city" value="addis" required class="form-control">
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="pos" value="1">
                          <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                    {{ Form::close() }}
                  </div>
                </div>
            </div>
            <!-- recent transaction modal -->
            <div id="recentTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Recent Transaction')}} <div class="badge badge-primary">{{trans('file.latest')}} 10</div></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" href="#sale-latest" role="tab" data-toggle="tab">{{trans('file.Sale')}}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#draft-latest" role="tab" data-toggle="tab">{{trans('file.Draft')}}</a>
                          </li>
                        </ul>
                        <div class="tab-content">
                          <div role="tabpanel" class="tab-pane show active" id="sale-latest">
                              <div class="table-responsive">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>{{trans('file.date')}}</th>
                                      <th>{{trans('file.reference')}}</th>
                                      <th>{{trans('file.customer')}}</th>
                                      <th>{{trans('file.grand total')}}</th>
                                      <th>{{trans('file.action')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($recent_sale as $sale)
                                    <?php $customer = DB::table('customers')->find($sale->customer_id); ?>
                                    <tr>
                                      <td>{{date('d-m-Y', strtotime($sale->created_at))}}</td>
                                      <td>{{$sale->reference_no}}</td>
                                      <td>{{$customer->name}}</td>
                                      <td>{{$sale->grand_total}}</td>
                                      <td>
                                        <div class="btn-group">
                                            @if(in_array("sales-edit", $all_permission))
                                            <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-success btn-sm" title="Edit"><i class="dripicons-document-edit"></i></a>&nbsp;
                                            @endif
                                            @if(in_array("sales-delete", $all_permission))
                                            {{ Form::open(['route' => ['sales.destroy', $sale->id], 'method' => 'DELETE'] ) }}
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete()" title="Delete"><i class="dripicons-trash"></i></button>
                                            {{ Form::close() }}
                                            @endif
                                        </div>
                                      </td>
                                    </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                          </div>
                          <div role="tabpanel" class="tab-pane fade" id="draft-latest">
                              <div class="table-responsive">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>{{trans('file.date')}}</th>
                                      <th>{{trans('file.reference')}}</th>
                                      <th>{{trans('file.customer')}}</th>
                                      <th>{{trans('file.grand total')}}</th>
                                      <th>{{trans('file.action')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($recent_draft as $draft)
                                    <?php $customer = DB::table('customers')->find($draft->customer_id); ?>
                                    <tr>
                                      <td>{{date('d-m-Y', strtotime($draft->created_at))}}</td>
                                      <td>{{$draft->reference_no}}</td>
                                      <td>{{$customer->name}}</td>
                                      <td>{{$draft->grand_total}}</td>
                                      <td>
                                        <div class="btn-group">
                                            @if(in_array("sales-edit", $all_permission))
                                            <a href="{{url('sales/'.$draft->id.'/create') }}" class="btn btn-success btn-sm" title="Edit"><i class="dripicons-document-edit"></i></a>&nbsp;
                                            @endif
                                            @if(in_array("sales-delete", $all_permission))
                                            {{ Form::open(['route' => ['sales.destroy', $draft->id], 'method' => 'DELETE'] ) }}
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete()" title="Delete"><i class="dripicons-trash"></i></button>
                                            {{ Form::close() }}
                                            @endif
                                        </div>
                                      </td>
                                    </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- today sale modal -->
            <div id="today-sale-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Today Sale')}}</h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p>{{trans('file.Please review the transaction and payments.')}}</p>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                          <td>{{trans('file.Total Sale Amount')}}:</td>
                                          <td class="total_sale_amount text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Cash Payment')}}:</td>
                                          <td class="cash_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Credit Card Payment')}}:</td>
                                          <td class="credit_card_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Cheque Payment')}}:</td>
                                          <td class="cheque_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Gift Card Payment')}}:</td>
                                          <td class="gift_card_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Deposit Payment')}}:</td>
                                          <td class="deposit_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Paypal Payment')}}:</td>
                                          <td class="paypal_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Total Payment')}}:</td>
                                          <td class="total_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Total Sale Return')}}:</td>
                                          <td class="total_sale_return text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Total Expense')}}:</td>
                                          <td class="total_expense text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><strong>{{trans('file.Total Cash')}}:</strong></td>
                                          <td class="total_cash text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- today profit modal -->
            <div id="today-profit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Today Profit')}}</h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <select required name="warehouseId" class="form-control">
                                    <option value="0">{{trans('file.All Warehouse')}}</option>
                                    @foreach($lims_warehouse_list as $warehouse)
                                    <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                          <td>{{trans('file.Product Revenue')}}:</td>
                                          <td class="product_revenue text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Product Cost')}}:</td>
                                          <td class="product_cost text-right"></td>
                                        </tr>
                                        <tr>
                                          <td>{{trans('file.Expense')}}:</td>
                                          <td class="expense_amount text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><strong>{{trans('file.Profit')}}:</strong></td>
                                          <td class="profit text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection


@push('scripts')
<script type="text/javascript">

    $("ul#sale").siblings('a').attr('aria-expanded','true');
    $("ul#sale").addClass("show");
    $("ul#sale #sale-pos-menu").addClass("active");

    var public_key = <?php echo json_encode($lims_pos_setting_data->stripe_public_key) ?>;
    var alert_product = <?php echo json_encode($alert_product) ?>;
    var currency = <?php echo json_encode($currency) ?>;
    var valid;

// array data depend on warehouse
var lims_product_array = [];
var product_code = [];
var product_name = [];
var vehicle_chassis = [];
var vehicle_engine = [];
var product_qty = [];
var product_type = [];
var product_id = [];
var product_list = [];
var qty_list = [];

// array data with selection
var product_price = [];
var product_unit = [];
var sell_unit = [];
var product_discount = [];
var tax_rate = [];
var tax_name = [];
var tax_method = [];
var unit_name = [];
var unit_operator = [];
var unit_operation_value = [];
var is_imei = [];
var gift_card_amount = [];
var gift_card_expense = [];

// temporary array
var temp_unit_name = [];
var temp_unit_operator = [];
var temp_unit_operation_value = [];



var product_row_number = <?php echo json_encode($lims_pos_setting_data->product_number) ?>;
var rowindex;
var customer_group_rate;
var row_product_price;
var pos;
var keyboard_active = <?php echo json_encode($keybord_active); ?>;
var role_id = <?php echo json_encode(\Auth::user()->role_id) ?>;
var warehouse_id = <?php echo json_encode(\Auth::user()->warehouse_id) ?>;
var biller_id = <?php echo json_encode(\Auth::user()->biller_id) ?>;
var currency = <?php echo json_encode($currency) ?>;

var localStorageQty = [];
var localStorageProductId = [];
var localStorageProductDiscount = [];
var localStorageTaxRate = [];
var localStorageNetUnitPrice = [];
var localStorageTaxValue = [];
var localStorageTaxName = [];
var localStorageTaxMethod = [];
var localStorageSubTotalUnit = [];
var localStorageProductUnit = [];
var localStorageSellUnit = [];
var localStorageSubTotal = [];
var localStorageProductCode = [];
var localStorageSaleUnit = [];
var localStorageTempUnitName = [];
var localStorageSaleUnitOperator = [];
var localStorageSaleUnitOperationValue = [];

$("#reference-no").val(getSavedValue("reference-no"));
$("#order-discount").val(getSavedValue("order-discount"));
$("#order-discount-val").val(getSavedValue("order-discount-val"));
$("#order-discount-type").val(getSavedValue("order-discount-type"));
$("#order-tax-rate-select").val(getSavedValue("order-tax-rate-select"));

$('#lims_productcodeSearch').val("");
function saveValue(e) {
    var id = e.id;  // get the sender's id to save it.
    var val = e.value; // get the value.
    localStorage.setItem(id, val);// Every time user writing something, the localStorage's value will override.
}
//get the saved value function - return the value of "v" from localStorage.
function getSavedValue  (v) {
    if (!localStorage.getItem(v)) {
        return "";// You can change this to your defualt value.
    }
    return localStorage.getItem(v);
}


$('.selectpicker').selectpicker({
  style: 'btn-link',
});

if(keyboard_active==1){

    $("input.numkey:text").keyboard({
        usePreview: false,
        layout: 'custom',
        display: {
        'accept'  : '&#10004;',
        'cancel'  : '&#10006;'
        },
        customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']
        },
        restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
        preventPaste : true,  // prevent ctrl-v and right click
        autoAccept : true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active'
        },
    });

    $('input[type="text"]').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')
              }
    });

    $('textarea').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')
              }
    }); 

    $('#lims_productcodeSearch').keyboard().autocomplete().addAutocomplete({
        // add autocomplete window positioning
        // options here (using position utility)
        position: {
          of: '#lims_productcodeSearch',
          my: 'top+18px',
          at: 'center',
          collision: 'flip'
        }
    });
}

  $("li#notification-icon").on("click", function (argument) {
      $.get('notifications/mark-as-read', function(data) {
          $("span.notification-number").text(alert_product);
      });
  });

  $("#register-details-btn").on("click", function (e) {
      e.preventDefault();
      $.ajax({
          url: 'cash-register/showDetails/'+warehouse_id,
          type: "GET",
          success:function(data) {
              $('#register-details-modal #cash_in_hand').text(data['cash_in_hand']);
              $('#register-details-modal #total_sale_amount').text(data['total_sale_amount']);
              $('#register-details-modal #total_payment').text(data['total_payment']);
              $('#register-details-modal #cash_payment').text(data['cash_payment']);
              $('#register-details-modal #credit_card_payment').text(data['credit_card_payment']);
              $('#register-details-modal #cheque_payment').text(data['cheque_payment']);
              $('#register-details-modal #gift_card_payment').text(data['gift_card_payment']);
              $('#register-details-modal #deposit_payment').text(data['deposit_payment']);
              $('#register-details-modal #paypal_payment').text(data['paypal_payment']);
              $('#register-details-modal #total_sale_return').text(data['total_sale_return']);
              $('#register-details-modal #total_expense').text(data['total_expense']);
              $('#register-details-modal #total_cash').text(data['total_cash']);
              $('#register-details-modal input[name=cash_register_id]').val(data['id']);
          }
      });
      $('#register-details-modal').modal('show');
  });

  $("#today-sale-btn").on("click", function (e) {
      e.preventDefault();
      $.ajax({
          url: 'sales/today-sale/',
          type: "GET",
          success:function(data) {
              $('#today-sale-modal .total_sale_amount').text(data['total_sale_amount']);
              $('#today-sale-modal .total_payment').text(data['total_payment']);
              $('#today-sale-modal .cash_payment').text(data['cash_payment']);
              $('#today-sale-modal .credit_card_payment').text(data['credit_card_payment']);
              $('#today-sale-modal .cheque_payment').text(data['cheque_payment']);
              $('#today-sale-modal .gift_card_payment').text(data['gift_card_payment']);
              $('#today-sale-modal .deposit_payment').text(data['deposit_payment']);
              $('#today-sale-modal .paypal_payment').text(data['paypal_payment']);
              $('#today-sale-modal .total_sale_return').text(data['total_sale_return']);
              $('#today-sale-modal .total_expense').text(data['total_expense']);
              $('#today-sale-modal .total_cash').text(data['total_cash']);
          }
      });
      $('#today-sale-modal').modal('show');
  });

  $("#today-profit-btn").on("click", function (e) {
      e.preventDefault();
      calculateTodayProfit(0);
  });

  $("#today-profit-modal select[name=warehouseId]").on("change", function() {
      calculateTodayProfit($(this).val());
  });

  function calculateTodayProfit(warehouse_id) {
      $.ajax({
            url: 'sales/today-profit/' + warehouse_id,
            type: "GET",
            success:function(data) {
                $('#today-profit-modal .product_revenue').text(data['product_revenue']);
                $('#today-profit-modal .product_cost').text(data['product_cost']);
                $('#today-profit-modal .expense_amount').text(data['expense_amount']);
                $('#today-profit-modal .profit').text(data['profit']);
            }
        });
      $('#today-profit-modal').modal('show');
  }
  if(getSavedValue("customer_id")) {
    $('select[name=customer_id]').val(getSavedValue("customer_id"));
  }
    if(role_id > 2){
    //$('#biller_id').addClass('d-none');
    $('#warehouse_id').addClass('d-none');
    $('select[name=warehouse_id]').val(warehouse_id);
    }
    else {
        if(getSavedValue("warehouse_id")){
        warehouse_id = getSavedValue("warehouse_id");
        }
        else {
        warehouse_id = $("input[name='warehouse_id_hidden']").val();
        }

        $('select[name=warehouse_id]').val(warehouse_id);
    }
        var kasuabe = getSavedValue("kasu_type");
        $('select[name=kasu_type]').val(kasuabe);
        $('input[name=kasu_id]').val(kasuabe);
        $('.selectpicker').selectpicker('refresh');


   


    var id = $("#warehouse_id").val();

    var kasukooo =  $('input[name=kasu_id]').val();
    if(kasukooo == 2)
    { 
        $('#sell-unit12').css('display','none');
        $('#vehicle-engine-no').css('display','block');
        $.get('sales/getvehicle/' + warehouse_id, function(data) {
        lims_product_array = [];
            product_code = data[0];
            product_name = data[1];
            product_qty = data[2];
            product_id = data[3];
            product_unit = data[4];
            sell_unit = data[4];
            product_price = data[5];
            vehicle_chassis = data[6];
            vehicle_engine = data[7];
            $.each(product_code, function(index) {      
                    lims_product_array.push(product_code[index] + ' (' + vehicle_engine[index] + ' , CHA.NO: '+vehicle_chassis[index]+ ')');
            });
            
        });
    }
    else
    {
        $('#sell-unit12').css('display','block');
        $('#vehicle-engine-no').css('display','none');
        $.get('sales/getproduct/' + id, function(data) {
        lims_product_array = [];
            product_code = data[0];
            product_name = data[1];
            product_qty = data[2];
            product_id = data[3];
            product_unit = data[4];
            sell_unit = data[4];
            product_price = data[5];
            product_piece_in_carton = data[6];

            $.each(product_code, function(index) {
            
                    lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
            });
        });
    }
   



if(keyboard_active==1){ 
    $('#lims_productcodeSearch').bind('keyboardChange', function (e, keyboard, el) {
        var customer_id = $('#customer_id').val();
        var kas_id = $('select[name="kasu_type"]').val();
        var warehouse_id = $('select[name="warehouse_id"]').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){ 
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }
        else if(!kas_id || kas_id == 0){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Product Type!');
        }
    });
}
else{
    $('#lims_productcodeSearch').on('input', function(){
        var customer_id = $('#customer_id').val();
        var warehouse_id = $('#warehouse_id').val();
        var ab_id = $('#kasu_type').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }
        else if(!ab_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Product Type!');
        }

    });
} 

$("#print-btn").on("click", function(){
      var divToPrint=document.getElementById('sale-details');
      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},10);
});
var check_id =  $('input[name=kasu_id]').val();
$('select[name=kasu_type]').val(check_id);
$('.selectpicker').selectpicker('refresh');

if(check_id == 2)
{
    $('#product-both').hide();
    $('#vehicle-both').show();
    $('#product-table').hide();
    $('#vehicle-table').show();
    $('#vehicles-filter').css('color','black');  
    $('#products-filter').css('color','white'); 
}
else
{
    $('#product-both').show();
    $('#vehicle-both').hide();
    $('#product-table').show();
    $('#vehicle-table').hide();
    $('#products-filter').css('color','black'); 
    $('#vehicles-filter').css('color','white');  
}

$('body').on('click', function(e){
    $('.filter-window').hide('slide', {direction: 'right'}, 'fast');
});
$('#products-filter').on('click', function(e){
$("table.order-list tbody").html('');
$('#sell-unit12').css('display','block');
$('#vehicle-engine-no').css('display','none');
$('#products-filter').css('color','black');  
$('#vehicles-filter').css('color','white');  
$('input[name=kasu_id]').val(1);
$('select[name=kasu_type]').val(1);
$('.selectpicker').selectpicker('refresh');
$('#product-both').show();
$('#vehicle-both').hide();
$('#product-table').show();
$('#vehicle-table').hide();
CheckButton();
});
$('#vehicles-filter').on('click', function(e){
$('#sell-unit12').css('display','none');
$('#vehicle-engine-no').css('display','block');
$("table.order-list tbody").html('');
$('input[name=kasu_id]').val(2);
$('select[name=kasu_type]').val(2);
$('.selectpicker').selectpicker('refresh');
$('#products-filter').css('color','white');  
$('#vehicles-filter').css('color','black');  
$('#product-both').hide();
$('#product-table').hide();
$('#vehicle-both').show();
$('#vehicle-table').show();
CheckButton();
});

$('#category-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.brand').hide();
    $('.vehicle-brand').hide();
    $('.vehicle-category').hide();
    $('.category').show();
});
$('#vehiclecategory-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.category').hide();
    $('.brand').hide();
    $('.vehicle-brand').hide();
    $('.vehicle-category').show();
});
$('#vehiclebrand-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.category').hide();
    $('.brand').hide();
    $('.vehicle-category').hide();
    $('.vehicle-brand').show();

});

$('.category-img').on('click', function(){
  var kas=  $('input[name="kasu_id"]').val();
  var kas1=$('select[name="kasu_type"]').val();
    var category_id = $(this).data('category');
    var brand_id = 0;
     if(kas == 2 || kas1==2)
     {
        $(".table-container").children().remove();
        $.get('sales/getvehicle/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
    });
     }
     else
     {
        $(".table-container").children().remove();
    $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
    }); 
     }
   
});

$('#brand-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.category').hide();
    $('.vehicle-brand').hide();
    $('.vehicle-category').hide();
    $('.brand').show();
});

$('.brand-img').on('click', function(){
    var kas=  $('input[name="kasu_id"]').val();
  var kas1=$('select[name="kasu_type"]').val();
    var brand_id = $(this).data('brand');
    var category_id = 0;
    if(kas == 2 || kas1==2)
     {
        $(".table-container").children().remove();
    $.get('sales/getvehicle/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
       // console.log(data['chassis']);
       // console.log(vehicle_chassis);
    });
     }
     else
     {
        $(".table-container").children().remove();
    $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
    });
     }
   
});


function populateProduct(data) {
var kasu =   $('input[name="kasu_id"]').val();
var kasua =   $('select[name="kasu_type"]').val();
    if(kasu == 2 || kasua == 2)
    {    //lims_product_array.push(product_code[index] + ' (' + vehicle_engine[index] + ' , CHA.NO : '+vehicle_chassis[index]+ ')');
        var tableData = '<table id="vehicle-table" class="table no-shadow vehicle-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';
            if (Object.keys(data).length != 0) {
            $.each(data['chassis'], function(index) { 
            
                var product_info = data['code'][index]+ ' (' +data['engine'][index] +' , CHA.NO: '+data['chassis'][index] + ')';
                if(index % 5 == 0 && index != 0)
                    tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><p>'+data['name'][index]+'</p><span>'+data['chassis'][index]+'</span></td>';
                else
                    tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><p>'+data['name'][index]+'</p><span>'+data['chassis'][index]+'</span></td>';
            });

            if(data['engine'].length % 5){
                var number = 5 - (data['engine'].length % 5);
                while(number > 0)
                { //kasmmm
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }

            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#vehicle-table').DataTable( {
            "order": [],
            'pageLength': product_row_number,
            'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-left"></i>',
                    'next': '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: 'tp'
            });
            $('table.vehicle-list').hide();
            $('table.vehicle-list').show(500);
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    }
    else
    {
        var tableData = '<table id="product-table" class="table no-shadow product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';
            if (Object.keys(data).length != 0) {
            $.each(data['name'], function(index) {
                var product_info = data['code'][index]+' (' + data['name'][index] + ')';
                if(index % 5 == 0 && index != 0) 
                    tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><p>'+data['name'][index]+'</p><span>'+data['code'][index]+'</span></td>';
                else
                    tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><p>'+data['name'][index]+'</p><span>'+data['code'][index]+'</span></td>';
            });

            if(data['name'].length % 5){
                var number = 5 - (data['name'].length % 5);
                while(number > 0)
                {
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }

            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#product-table').DataTable( {
            "order": [],
            'pageLength': product_row_number,
            'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-left"></i>',
                    'next': '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: 'tp'
            });
            $('table.product-list').hide();
            $('table.product-list').show(500);
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    }
  

    
}


$('select[name="warehouse_id"]').on('change', function() {
    saveValue(this);
    warehouse_id = $(this).val();
    var kas_id=$('select[name="kasu_type"]').val();
    if(!kas_id)
    {
        alert('Please select Product type ');
    }
    else
    {
    if(kas_id == 2)
    {
    $('#sell-unit12').css('display','none');
    $('#vehicle-engine-no').css('display','block');
    $.get('sales/getvehicle/' + warehouse_id, function(data) {
        lims_product_array = [];
            product_code = data[0];
            product_name = data[1];
            product_qty = data[2];
            product_id = data[3];
            product_unit = data[4];
            sell_unit = data[4];
            product_price = data[5];
            vehicle_chassis = data[6];
            vehicle_engine = data[7];
            $.each(product_code, function(index) {      
                    lims_product_array.push(product_code[index] + ' (' + vehicle_engine[index] + ' , CHA.NO : '+vehicle_chassis[index]+ ')');
            });
        
    });
    }
    else
    {
        $('#sell-unit12').css('display','block');
        $('#vehicle-engine-no').css('display','none');
        warehouse_id = $(this).val();
        $.get('sales/getproduct/' + warehouse_id, function(data) {
        lims_product_array = [];
        product_code = data[0];
        product_name = data[1];
        product_qty = data[2];
        product_id = data[3];
        product_unit = data[4];
        sell_unit = data[4];
        product_price = data[5];
        product_piece_in_carton = data[6];
            $.each(product_code, function(index) {
          
          lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
         });

       
    });
    }
     }


});



function CheckButton()
{
   var  ware_id = $('select[name="warehouse_id"]').val();
   var kas_id = $('input[name="kasu_id"]').val();
    if(kas_id == 2)
    {
    $('#sell-unit12').css('display','none');
    $('#vehicle-engine-no').css('display','block');
    $.get('sales/getvehicle/' + ware_id, function(data) {
        lims_product_array = [];
            product_code = data[0];
            product_name = data[1];
            product_qty = data[2];
            product_id = data[3];
            product_unit = data[4];
            sell_unit = data[4];
            product_price = data[5];
            vehicle_chassis = data[6];
            vehicle_engine = data[7];
            $.each(product_code, function(index) {      
                    lims_product_array.push(product_code[index] + ' (' + vehicle_engine[index] + ' , CHA.NO : '+vehicle_chassis[index]+ ')');
            });
        
    });
    }
    else
    {
        $('#sell-unit12').css('display','block');
        $('#vehicle-engine-no').css('display','none');
        $.get('sales/getproduct/' + ware_id, function(data) {
        lims_product_array = [];
        product_code = data[0];
        product_name = data[1];
        product_qty = data[2];
        product_id = data[3];
        product_unit = data[4];
        sell_unit = data[4];
        product_price = data[5];
        product_piece_in_carton = data[6];
            $.each(product_code, function(index) {
          
          lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
         });

       
    });
    var lims_productcodeSearch = $('#lims_productcodeSearch');

    lims_productcodeSearch.autocomplete({
        source: function(request, response) {
            var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
            response($.grep(lims_product_array, function(item) {
                return matcher.test(item);
            }));
        },
        response: function(event, ui) {
            if (ui.content.length == 1) {
                var data = ui.content[0].value;
                $(this).autocomplete( "close" );
                productSearch(data);
            }
            else if(ui.content.length == 0 && $('#lims_productcodeSearch').val().length == 13) {
            productSearch($('#lims_productcodeSearch').val()+'|'+1);
            }
        },
        select: function(event, ui) {
            var data = ui.item.value;
            productSearch(data);
        },
    });
    }
     

}
var lims_productcodeSearch = $('#lims_productcodeSearch');

lims_productcodeSearch.autocomplete({
    source: function(request, response) {
        var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
        response($.grep(lims_product_array, function(item) {
            return matcher.test(item);
        }));
    },
    response: function(event, ui) {
        if (ui.content.length == 1) {
            var data = ui.content[0].value;
            $(this).autocomplete( "close" );
            productSearch(data);
        }
        else if(ui.content.length == 0 && $('#lims_productcodeSearch').val().length == 13) {
          productSearch($('#lims_productcodeSearch').val()+'|'+1);
        }
    },
    select: function(event, ui) {
        var data = ui.item.value;
        productSearch(data);
    },
});

$('select[name="kasu_type"]').on("change", function() {
    saveValue(this);
    $("table.order-list tbody").html('');
    var id = $(this).val();
    $('input[name="kasu_id"]').val(id);
    var warehouse_id=$('select[name="warehouse_id"]').val();
    if(!warehouse_id)
    {
        alert('Please select Warehouse ');
    }
    else
    {
        if(id == 2)
        {
        $('#product-both').hide();
        $('#vehicle-both').show();
        $('#product-table').hide();
        $('#vehicle-table').show();
        $('#vehicles-filter').css('color','black');  
        $('#products-filter').css('color','white'); 
        $('#sell-unit12').css('display','none');
        $('#vehicle-engine-no').css('display','block');
        $.get('sales/getvehicle/' + warehouse_id, function(data) {
            lims_product_array = [];
                product_code = data[0];
                product_name = data[1];
                product_qty = data[2];
                product_id = data[3];
                product_unit = data[4];
                sell_unit = data[4];
                product_price = data[5];
                vehicle_chassis = data[6];
                vehicle_engine = data[7];
                $.each(product_code, function(index) {      
                        lims_product_array.push(product_code[index]  + ' (' + vehicle_engine[index] + ' , CHA.NO: '+vehicle_chassis[index]+ ')');
                });
            
        });
        }
        else
        {
            $('#product-both').show();
            $('#vehicle-both').hide();
            $('#product-table').show();
            $('#vehicle-table').hide();
            $('#vehicles-filter').css('color','white');  
            $('#products-filter').css('color','black');
            $('#sell-unit12').css('display','block');
            $('#vehicle-engine-no').css('display','none');
            warehouse_id = $('select[name="warehouse_id"]').val();
        $.get('sales/getproduct/' + warehouse_id, function(data) {
            lims_product_array = [];
            product_code = data[0];
            product_name = data[1];
            product_qty = data[2];
            product_id = data[3];
            product_unit = data[4];
            sell_unit = data[4];
            product_price = data[5];
            product_piece_in_carton = data[6];

            $.each(product_code, function(index) {
            
                    lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
            });
            
            });
        }
    }
});

var lims_productcodeSearch = $('#lims_productcodeSearch');

lims_productcodeSearch.autocomplete({
    source: function(request, response) {
        var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
        response($.grep(lims_product_array, function(item) {
            return matcher.test(item);
        }));
    },
    response: function(event, ui) {
        if (ui.content.length == 1) {
            var data = ui.content[0].value;
            $(this).autocomplete( "close" );
            productSearch(data);
        }
        else if(ui.content.length == 0 && $('#lims_productcodeSearch').val().length == 13) {
          productSearch($('#lims_productcodeSearch').val()+'|'+1);
        }
    },
    select: function(event, ui) {
        var data = ui.item.value;
        productSearch(data);
    },
});

$('#myTable').keyboard({
    accepted : function(event, keyboard, el) {
      checkQuantity(el.value, true);
  }
});

$("#myTable").on('click', '.plus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
    if(!qty)
      qty = 1;
    else
      qty = parseFloat(qty) + 1;
    checkDiscount(qty, true);
});

$("#myTable").on('click', '.minus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) - 1;
    if (qty > 0) {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    } else {
        qty = 1;
    }
    checkDiscount(qty, true);
});
$("#myTable").on('input', '.qty', function() {
    rowindex = $(this).closest('tr').index();
    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
    if(!qty || qty == '') {
      $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(1);
      alert("Quantity can't be less than 0");
    }
    checkDiscount($(this).val(), true);
});
$("#myTable").on("click", ".sell-unit1", function () {
    var sell_unit=$(this).val();
    rowindex = $(this).closest('tr').index();
    var spare = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-unit1').val();
    var pprice = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text();
    var ptotal = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal').val();
    var pqty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
       if(sell_unit=='Piece' && spare=='Piece')
       {
        alert('Sorry, the product is not avaliable in another unit ! ');
       }
       else if(spare=='Dozen' && sell_unit=='Dozen')
       {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val('Piece');
        var kasu=pprice/12;
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(parseFloat(kasu));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(parseFloat(kasu * pqty));
        calculateTotal();
       }
       else if(spare=='Dozen' && sell_unit=='Piece')
       {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val('Dozen');
        var kasu=pprice*12;
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(parseFloat(kasu));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(parseFloat(kasu * pqty));
        calculateTotal();
       }
       else if(spare=='Carton' && sell_unit=='Carton')
       {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val('Piece');
       var ab= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.piece-in-carton').val();
        var kasu=pprice/ab;
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(parseFloat(kasu));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(parseFloat(kasu * pqty));
        calculateTotal();
       }
       else if(spare=='Carton' && sell_unit=='Piece')
       {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val('Carton');
       var abaa= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.piece-in-carton').val();
        var kasu=parseFloat(pprice * abaa);
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(parseFloat(kasu));
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(parseFloat(kasu * pqty));
        calculateTotal();
       }
       else
       {
        console.log(spare,sell_unit);
       }
       
    
});

//Change quantity

$("#myTable").on('click', '.qty', function() {
    rowindex = $(this).closest('tr').index();
});

$(document).on('click', '.sound-btn', function() {
    var audio = $("#mysoundclip1")[0];
    audio.play();
});

$(document).on('click', '.product-img', function() {
    var customer_id = $('#customer_id').val();
    var type_id = $('select[name="kasu_type"]').val();
    var warehouse_id = $('select[name="warehouse_id"]').val();
    if(customer_id == "")
    {
        alert('Please select Customer!');
        $('#customer_id').css('border-color', 'red');
    }
       
    else if(warehouse_id == "")
    {
        alert('Please select Warehouse!');
        $('select[name="warehouse_id"]').css('border-color', 'red');
    }
       
    else if(type_id == "")
    {
        alert('Please select Product Type!');
        $('#kasu_type').css('border-color', 'red');
    }

    else{
        var data = $(this).data('product');  
        product_info = data.split(" ");
        //pos = product_code.indexOf(product_info[0]);
        var kas_id=$('input[name="kasu_id"]').val();
        if(kas_id == 2)
        {
            pos = vehicle_chassis.indexOf(product_info[4].slice(0,-1)); 
        }
        if(kas_id == 1)
        {
            pos = product_code.indexOf(product_info[0]);
        }
       if(pos < 0)
          alert('Product is not avaialable in the selected warehouse');
        else{
            productSearch(data);
       }
       //kasusali
    }
});
//Delete product
$("table.order-list tbody").on("click", ".ibtnDel", function(event) {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    rowindex = $(this).closest('tr').index();
    product_price.splice(rowindex, 1);
    product_unit.splice(rowindex, 1);
    sell_unit.splice(rowindex, 1);
    product_discount.splice(rowindex, 1);
    tax_rate.splice(rowindex, 1);
    tax_name.splice(rowindex, 1);
    tax_method.splice(rowindex, 1);
    unit_name.splice(rowindex, 1);
    unit_operator.splice(rowindex, 1);
    unit_operation_value.splice(rowindex, 1);

    localStorageProductId.splice(rowindex, 1);
    localStorageQty.splice(rowindex, 1);
    localStorageSaleUnit.splice(rowindex, 1);
    localStorageProductDiscount.splice(rowindex, 1);
    localStorageTaxRate.splice(rowindex, 1);
    localStorageNetUnitPrice.splice(rowindex, 1);
    localStorageProductUnit.splice(rowindex, 1);
    localStorageSellUnit.splice(rowindex, 1);
    localStorageTaxValue.splice(rowindex, 1);
    localStorageSubTotalUnit.splice(rowindex, 1);
    localStorageSubTotal.splice(rowindex, 1);
    localStorageProductCode.splice(rowindex, 1);

    localStorageTaxName.splice(rowindex, 1);
    localStorageTaxMethod.splice(rowindex, 1);
    localStorageTempUnitName.splice(rowindex, 1);
    localStorageSaleUnitOperator.splice(rowindex, 1);
    localStorageSaleUnitOperationValue.splice(rowindex, 1);

    localStorage.setItem("localStorageProductId", localStorageProductId);
    localStorage.setItem("localStorageQty", localStorageQty);
    localStorage.setItem("localStorageSaleUnit", localStorageSaleUnit);
    localStorage.setItem("localStorageProductCode", localStorageProductCode);
    localStorage.setItem("localStorageProductDiscount", localStorageProductDiscount);
    localStorage.setItem("localStorageTaxRate", localStorageTaxRate);
    localStorage.setItem("localStorageTaxName", localStorageTaxName);
    localStorage.setItem("localStorageTaxMethod", localStorageTaxMethod);
    localStorage.setItem("localStorageTempUnitName", localStorageTempUnitName);
    localStorage.setItem("localStorageSaleUnitOperator", localStorageSaleUnitOperator);
    localStorage.setItem("localStorageSaleUnitOperationValue", localStorageSaleUnitOperationValue);
    localStorage.setItem("localStorageNetUnitPrice", localStorageNetUnitPrice);
    localStorage.setItem("localStorageProductUnit", localStorageProductUnit);
    localStorage.setItem("localStorageSellUnit", localStorageSellUnit);
    localStorage.setItem("localStorageTaxValue", localStorageTaxValue);
    localStorage.setItem("localStorageSubTotalUnit", localStorageSubTotalUnit);
    localStorage.setItem("localStorageSubTotal", localStorageSubTotal);
 
    $(this).closest("tr").remove();
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
    calculateTotal();
});

//Edit product
$("table.order-list").on("click", ".edit-product", function() {
    rowindex = $(this).closest('tr').index();
    edit();
});

//Update product


$('button[name="order_discount_btn"]').on("click", function() {
    calculateGrandTotal();
}); 

$('button[name="shipping_cost_btn"]').on("click", function() {
    calculateGrandTotal();
});

$('button[name="order_tax_btn"]').on("click", function() {
    calculateGrandTotal();
});



$(".payment-btn").on("click", function() {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber >= 0) {
        var audio = $("#mysoundclip2")[0];
    audio.play();
    $('#credit_div').hide();
    $('#bank_div').hide();
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    $('select[name="paid_by_id_select"]').val(1);
    $('.selectpicker').selectpicker('refresh');
    $('#add-payment').show();
     
    }
    else
    {
        alert("Please insert product to order table!");
        $('#add-payment').hide();
    }

});

$("#submit-btn").on("click", function() {
    $('.payment-form').submit();
});

$('select[name="paid_by_id_select"]').on("change", function() {
    var id = $(this).val();
    $(".payment-form").off("submit");
    if(id==2)
    {
        $('#bank_div').show();  
        $('#credit_div').hide();  
        $('input[name="paid_amount"]').val($("#grand-total").text());
        
    } 
    else if(id==3)
    {
        $('input[name="paid_amount"]').val(0.00);
        $('#bank_div').hide(); 
    }
    else if(id==4)
    {
        $('#bank_div').hide();  
        $('#credit_div').show(); 
        $('input[name="paid_amount"]').val($("#grand-total").text());
    }
    else 
    {
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('#credit_div').hide();
    $('#bank_div').hide();
    }
  
   
    
});


$('#add-payment input[name="paying_amount"]').on("input", function() {
    change($(this).val(), $('input[name="paid_amount"]').val());
});

$('input[name="credit_amount"]').on("input", function() {
    var recieved=parseFloat($('input[name="paying_amount"]').val());
    if( $(this).val() > parseFloat($('input[name="paying_amount"]').val()) ) {
        alert('Credit amount cannot be bigger than recieved amount');
        $(this).val('');
    }
    else if( $(this).val() > parseFloat($('#grand-total').text()) ){
        alert('Credit amount cannot be bigger than grand total');
        $(this).val('');
    }
    var kass= $(this).val();
   var paid=recieved-kass;
$('input[name="paid_amount"]').val(paid);

});

function change(paying_amount, paid_amount) {
    $("#change").text( parseFloat(paying_amount - paid_amount).toFixed(2) );
}

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    } 
    return false;
}

function productSearch(data) {
    var product_code;
    var kas_id=$('input[name="kasu_id"]').val();
    var product_info = data.split(" ");
    if(kas_id == 2)
    {
        product_code = product_info[4].slice(0,-1);
    }
    else
    {
        product_code = product_info[0];
    }
    var pre_qty = 0;
    if(kas_id == 2)
    {
        var engg = product_info[1];
        var vhcl_eng = engg.substring(1, engg.length);
        var vhcl_cha = product_info[4].slice(0,-1);
        $('#sell-unit12').css('display','none');
        $('#vehicle-engine-no').css('display','block');
        $('.sell-unit1').css('display','none');
        $(".product-code").each(function(i) {
        if ($(this).val() == product_code) {
            rowindex = i;
            pre_qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
        }
        });
        data += '?'+$('#customer_id').val()+'?'+(parseFloat(pre_qty) + 1);
    $.ajax({
        type: 'GET',
        url: 'sales/lims_product_search1',
        data: {
            "data": data,
            "engine": vhcl_eng,
            "chassis": vhcl_cha,
        },
        success: function(data) {
          //  console.log(pre_qty);
            var flag = 1;
            if (pre_qty > 0) {
                /*if(pre_qty)
                    var qty = parseFloat(pre_qty) + data[14];
                else*/
                    var qty = data[4];
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
                pos = product_code.indexOf(data[10]);  
                product_price[rowindex] = parseFloat(data[2]);
                var kas=  product_price[rowindex] ;
              
                flag = 0;
                checkQuantity(String(qty), true);
                flag = 0;
                localStorage.setItem("tbody-id", $("table.order-list tbody").html());
            }
            $("input[name='product_code_name']").val('');
            if(flag){
                addNewProduct(data);
            }
            else
            {
               // console.log("Salami");
            }
        }
    }); 

    }
    else
    {
        $('#sell-unit12').css('display','block');
        $('#vehicle-engine-no').css('display','none');
        $('.sell-unit1').css('display','block');
        $(".product-code").each(function(i) {
        if ($(this).val() == product_code) {
            rowindex = i;
            pre_qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
        }
         });
    data += '?'+$('#customer_id').val()+'?'+(parseFloat(pre_qty) + 1);
    $.ajax({
        type: 'GET',
        url: 'sales/lims_product_search',
        data: {
            data: data
        },
        success: function(data) {
           // console.log(pre_qty);
            var flag = 1;
            if (pre_qty > 0) {
                /*if(pre_qty)
                    var qty = parseFloat(pre_qty) + data[14];
                else*/
                    var qty = data[4];
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
                pos = product_code.indexOf(data[1]);  
                product_price[rowindex] = parseFloat(data[2]);
                var kas=  product_price[rowindex] ;
              
                flag = 0;
                checkQuantity(String(qty), true);
                flag = 0;
                localStorage.setItem("tbody-id", $("table.order-list tbody").html());
            }
            $("input[name='product_code_name']").val('');
            if(flag){
                addNewProduct(data);
            }
            else
            {
              //  console.log("Salami");
            }
        }
    }); 
        
    }
   

}

function addNewProduct(data){
    var kas_id=$('input[name="kasu_id"]').val();
    var newRow = $("<tr>");
    var cols = '';
    if(kas_id == 1)
    {
        temp_unit_name = (data[5]).split(',');
        pos = product_code.indexOf(data[1]);
    }
    else
    {
        pos = product_code.indexOf(data[10]);
    }
   
    if(kas_id == 1)
    {
        cols += '<td class="col-sm-2 product-title"><strong><span class="product-name" style="max-width:120px;">' + data[0] + '</span></strong>' + data[1] + '<p>In Stock: <span class="in-stock"></span></p></td>';
        cols += '<td class="col-sm-2"><div class="input-group"><input type="text" role="submit" class="form-control sell-unit1 btn btn-link"  name="sell-unit1[]"  value="'+data[5]+'" /></div>  </td>';
    }
    if(kas_id == 2)
    {
        cols += '<td class="col-sm-2 product-title"><strong><span class="product-name" style="max-width:120px;">' + data[0] + '</span></strong>' + data[10] + '<p>In Stock: <span class="in-stock"></span></p></td>';
        cols += '<td class="col-sm-2">'+data[11]+' </td>';
    }
  
    cols += '<td class="col-sm-2 product-price"></td>';
    cols += '<td class="col-sm-3"><div class="input-group"><span class="input-group-btn"><button type="button" class="btn btn-default minus"><span class="dripicons-minus"></span></button></span><input type="text" name="qty[]" class="form-control qty numkey input-number" step="any" value="'+data[4]+'" required><span class="input-group-btn"><button type="button" class="btn btn-default plus"><span class="dripicons-plus"></span></button></span></div></td>';
    cols += '<td class="col-sm-2 sub-total"></td>';
  
    cols += '<td class="col-sm-1"><button type="button" class="ibtnDel btn btn-danger btn-sm"><i class="dripicons-cross"></i></button></td>';
    if(kas_id == 1)
    {
        cols += '<input type="hidden" class="product-code" name="product_code[]" value="' + data[1] + '"/>';
    }
    else
    {
        cols += '<input type="hidden" class="product-code" name="product_code[]" value="' + data[10] + '"/>';
    }
    cols += '<input type="hidden" class="product-id" name="product_id[]" value="' + data[3] + '"/>';
    cols += '<input type="hidden" class="product_price" />';
    if(kas_id == 2)
    {
        cols += '<input type="hidden" class="sell-unit1" name="sell-unit1[]" value="'+data[5]+'"/>'; 
    }
    cols += '<input type="hidden" class="product-unit1" name="product-unit1[]" value="'+data[5]+'"/>';
    cols += '<input type="hidden" class="tax-rate" name="tax_rate[]" value="' + data[6] + '"/>';
    cols += '<input type="hidden" class="pro-pricr" name="pro_price[]" value="' + data[2] + '"/>';
    cols += '<input type="hidden" class="tax-name" value="'+data[7]+'" />';
    cols += '<input type="hidden" class="tax-method" value="'+data[8]+'" />';
    if(kas_id != 2)
    {
    cols += '<input type="hidden" class="piece-in-carton" value="'+data[9]+'" />';
    }
    if(kas_id == 2)
    {
        cols += '<input type="hidden" class="" name="vehicle_cha[]" value="' + data[10] + '"/>';
        cols += '<input type="hidden" class="" name="vehicle_eng[]" value="' + data[11] + '"/>';
    }
    cols += '<input type="hidden" class="subtotal-value" name="subtotal[]" />';

    newRow.append(cols);
    if(keyboard_active==1) {
        $("table.order-list tbody").prepend(newRow).find('.qty').keyboard({usePreview: false, layout: 'custom', display: { 'accept'  : '&#10004;', 'cancel'  : '&#10006;' }, customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']}, restrictInput : true, preventPaste : true, autoAccept : true, css: { container: 'center-block dropdown-menu', buttonDefault: 'btn btn-default', buttonHover: 'btn-primary',buttonAction: 'active'},});
    }
    else
        $("table.order-list tbody").prepend(newRow);

    rowindex = newRow.index();
    product_price.splice(rowindex, 0, parseFloat(data[2]));
    product_unit.splice(rowindex, 0, data[5]);
    sell_unit.splice(rowindex, 0, data[5]);
    product_discount.splice(rowindex, 0, '0.00');
    tax_rate.splice(rowindex, 0, parseFloat(data[7]));
    tax_name.splice(rowindex, 0, data[8]);
    tax_method.splice(rowindex, 0, data[9]);
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product_price').val(product_price[rowindex]);
   
    localStorageQty.splice(rowindex, 0, data[4]);
    localStorageProductId.splice(rowindex, 0, data[3]);
    localStorageProductCode.splice(rowindex, 0, data[1]);
    localStorageProductUnit.splice(rowindex, 0, data[5]);
    
    localStorageSaleUnit.splice(rowindex, 0, temp_unit_name[0]);
    localStorageProductDiscount.splice(rowindex, 0, product_discount[rowindex]);
    localStorageTaxRate.splice(rowindex, 0, tax_rate[rowindex].toFixed(2));
    localStorageTaxName.splice(rowindex, 0, data[8]);
    localStorageTaxMethod.splice(rowindex, 0, data[9]);
    localStorageTempUnitName.splice(rowindex, 0, data[5]);
    //put some dummy value
    localStorageNetUnitPrice.splice(rowindex, 0, '0.00');
    localStorageTaxValue.splice(rowindex, 0, '0.00');
    localStorageSubTotalUnit.splice(rowindex, 0, '0.00');
    localStorageSubTotal.splice(rowindex, 0, '0.00');

    localStorage.setItem("localStorageProductId", localStorageProductId);
    localStorage.setItem("localStorageSaleUnit", localStorageSaleUnit);
    localStorage.setItem("localStorageProductCode", localStorageProductCode);
    localStorage.setItem("localStorageTaxName", localStorageTaxName);
    localStorage.setItem("localStorageTaxMethod", localStorageTaxMethod);
    localStorage.setItem("localStorageTempUnitName", localStorageTempUnitName);
    localStorage.setItem("localStorageProductUnit", localStorageProductUnit);
    checkQuantity(data[4], true);
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
    if(data[13]) { 
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.edit-product').click();
    }
}

function edit(){
    $(".imei-section").remove();
    if(is_imei[rowindex]) {
        var imeiNumbers = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.imei-number').val();

        htmlText = '<div class="col-md-12 form-group imei-section"><label>IMEI or Serial Numbers</label><input type="text" name="imei_numbers" value="'+imeiNumbers+'" class="form-control imei_number" placeholder="Type imei or serial numbers and separate them by comma. Example:1001,2001" step="any"></div>';
        $("#editModal .modal-element").append(htmlText);
    }

    var row_product_name_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-name').text();
    $('#modal_header').text(row_product_name_code);

    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
    $('input[name="edit_qty"]').val(qty);

    $('input[name="edit_discount"]').val(parseFloat(product_discount[rowindex]).toFixed(2));

    var tax_name_all = <?php echo json_encode($tax_name_all) ?>;
    pos = tax_name_all.indexOf(tax_name[rowindex]);
    $('select[name="edit_tax_rate"]').val(pos);

    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    pos = product_code.indexOf(row_product_code);
    if(product_type[pos] == 'standard'){
      
        temp_unit_name = (unit_name[rowindex]).split(',');
        temp_unit_name.pop();
        temp_unit_operator = (unit_operator[rowindex]).split(',');
        temp_unit_operator.pop();
        temp_unit_operation_value = (unit_operation_value[rowindex]).split(',');
        temp_unit_operation_value.pop();
        $('select[name="edit_unit"]').empty();
        $.each(temp_unit_name, function(key, value) {
            $('select[name="edit_unit"]').append('<option value="' + key + '">' + value + '</option>');
        });
        $("#edit_unit").show();
    }
    else{
        row_product_price = product_price[rowindex];
        $("#edit_unit").hide();
    }
    $('input[name="edit_unit_price"]').val(row_product_price.toFixed(2));
    $('.selectpicker').selectpicker('refresh');
}

function checkDiscount(qty, flag) {
    var customer_id = $('#customer_id').val();
    var product_id = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .product-id').val();
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    checkQuantity(String(qty), flag);
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
} 

function checkQuantity(sale_qty, flag) {
    var kas_id=$('input[name="kasu_id"]').val();
    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    if(kas_id == 1)
    {
        pos = product_code.indexOf(row_product_code);
    }
    else
    {
        pos = vehicle_chassis.indexOf(row_product_code); 
    }

    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.in-stock').text(product_qty[pos]);
   // localStorageQty[rowindex] = sale_qty;
   // localStorage.setItem("localStorageQty", localStorageQty);
    var S_UNIT = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val();
    var P_UNIT = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-unit1').val();
    if(P_UNIT=="Dozen" && S_UNIT=="Piece")
     {
        total_qty = sale_qty/12;
        if(total_qty > parseFloat(product_qty[pos]))
        {
            alert('Quantity exceeds stock quantity!');
            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
         //   localStorageQty[rowindex] = sale_qty-1;
          //  localStorage.setItem("localStorageQty", localStorageQty);
            sale_qty= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
        }
     }
     else if(P_UNIT=="Dozen" && S_UNIT=="Dozen")
     {
        if(sale_qty > parseFloat(product_qty[pos]))
        {
            alert('Quantity exceeds stock quantity!');
            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
           // localStorageQty[rowindex] = sale_qty-1;
          //  localStorage.setItem("localStorageQty", localStorageQty);
           sale_qty= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
        }
     }
     else if(P_UNIT=="Carton" && S_UNIT=="Piece")
     {
        var pic=$('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.piece-in-carton').val();
        total_qty = sale_qty/pic;
        if(total_qty > parseFloat(product_qty[pos]))
        {
            alert('Quantity exceeds stock quantity!');
            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
          //  localStorageQty[rowindex] = sale_qty-1;
          //  localStorage.setItem("localStorageQty", localStorageQty);
            sale_qty= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
        }
     }
     else if(P_UNIT=="Carton" && S_UNIT=="Carton")
     {
        if(sale_qty > parseFloat(product_qty[pos]))
        {
            alert('Quantity exceeds stock quantity!');
            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
         // localStorageQty[rowindex] = sale_qty-1;
         //  localStorage.setItem("localStorageQty", localStorageQty);
           sale_qty= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
        }
     }
     else if(P_UNIT=="Piece" && S_UNIT=="Piece" )
     {
        if(sale_qty > parseFloat(product_qty[pos]))
        {
            alert('Quantity exceeds stock quantity!');
            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
       //   localStorageQty[rowindex] = sale_qty-1;
         //  localStorage.setItem("localStorageQty", localStorageQty);
           sale_qty= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
        }
     } 
     else
     {
        console.log(error);
     }
     /*
    if(product_type[pos] == 'standard') {
        var operator = unit_operator[rowindex].split(',');
        var operation_value = unit_operation_value[rowindex].split(',');
        if(operator[0] == '*')
            total_qty = sale_qty * operation_value[0];
        else if(operator[0] == '/')
            total_qty = sale_qty / operation_value[0];
        if (total_qty > parseFloat(product_qty[pos])) {
         
            if (flag) {
                sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                localStorageQty[rowindex] = sale_qty;
                localStorage.setItem("localStorageQty", localStorageQty);
                checkQuantity(sale_qty, true);
            } else {
                localStorageQty[rowindex] = sale_qty;
                localStorage.setItem("localStorageQty", localStorageQty);
                edit();
                return;
            }
        }
    }
    else if(product_type[pos] == 'combo'){
        child_id = product_list[pos].split(',');
        child_qty = qty_list[pos].split(',');
        $(child_id).each(function(index) {
            var position = product_id.indexOf(parseInt(child_id[index]));
            if( parseFloat(sale_qty * child_qty[index]) > product_qty[position] ) {
                alert('Quantity exceeds stock quantity!');
                if (flag) {
                    sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                }
                else {
                    edit();
                    flag = true;
                    return false;
                }
            }
        });
    } */

    if(!flag){
        $('#editModal').modal('hide');
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
    }
    calculateRowProductData(sale_qty);
}


function calculateRowProductData(quantity) {
var row_product_price = product_price[rowindex];
 var Unit1= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sell-unit1').val();
 var Unit2= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-unit1').val();
 var rowprice= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').val();
 var rowtotal= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').val();
//console.log(row_product_price ,  rowprice);
if(Unit2 != Unit1)
{
      if(Unit2=="Dozen" && Unit1 == "Piece")
      {
        var TM= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val();
       var ratu= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val();
       rowprice=row_product_price/12;
            if (TM == 1) {
            var tax = rowprice * quantity * (ratu / 100);
            rowtotal= quantity * rowprice;
            var sub_total_unit = rowtotal / quantity;
            }
            else
            {
                var tax = rowprice * quantity * (0);
                rowtotal= quantity * rowprice;
                var sub_total_unit = rowtotal / quantity;
            }
      }
      else if(Unit2=="Piece" && Unit1 == "Dozen")
      {
        var TM= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val();
       var ratu= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val();
      rowprice=row_product_price*12;
            if (TM == 1) {
            var tax = rowprice * quantity * (ratu / 100);
            rowtotal= quantity * rowprice;
            var sub_total_unit = rowtotal / quantity;
            }
            else
            {
                var tax = rowprice * quantity * ( 0);
                rowtotal= quantity * rowprice;
                var sub_total_unit = rowtotal / quantity;
            }
      }
      else if(Unit2=="Carton" && Unit1 == "Piece")
      {
        var TM= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val();
        var PIC= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.piece-in-carton').val();
       var ratu= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val();
       rowprice=row_product_price/PIC;
            if (TM == 1) {
            var tax = rowprice * quantity * (ratu / 100);
            rowtotal= quantity * rowprice;
            var sub_total_unit = rowtotal / quantity;
            }
            else
            {
                var tax = rowprice * quantity * (0);
                rowtotal= quantity * rowprice;
                var sub_total_unit = rowtotal / quantity;
            }
      }
      else if(Unit2=="Piece" && Unit1 == "Carton")
      {
        var TM= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val();
        var PIC1= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.piece-in-carton').val();
       var ratu= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val();
        rowprice=row_product_price*PIC1;
            if (TM == 1) {
            var tax = rowprice * quantity * (ratu / 100);
            rowtotal= quantity * rowprice;
            var sub_total_unit = rowtotal / quantity;
            }
            else
            {
                var tax = rowprice * quantity * (0);
                rowtotal= quantity * rowprice;
                var sub_total_unit = rowtotal / quantity;
            }
      }
      else
      {
        console.log(Unit1, Unit2);
      }
      $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val(tax_rate[rowindex].toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-value').val(tax.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(sub_total_unit.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(rowtotal.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal-value').val(rowtotal.toFixed(2));
}
else
{
    var TM= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-method').val();
    var ratu= $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val();
    rowprice=row_product_price;
    if (TM == 1) {
        var tax = rowprice * quantity * (ratu / 100);
        rowtotal= quantity * row_product_price;
        var sub_total_unit = rowtotal / quantity;
    }
    else
    {
        var tax = 0;
        rowtotal= quantity * row_product_price;
        var sub_total_unit = rowtotal / quantity;
    }

}
$('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val(ratu);
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-value').val(tax.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-price').text(sub_total_unit.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sub-total').text(rowtotal.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal-value').val(rowtotal.toFixed(2));

    localStorageTaxRate.splice(rowindex, 1, ratu);
    localStorageTaxValue.splice(rowindex, 1, tax.toFixed(2));
    localStorageSubTotalUnit.splice(rowindex, 1, sub_total_unit.toFixed(2));
    localStorageSubTotal.splice(rowindex, 1, rowtotal.toFixed(2));
    localStorage.setItem("localStorageTaxRate", localStorageTaxRate);
    localStorage.setItem("localStorageTaxValue", localStorageTaxValue);
    localStorage.setItem("localStorageSubTotalUnit", localStorageSubTotalUnit);
    localStorage.setItem("localStorageSubTotal", localStorageSubTotal);
    calculateTotal();
}

function calculateTotal() {
    //Sum of quantity
    var total_qty = 0;
    $("table.order-list tbody .qty").each(function(index) {
        if ($(this).val() == '') {
            total_qty += 0;
        } else {
            total_qty += parseFloat($(this).val());
        }
    });
    $('input[name="total_qty"]').val(total_qty);

    //Sum of discount
    var total_discount = 0;
    $("table.order-list tbody .discount-value").each(function() {
        total_discount += parseFloat($(this).val());
    });

    $('input[name="total_discount"]').val(total_discount.toFixed(2));
    //Sum of tax
    var total_tax = 0;
    $(".tax-value").each(function() {
        total_tax += parseFloat($(this).val());
    });

    $('input[name="total_tax"]').val(total_tax.toFixed(2));

    //Sum of subtotal
    var total = 0;
    $(".sub-total").each(function() {
        total += parseFloat($(this).text());
    });
    $('input[name="total_price"]').val(total.toFixed(2));
 //Sum of tax
 var total_tax =   $('input[name="total_price"]').val(total.toFixed(2));;
   
    calculateGrandTotal();
}

function calculateGrandTotal() {
    var item = $('table.order-list tbody tr:last').index();
    var total_qty = parseFloat($('input[name="total_qty"]').val());
    var subtotal = parseFloat($('input[name="total_price"]').val());
    var order_tax = parseFloat($('select[name="order_tax_rate_select"]').val());
    var order_discount_type = $('select[name="order_discount_type_select"]').val();
    var order_discount_value = parseFloat($('input[name="order_discount_value"]').val());

    if (!order_discount_value)
        order_discount_value = 0.00;

    if(order_discount_type == 'Flat')
        var order_discount = parseFloat(order_discount_value);
    else
        var order_discount = parseFloat(subtotal * (order_discount_value / 100));

    localStorage.setItem("order-tax-rate-select", order_tax);
    localStorage.setItem("order-discount-type", order_discount_type);
    $("#discount").text(order_discount.toFixed(2)); 
    $('input[name="order_discount"]').val(order_discount);
    $('input[name="order_discount_type"]').val(order_discount_type);

    var shipping_cost = parseFloat($('input[name="shipping_cost"]').val());
    if (!shipping_cost)
        shipping_cost = 0.00;

    item = ++item + '(' + total_qty + ')';
    order_tax = (subtotal - order_discount) * (order_tax / 100);
    var grand_total = (subtotal + order_tax + shipping_cost) - order_discount;
    $('input[name="grand_total"]').val(grand_total.toFixed(2));

    var coupon_discount = parseFloat($('input[name="coupon_discount"]').val());
    if (!coupon_discount)
        coupon_discount = 0.00;
    grand_total -= coupon_discount;

    $('#item').text(item);
    $('input[name="item"]').val($('table.order-list tbody tr:last').index() + 1);
    $('#subtotal').text(subtotal.toFixed(2));
    $('#tax').text(order_tax.toFixed(2));
    $('input[name="order_tax"]').val(order_tax.toFixed(2));
    $('#grand-total').text(grand_total.toFixed(2));
    $('input[name="grand_total"]').val(grand_total.toFixed(2));
}

function hide() {
    $(".card-element").hide();
    $(".card-errors").hide();
    $(".cheque").hide();
    $(".gift-card").hide();
    $('input[name="cheque_no"]').attr('required', false);
}







function cancel(rownumber) {
    while(rownumber >= 0) {
        product_price.pop();
        product_unit.pop();
        sell_unit.pop();
        product_discount.pop();
        tax_rate.pop();
        tax_name.pop();
        tax_method.pop();
        unit_name.pop();
        unit_operator.pop();
        unit_operation_value.pop();
        $('table.order-list tbody tr:last').remove();
        rownumber--;
    }
    $('input[name="shipping_cost"]').val('');
    $('input[name="order_discount"]').val('');
    $('select[name="order_tax_rate_select"]').val(0);
    calculateTotal();
}

function confirmCancel() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    if (confirm("Are you sure want to cancel?")) {
        cancel($('table.order-list tbody tr:last').index());
    }
    return false;
}

$(document).on('submit', '.payment-form', function(e) {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
        e.preventDefault();
    }
    else if( parseFloat( $('input[name="paying_amount"]').val() ) == 0 ){
        alert('Grand total cannot be zero');
        e.preventDefault();
    }
    else {
        $("#submit-button").prop('disabled', true);
    }
    $('input[name="paid_by_id"]').val($('select[name="paid_by_id_select"]').val());
    $('input[name="deposit_bank_id"]').val($('select[name="bank_id"]').val());
    $('input[name="order_tax_rate"]').val($('select[name="order_tax_rate_select"]').val());

});

    var kas_id=$('input[name="kasu_id"]').val();
    var table;
    if(kas_id == 1)
    {
        table =  $('#product-table').DataTable( {
        "order": [],
        'retrieve':true,
        'paging': true,
        'pageLength': product_row_number,
        'language': {
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        },
        dom: 'tp'
    });
    }
    if(kas_id == 2)
    {
        table =  $('#vehicle-table').DataTable( {
        "order": [],
        'retrieve':true,
        'paging': false,
        'pageLength': product_row_number,
        'language': {
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        },
        dom: 'tp'
    });
    }
    if(kas_id == "" || kas_id == null)
    {
        table =  $('#product-table').DataTable( {
        "order": [],
        'retrieve':true,
        'paging': true,
        'pageLength': product_row_number,
        'language': {
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        },
        dom: 'tp'
    });
    }
    $('#products-filter').click(function(e){
        table.destroy();
        table =  $('#product-table').DataTable( {
        "order": [],
        'retrieve':true,
        'pageLength': product_row_number,
        'language': {
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        },
        dom: 'tp'
    });
    
    
    });

    $('#vehicles-filter').click(function(e){
        table.destroy();
        table =  $('#vehicle-table').DataTable( {
        "order": [],
        'retrieve':true,
        'pageLength': product_row_number,
        'language': {
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        },
        dom: 'tp'
    });
    });

</script>
@endpush
