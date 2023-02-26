@extends('layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
    <br>
    <div class="container-fluid">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> Add Proforma Invoice</button>&nbsp;
        <a href="#" data-toggle="modal" data-target="#importItem" class="btn btn-primary"><i class="dripicons-copy"></i> Import Items</a>
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>PFI Date</th>
                    <th>PFI Number</th>
                    <th>Order Number</th>
                    <th>Supplier Name</th>
                    <th>Buyer Name</th>
                    <th>No Of Items</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table> 
    </div>


<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
       
        <form class="form" id="form-add-proforma" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-file"></i> Add Proforma Invoice</h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <p class="italic" style="margin-left: 20px;"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
        <div class="modal-body">
            
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Supplier Name * </strong> </label>
                        <input type="text" value="" class="form-control"   id="supplier_name" name="supplier_name" required="true" placeholder="Enter supplier name ...">
                        <div id="supplier_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Buyer Name  </strong> </label>
                        <input type="text" value="" class="form-control"   id="buyer_name" name="buyer_name"  placeholder="Enter buyer name ...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Order Number *  </strong> </label>
                        <input type="text" value="" class="form-control" required="true"   id="order_number" name="order_number"  placeholder="Enter order number ...">
                        <div id="order_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

           </div>
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> PFI Number *  </strong> </label>
                        <input type="text" value="" class="form-control" required="true"  id="pfi_number" name="pfi_number"  placeholder="Enter PFI number ...">
                        <div id="number_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> PFI Date *  </strong> </label>
                        <input type="text" id="pfi_date" name="pfi_date" value="" class="datepicker-field form-control"  required="true"   >
                        <div id="date_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <label><strong>Bank Name *</strong></label>
                    <select id="bank_name"  value=""  name="bank_name" class="form-control selectpicker" title="Select bank name...">
                        @foreach($banks as $warehouse1)
                        <option value="{{$warehouse1->name}}">{{$warehouse1->name}}</option>
                        @endforeach
                    </select> 
                    <div id="bank_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
               </div>
               <div class="form-group col-md-12">
                <label><strong>Payment Term </strong></label>
                <textarea id="payment_term" rows="2" class="form-control" name="payment_term"></textarea>
            </div>
           </div>
                
           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="createProforma"  type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div> 
<!-- Edit Modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
       
        <form class="form" id="form-update-proforma" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-file"></i> Update Proforma Invoice</h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <p class="italic" style="margin-left: 20px;"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
        <div class="modal-body">
            
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <input type="hidden" name="pre_number" id="pre_number">
                        <label><strong> Supplier Name * </strong> </label>
                        <input type="text" value="" class="form-control"   id="edit_supplier_name" name="edit_supplier_name" required="true" placeholder="Enter supplier name ...">
                        <div id="edit_supplier_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Buyer Name  </strong> </label>
                        <input type="text" value="" class="form-control"   id="edit_buyer_name" name="edit_buyer_name"  placeholder="Enter buyer name ...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Order Number *  </strong> </label>
                        <input type="text" value="" class="form-control" required="true"   id="edit_order_number" name="edit_order_number"  placeholder="Enter order number ...">
                        <div id="edit_order_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

           </div>
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> PFI Number *  </strong> </label>
                        <input type="text" value="" class="form-control" required="true"  id="edit_pfi_number" name="edit_pfi_number"  placeholder="Enter PFI number ...">
                        <div id="edit_number_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> PFI Date *  </strong> </label>
                        <input type="text" id="edit_pfi_date" name="edit_pfi_date" value="" class="datepicker-field form-control"  required="true"   >
                        <div id="edit_date_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <label><strong>Bank Name *</strong></label>
                    <select id="edit_bank_name"  value=""  name="edit_bank_name" class="form-control selectpicker" title="Select bank name...">
                        @foreach($banks as $warehouse1)
                        <option value="{{$warehouse1->name}}">{{$warehouse1->name}}</option>
                        @endforeach
                    </select> 
                    <div id="edit_bank_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
               </div>
               <div class="form-group col-md-12">
                <label><strong>Payment Term </strong></label>
                <textarea id="edit_payment_term" rows="2" class="form-control" name="edit_payment_term"></textarea>
            </div>
           </div>
                
           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="updateProforma"  type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>
<!-- Item Create Modal -->
<div id="createItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-add-item" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-plus"> </i>   Add Item To Proforma Invoice (<span id="proforma_name"> </span>)</h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="hidden" name="proforma_id" id="proforma_id">
                        <label><strong> Quantity * </strong> </label>
                        <input type="text" value="" class="form-control" onkeypress="return onlyNumberKey(event)"   id="item_qty" name="item_qty" required="true" placeholder="Enter item quantity ...">
                        <div id="qty_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Unit Price * </strong> </label>
                        <input type="text" value="" class="form-control" onkeypress="return onlyNumberKey(event)"   id="unit_price" name="unit_price" required="true" placeholder="Enter item unit price ...">
                        <div id="price_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Total  Amount *  </strong> </label>
                        <input type="text" value="" onkeypress="return onlyNumberKey(event)" class="form-control" required="true"   id="total_amount" name="total_amount"  placeholder="Enter total amount ...">
                        <div id="amount_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

           </div>
           <div class="row">
               <div class="form-group col-md-12">
                <label><strong>Description * </strong></label>
                <textarea id="item_description" rows="2" class="form-control" name="item_description"></textarea>
                <div id="description_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
               </div>
           </div>
                
           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="addItem"  type="submit" value="Add Item" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>
<!-- Import Items Modal -->
<div id="importItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" enctype="multipart/form-data" method="POST" action="/proforma/item/import" >
            @csrf   {!! csrf_field() !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">Import Items</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
           <p>{{trans('file.The correct column order is')}} (description*, quantity*, unit_price*, total_amount*) {{trans('file.and you must follow this')}}. </p>
          
           <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong> PFI Number *  </strong> </label>
                    <select id="import_proforma_id"  value=""  name="import_proforma_id" class="form-control selectpicker" required title="Please select proforma first...">
                        <option value="">Please select proforma ...</option> 
                        @foreach($proformas as $warehouse1)
                        <option value="{{$warehouse1->id}}">{{$warehouse1->pfi_number}} , ({{ $warehouse1->pfi_date }})</option> 
                        @endforeach
                    </select> 
                </div>
            </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Upload CSV File *  </strong> </label>
                        <input   class="form-control" required  accept=".csv"  id="selected_file" type="file" name="file" onchange="checkfile(this);">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label> {{trans('file.Sample File')}}</label>
                        <a href="{{asset('/sample_file/sample_items.csv')}}" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
                      
                    </div>
                </div>
           </div>
           <div class="form-group">
            <input   type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div> 
<!-- Proforma Detail Modal -->
<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-6 d-print-none">
                       
                    </div>
                    <div class="col-md-6 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div> 
                    <div class="col-md-12 text-center">
                        <h2 style="font-size: 15px;">PROFORMA INVOICE DETAILS</h2>
                    </div>
                </div>
            </div>
            <div id="sale-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-sale-list">
                <thead>
                    <th>#</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Added By</th>
                </thead>
                <tbody>
               </tbody>
            </table>
            <br>
        </div>
    </div>
</div>
<!-- View Items Modal -->
<div id="view-items" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> 
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="col-md-12 text-center">
                <h5 id="exampleModalLabel" class="modal-title"> Proforma Invoice ( <span id="items-pro-name"> </span>)  Items</h5>
            </div>
            <div class="modal-body">
                <table class="table table-hover payment-list">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Amount</th>
                            <th>{{trans('file.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Item Edit Modal -->
<div id="editItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-update-item" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-edit"> </i>   Update Item </h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <p class="italic" style="margin-left: 20px;"><small>All fields are required input fields</small></p>
        <div class="modal-body">
            
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="hidden" name="kasu_id" id="kasu_id">
                        <label><strong> Quantity * </strong> </label>
                        <input type="text" value="" class="form-control" onkeypress="return onlyNumberKey(event)"   id="kasu_qty" name="kasu_qty" required="true" placeholder="Enter item quantity ...">
                        <div id="kqty_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="hidden" name="proforma_id" id="proforma_id">
                        <label><strong> Unit Price * </strong> </label>
                        <input type="text" value="" class="form-control" onkeypress="return onlyNumberKey(event)"   id="kasu_price" name="kasu_price" required="true" placeholder="Enter item unit price ...">
                        <div id="kprice_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> Total  Amount *  </strong> </label>
                        <input type="text" value="" onkeypress="return onlyNumberKey(event)" class="form-control" required="true"   id="kasu_amount" name="kasu_amount"  placeholder="Enter total amount ...">
                        <div id="kamount_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

           </div>
           <div class="row">
               <div class="form-group col-md-12">
                <label><strong>Description * </strong></label>
                <textarea id="kasu_description" rows="2" class="form-control" name="kasu_description"></textarea>
                <div id="kdescription_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
               </div>
           </div>
                
           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="updateItem"  type="submit" value="Update Item" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>
<!-- Bank Submit Modal -->
<div id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="form" class="modal-dialog">
    <div class="modal-content">
        <form class="form" id="form-bank-submit" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-money"></i>   Add Proforma (<span id="proforma_bank"> </span>) To Bank Submit </h3>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body overflow-hidden">
            
            <div class="row">
                <div  class="col-md-6">
                    <div class="form-group">
                    <input type="hidden" id="submit_id" name="submit_id">
                    <label> <strong> Permit Number </strong> </label>
                    <input type="text" value="" id="permit_number" placeholder="Enter Permit Number..."  name="permit_number" class="form-control" step="any">
                    <div id="permit_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Payment Method </strong></label>
                        <select id="payment_method"   name="payment_method" class="form-control selectpicker" title="Select payment method...">
                        <option value="CAD">CAD</option>
                        <option value="LC">LC</option>
                        <option value="TT">TT</option>
                        </select> 
                        <div id="method_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

                <div  class="col-md-6">
                    <div class="form-group">
                    <label> <strong> <span id="payment-text"> Payment </span> Number</strong> </label>
                    <input type="text" value="" id="payment_number"  name="payment_number" class="form-control" placeholder="Enter Payment Number..." step="any">
                    <div id="pnum_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div  class="col-md-6">
                    <div class="form-group" style="float:right; margin-right: 100px; margin-top: 30px;">
                        <input id="bank-submit-btn"  type="submit" value="Add Bank Submit" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>
</div>
@foreach($proformas as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->pfi_number }}">
@endforeach
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-proforma-menu").addClass("active");
    var desc = [];
    var quantity = [];
    var unitprice = [];
    var amount = [];
    var item_id = [];
    function checkfile(sender)
      {
        var validExts = new Array(".csv");
        var fileExt = sender.value;
        fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
        if (validExts.indexOf(fileExt) < 0) {
        alert("Invalid file selected, valid files are of " +
                validExts.toString() + " types.");
        document.getElementById("selected_file").value = null;
        return false;
        }
        else
        {
            return true;
        }
      }
    function onlyNumberKey(evt) 
      {
          
          // Only ASCII character in that range allowed
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
      }
    var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });

    $(document).ready(function () {
    $('#footer-ims').css('position','relative');
   });
   $(document).ready(function () {
   $('.datepicker-field').datepicker({
    format: 'MM,dd-mm-yyyy',
    autoclose: true
   });
   var today = moment().format('MMMM,DD-MM-YYYY');
   $('#pfi_date').val(today);
   });
    $("#createProforma").click(function(e){
          e.preventDefault();
        if ($('#supplier_name').val() == "" ){
            var value = "Please enter supplier name ...";
            $("#supplier_error_msg").css('display','block');
            $("#supplier_error_msg").text(value);
            $("#order_error_msg").css('display','none');
            $("#number_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
            $("#bank_error_msg").css('display','none');
            $("#supplier_name").css('border-color','red');
            return false;
        }
        else if($('#order_number').val() == ""){
            var value = "Please enter order number ...";
            $("#order_error_msg").css('display','block');
            $("#order_error_msg").text(value);
            $("#supplier_error_msg").css('display','none');
            $("#number_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
            $("#bank_error_msg").css('display','none');
            $("#order_number").css('border-color','red');
            return false;
        }
        else if($('#pfi_number').val() == ""){
            var value = "Please enter PFI number ...";
            $("#number_error_msg").css('display','block');
            $("#number_error_msg").text(value);
            $("#supplier_error_msg").css('display','none');
            $("#order_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
            $("#bank_error_msg").css('display','none');
            $("#pfi_number").css('border-color','red');
            return false;
        }
        else if($.inArray($('#pfi_number').val(),myArray) !== -1){
            var value = "Proforma already exists, please add different one .";
            $("#number_error_msg").css('display','block');
            $("#number_error_msg").text(value);
            $("#supplier_error_msg").css('display','none');
            $("#order_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
            $("#bank_error_msg").css('display','none');
            $("#pfi_number").css('border-color','red');
            return false;
        }
        else if($('#pfi_date').val() == ""){
            var value = "Please enter PFI date ...";
            $("#date_error_msg").css('display','block');
            $("#date_error_msg").text(value);
            $("#supplier_error_msg").css('display','none');
            $("#order_error_msg").css('display','none');
            $("#number_error_msg").css('display','none');
            $("#bank_error_msg").css('display','none');
            $("#pfi_date").css('border-color','red');
            return false;
        }
        else if($('#bank_name').val() == ""){
            var value = "Please select bank name ...";
            $("#bank_error_msg").css('display','block');
            $("#bank_error_msg").text(value);
            $("#supplier_error_msg").css('display','none');
            $("#order_error_msg").css('display','none');
            $("#number_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
            $("#bank_name").css('border-color','red');
            return false;
        }
        else{ 
            $("#bank_error_msg").css('display','none');
            $("#supplier_error_msg").css('display','none');
            $("#order_error_msg").css('display','none');
            $("#number_error_msg").css('display','none');
            $("#date_error_msg").css('display','none');
        }
        var data = $('#form-add-proforma').serialize();

        $.ajax({
            url:"{{ route('purchase.proforma.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                    var msg = 'Proforma added successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.proforma') }}';
                    });
            }
        });
    }); 
    $("#updateProforma").click(function(e){
        e.preventDefault();
        if ($('#edit_supplier_name').val() == "" ){
            var value = "Please enter supplier name ...";
            $("#edit_supplier_error_msg").css('display','block');
            $("#edit_supplier_error_msg").text(value);
            $("#edit_order_error_msg").css('display','none');
            $("#edit_number_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_supplier_name").css('border-color','red');
            return false;
        }
        else if($('#edit_order_number').val() == ""){
            var value = "Please enter order number ...";
            $("#edit_order_error_msg").css('display','block');
            $("#edit_order_error_msg").text(value);
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_number_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_order_number").css('border-color','red');
            return false;
        }
        else if($('#edit_pfi_number').val() == ""){
            var value = "Please enter PFI number ...";
            $("#edit_number_error_msg").css('display','block');
            $("#edit_number_error_msg").text(value);
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_order_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_pfi_number").css('border-color','red');
            return false;
        }
        else if($('#edit_pfi_number').val() != $('#pre_number').val() && $.inArray($('#edit_pfi_number').val(),myArray) !== -1){
            var value = "Proforma already exists, please add different one .";
            $("#edit_number_error_msg").css('display','block');
            $("#edit_number_error_msg").text(value);
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_order_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_pfi_number").css('border-color','red');
            return false;
        }
        else if($('#edit_pfi_date').val() == ""){
            var value = "Please enter PFI date ...";
            $("#edit_date_error_msg").css('display','block');
            $("#edit_date_error_msg").text(value);
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_order_error_msg").css('display','none');
            $("#edit_number_error_msg").css('display','none');
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_pfi_date").css('border-color','red');
            return false;
        }
        else if($('#edit_bank_name').val() == ""){
            var value = "Please select bank name ...";
            $("#edit_bank_error_msg").css('display','block');
            $("#edit_bank_error_msg").text(value);
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_order_error_msg").css('display','none');
            $("#edit_number_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
            $("#edit_bank_name").css('border-color','red');
            return false;
        }
        else{ 
            $("#edit_bank_error_msg").css('display','none');
            $("#edit_supplier_error_msg").css('display','none');
            $("#edit_order_error_msg").css('display','none');
            $("#edit_number_error_msg").css('display','none');
            $("#edit_date_error_msg").css('display','none');
        }
        var data = $('#form-update-proforma').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                    var msg = 'Proforma updated successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.proforma') }}';
                    });
            }
        });
    });
    $("#addItem").click(function(e){
        e.preventDefault();
        if ($('#item_qty').val() == "" || $('#item_qty').val() == 0 ){
            var value = "Please enter item quantity ...";
            $("#qty_error_msg").css('display','block');
            $("#qty_error_msg").text(value);
            $("#price_error_msg").css('display','none');
            $("#amount_error_msg").css('display','none');
            $("#description_error_msg").css('display','none');
            $("#item_qty").css('border-color','red');
            return false;
        }
        else if($('#unit_price').val() == "" || $('#unit_price').val() == 0){
            var value = "Please enter item unit price ...";
            $("#price_error_msg").css('display','block');
            $("#price_error_msg").text(value);
            $("#qty_error_msg").css('display','none');
            $("#amount_error_msg").css('display','none');
            $("#description_error_msg").css('display','none');
            $("#unit_price").css('border-color','red');
            return false;
        }
        else if($('#total_amount').val() == "" || $('#total_amount').val() == 0){
            var value = "Please enter total amount ...";
            $("#amount_error_msg").css('display','block');
            $("#amount_error_msg").text(value);
            $("#qty_error_msg").css('display','none');
            $("#price_error_msg").css('display','none');
            $("#description_error_msg").css('display','none');
            $("#total_amount").css('border-color','red');
            return false;
        }
        else if($('#item_description').val() == "" || $('#item_description').val() == 0){
            var value = "Please enter item description ...";
            $("#description_error_msg").css('display','block');
            $("#description_error_msg").text(value);
            $("#qty_error_msg").css('display','none');
            $("#price_error_msg").css('display','none');
            $("#amount_error_msg").css('display','none');
            $("#item_description").css('border-color','red');
            return false;
        }
        else{ 
            $("#item_description").css('display','none');
            $("#price_error_msg").css('display','none');
            $("#amount_error_msg").css('display','none');
            $("#description_error_msg").css('display','none');
        }
        var data = $('#form-add-item').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.item.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            }, success:function(data){
                    var msg = 'Item added to proforma successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.proforma') }}';
                    });
            }
        });
    });
    $("#updateItem").click(function(e){
        e.preventDefault();
        if ($('#kasu_qty').val() == "" || $('#kasu_qty').val() == 0 ){
            var value = "Please enter item quantity ...";
            $("#kqty_error_msg").css('display','block');
            $("#kqty_error_msg").text(value);
            $("#kprice_error_msg").css('display','none');
            $("#kamount_error_msg").css('display','none');
            $("#kdescription_error_msg").css('display','none');
            $("#editItem #kasu_qty").css('border-color','red');
            return false;
        }
        else if($('#kasu_price').val() == "" || $('#kasu_price').val() == 0){
            var value = "Please enter item unit price ...";
            $("#kprice_error_msg").css('display','block');
            $("#kprice_error_msg").text(value);
            $("#kqty_error_msg").css('display','none');
            $("#kamount_error_msg").css('display','none');
            $("#kdescription_error_msg").css('display','none');
            $("#editItem #kasu_price").css('border-color','red');
            return false;
        }
        else if($('#kasu_amount').val() == "" || $('#kasu_amount').val() == 0){
            var value = "Please enter total amount ...";
            $("#kamount_error_msg").css('display','block');
            $("#kamount_error_msg").text(value);
            $("#kqty_error_msg").css('display','none');
            $("#kprice_error_msg").css('display','none');
            $("#kdescription_error_msg").css('display','none');
            $("#editItem #kasu_amount").css('border-color','red');
            return false;
        }
        else if($('#kasu_description').val() == "" ){
            var value = "Please enter item description ...";
            $("#kdescription_error_msg").css('display','block');
            $("#kdescription_error_msg").text(value);
            $("#kqty_error_msg").css('display','none');
            $("#kprice_error_msg").css('display','none');
            $("#kamount_error_msg").css('display','none');
            $("#editItem #kasu_description").css('border-color','red');
            return false;
        }
        else{ 
            $("#kitem_description").css('display','none');
            $("#kprice_error_msg").css('display','none');
            $("#kamount_error_msg").css('display','none');
            $("#kdescription_error_msg").css('display','none');
        }
        var data = $('#form-update-item').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.item.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            }, success:function(data){
                    var msg = 'Item updated successfully.';
                    $('#editItem').modal('hide');
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.proforma') }}';
                    });
            }
        });
    });
    $("#bank-submit-btn").click(function(e){
          e.preventDefault();
        if ($('#bankModal input[name="permit_number"]').val() == "" ){
            var value = "Please Enter Permit Number  ...";
            $("#permit_error_msg").css('display','block');
            $("#permit_error_msg").text(value);
            $("#method_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            return false;
        }
        else if ($('#bankModal select[name="payment_method"]').val() == "" ){
            var value = "Please Select Payment Method  ...";
            $("#method_error_msg").css('display','block');
            $("#method_error_msg").text(value);
            $("#permit_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            return false;
        }
        else if ($('#bankModal input[name="payment_number"]').val() == "" ){
            var value = "Please Enter Payment Number  ...";
            $("#pnum_error_msg").css('display','block');
            $("#pnum_error_msg").text(value);
            $("#permit_error_msg").css('display','none');
            $("#method_error_msg").css('display','none');
            return false;
        }
        else{ 
            $("#method_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            $("#permit_error_msg").css('display','none');
        }
        var data = $('#form-bank-submit').serialize();

        $.ajax({
            url:"{{ route('purchase.proforma.bank.submit')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                    var msg = 'Proforma added to bank submit successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.bank-submit') }}';
                    });
            }
        });
    }); 
    $(document).on("click", ".DeleteCategoryDialog", function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure want to delete proforma, "+name+"?",
            text: "You will not be able to revert this action.",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0275d8",
            confirmButtonText: "Yes, Delete it!",
            closeOnConfirm: false,
            preConfirm: function(result) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: "{{url('/purchase/proforma/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({ 
                                    title: "Success",
                                    text: "Bank "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    window.location.href='{{ route('purchases.proforma') }}';
                                });
                        }else{
                            swal.fire({
                                title: "Error!",
                                text: "Failed to Delete!",
                                icon: "error",
                                button: "Ok",
                            }).then(function(){ 
                                    location.reload();
                                });
                        }

                    }
                })
            },
            allowOutsideClick: false
        });
    });
    var category_id = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".open-EditCategoryDialog", function(){
        var url= "{{url('/purchase/proforma/edit')}}/";
          var id = $(this).data('id').toString();
          url = url.concat(id);

          $.get(url, function(data){
            $("#editModal input[name='edit_id']").val(data['id']);
            $("#editModal input[name='pre_number']").val(data['pfi_number']);
            $("#editModal input[name='edit_supplier_name']").val(data['supplier_name']);
            if(data['buyer_name'] != null)
            {
                $("#editModal input[name='edit_buyer_name']").val(data['buyer_name']);
            }
            $("#editModal input[name='edit_order_number']").val(data['order_number']);
            $("#editModal input[name='edit_pfi_number']").val(data['pfi_number']);
           // $("#editModal input[name='edit_pfi_date']").val(data['pfi_date']);
            $("#editModal select[name='edit_bank_name']").val(data['bank_name']);
            $('.selectpicker').selectpicker('refresh');
            if(data['payment_term'] != null)
            {
                $("#editModal textarea[name='edit_payment_term']").val(data['payment_term']);
            }
          });
          var today = moment().format('MMMM,DD-MM-YYYY');
          $('#edit_pfi_date').val(today);
          $("#editModal").modal('show');
    });
    $(document).on("click", ".AddItemDialog", function(){
          var id = $(this).data('id');
          var name = $(this).data('name');
          $("#createItemModal input[name='proforma_id']").val(id);
          $("#createItemModal span[id='proforma_name']").text(name);
          $("#createItemModal").modal('show');
    });
    $(document).on("click", ".pro-bank-submit", function(){
          var id = $(this).data('id');
          var name = $(this).data('name');
          $("#bankModal input[name='submit_id']").val(id);
          $("#bankModal span[id='proforma_bank']").text(name);
          $("#method_error_msg").css('display','none');
          $("#pnum_error_msg").css('display','none');
          $("#permit_error_msg").css('display','none');
          $("#bankModal input[name='payment_number']").val("");
          $("#bankModal input[name='permit_number']").val("");
          $("#bankModal select[name='payment_method']").val("");
          $("#bankModal").modal('show');
    });
    $('#bankModal select[name="payment_method"]').on("change", function() {
        var PayMthd = $(this).val();
        $('#bankModal span[id="payment-text"]').html(PayMthd);
    }); 
    // View detail and view items javascript
    $(document).on("click", "tr.proforma-link td:not(:first-child, :last-child)", function() {
        var sale = $(this).parent().data('proforma');
        saleDetails(sale);
    });
    $(document).on("click", ".pro-view", function(){
        var sale = $(this).parent().parent().parent().parent().parent().data('proforma');
        saleDetails(sale);
    });
     
    $(document).on("click", "table.sale-list tbody .get-items", function(event) {
        rowindex = $(this).closest('tr').index();
        var id = $(this).data('id').toString();
        $.get('/proforma/getItem/' + id, function(data) {
            $(".payment-list tbody").remove();
            var newBody = $("<tbody>");
            desc = data[0];
            quantity = data[1];
            unitprice = data[2];
            amount = data[3];
            item_id = data[4];
            $.each(desc, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + desc[index] + '</td>';
                cols += '<td>' + quantity[index] + '</td>';
                cols += '<td>' + unitprice[index] + '</td>';
                cols += '<td>' + amount[index] + '</td>';
                cols += '<td><div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans("file.action")}}<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                    cols += '<li><button type="button" class="btn btn-link edit-item-btn" data-id="' + item_id[index] +'" data-clicked=false data-toggle="modal" data-target="#editItem"><i class="dripicons-document-edit"></i> {{trans("file.edit")}}</button></li> ';
                    cols += '<li><button type="button" class="btn btn-link delete-item-btn" data-id="' + item_id[index] +'" data-name="' + desc[index] +'"  ><i class="dripicons-trash"></i> Delete </button></li> ';
                cols += '</ul></div></td>';
                newRow.append(cols);
                newBody.append(newRow);
                $("table.payment-list").append(newBody);
            });
            $('#view-items #items-pro-name').text(data['proforma']);
            $('#view-items').modal('show');
        }); 
    });
    $("table.payment-list").on("click", ".edit-item-btn", function(event) {
        $(".edit-item-btn").attr('data-clicked', true);
        var id = $(this).data('id').toString();
        $.each(item_id, function(index){
            if(item_id[index] == parseFloat(id)){
                $('#editItem input[name="kasu_id"]').val(item_id[index]);
                $('#editItem input[name="kasu_qty"]').val(quantity[index]);
                $('#editItem input[name="kasu_price"]').val(unitprice[index]);
                $('#editItem input[name="kasu_amount"]').val(amount[index]);
                $('#editItem textarea[name="kasu_description"]').val(desc[index]);
                return false;
            }
        });
        $('#view-items').modal('hide');
    });
    $("table.payment-list").on("click", ".delete-item-btn", function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure want to delete item, "+name+"?",
            text: "You will not be able to revert this action.",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0275d8",
            confirmButtonText: "Yes, Delete it!",
            closeOnConfirm: false,
            preConfirm: function(result) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $('#view-items').modal('hide');
                $.ajax({
                    type: 'GET',
                    url: "{{url('/purchase/proforma/item/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({ 
                                    title: "Success",
                                    text: "Item "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                })
                        }else{
                            swal.fire({
                                title: "Error!",
                                text: "Failed to Delete!",
                                icon: "error",
                                button: "Ok",
                            }).then(function(){ 
                                    location.reload();
                                });
                        }

                    }
                })
            },
            allowOutsideClick: false
        });
    });
    $('#category-table').DataTable( {
        responsive: true,
        "processing": true,
        "serverSide": true,
        "ajax":{ 
            url:"{{ route('purchase.proforma.data')}}",
            data:{
                "_token" : "{{ csrf_token() }}",
                }, 
            dataType: "json",
            type:"post" 
        }, 
        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('proforma-link');
            $(row).attr('data-proforma', data['proforma']);
        },  
        "columns": [
            {"data": "key"},
            {"data": "pfi_date"},
            {"data": "pfi_number"},
            {"data": "order_number"},
            {"data": "supplier_name"},
            {"data": "buyer_name"},
            {"data": "no_of_item"},
            {"data": "options"},
        ], 
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['3', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 4,6,7]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],

        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                        category_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var sale = $(this).closest('tr').data('proforma');
                                category_id[i-1] = sale[1];
                            }
                        });
                        if(category_id.length ) {
                            swal.fire({ 
                            title: "Are you sure want to delete selected records ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!",
                            closeOnConfirm: false,
                            preConfirm: function(result) {
                            $.ajax({
                                type:'POST',
                                url:"{{ route('purchase.proforma.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                    categoryIdArray: category_id,
                                },
                                success:function(data){                                   
                                     if($.isEmptyObject(data.error)){
                                        var msg = "Selected records deleted successfully.";
                                        swal.fire({
                                            title: "Success",
                                            text: msg, 
                                            icon: "success", 
                                            button: "Ok"
                                        }).then(function(){ 
                                            window.location.href='{{ route('purchases.proforma') }}';
                                        });
                                        }else{
                                            console.log(data.error);
                                        }
                                        
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                            dt.rows('selected').remove().draw(false);
                        }
                        else if(!category_id.length)
                        {
                            alert('No proforma is selected!');
                        }
                        else
                        {
                            alert('You cancelled the action !');
                        }
                      
                    
                   
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );                     
    function saleDetails(sale){
        if(sale[3] != "")
        {
            var byi = sale[3];
        }
        else
        {
            var byi = "N/A"; 
        }
        if(sale[8] != "")
        {
            var pti = sale[8];
        }
        else
        {
            var pti = "N/A"; 
        }
        var htmltext = '<div class="row"><div class="col-md-6"><strong> Supplier Name :&nbsp;&nbsp;</strong>'+sale[2]+'<br> <strong> PFI Number :&nbsp;&nbsp;</strong>'+sale[6]+'<br> <strong> Order Number :&nbsp;&nbsp;</strong>'+sale[4]+'<br> <strong> Created By :&nbsp;&nbsp;</strong>'+sale[9]+' ,<a href="#"> '+sale[10]+'</a> </div><div class="col-md-6"><strong> Buyer Name :&nbsp;&nbsp;</strong>'+byi+'<br> <strong> PFI Date :&nbsp;&nbsp;</strong>'+sale[5]+'<br> <strong> Bank Name :&nbsp;&nbsp;</strong>'+sale[7]+'<br> <strong> Created At :&nbsp;&nbsp;</strong>'+sale[0]+'<br></div></div>';
        var htmltext1 =  '<div class="row"><div class="col-md-12"> <strong> Payment Term :&nbsp;&nbsp;</strong> <span style="font-family:georgia;">  '+pti+'  </span> </div></div>';
       var idd = sale[1].toString(); 
        $.get('/purchases/proforma_item/' + sale[1], function(data){
            $(".product-sale-list tbody").remove();
            if(data == "" || data == null)
            {
            var newBody = $("<tbody>");
            var newRow = $("<tr>");
            cols = '';
            cols += '<td style="border:none;" colspan=3></td>';
            cols += '<td style="border:none;" colspan=2>No item is avaliable.</td>';
            cols += '<td style="border:none;" colspan=1></td>';
            newRow.append(cols);
            newBody.append(newRow);
            }
            else
            {
            var description = data[0];
            var qty = data[1];
            var unit_price = data[2];
            var total = data[3];
            var user = data[5];
            var newBody = $("<tbody>");
            $.each(description, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + description[index] + '</td>';
                cols += '<td>' + qty[index] + '</td>';
                cols += '<td>' + unit_price[index] + '</td>';
                cols += '<td>' + total[index] + '</td>';
                cols += '<td>' + user[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });
            var qty_sum = data[1].reduce((a, b) => a + b).toLocaleString();
            var amt_sum = data[3].reduce((a, b) => a + b).toLocaleString();
            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=2><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td> <strong>' + qty_sum + '</strong></td>';
            cols += '<td></td>';
            cols += '<td><strong>' + amt_sum + '</strong></td>';
            cols += '<td></td>';
            newRow.append(cols);
            newBody.append(newRow);

            }
            $("table.product-sale-list").append(newBody);
        });
      
        $('#sale-content').html(htmltext);
        $('#sale-content').append(htmltext1);
        $('#sale-details').modal('show');
    }
</script>
@endpush