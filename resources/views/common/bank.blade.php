@extends('layout.main') @section('content')
@if($errors->has('title'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Name already exists. Please add new one !</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <button class="btn btn-info" onclick=" $('#createModal').modal('show');"><i class="dripicons-plus"></i> {{trans('file.Add Bank')}} </button>&nbsp;
    </div>
    <div class="table-responsive">
        <table id="biller-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.bankname')}}</th>
                    <th>{{trans('file.bankcontact')}}</th>
                    <th>{{trans('file.br_created')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($brands as $key=> $brand)
                    <tr data-id="{{$brand->id}}">
                        <td>{{$key}}</td>
                    <td>{{ $brand->title }}</td>
                    <td>{{ $brand->contact }}</td>
                    <td>{{\Carbon\Carbon::parse($brand->created_at)->format('d/m/Y')  }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    <li><button type="button" data-id="{{$brand->id}}" data-name="{{$brand->name}} "  id="editButton" class="open-EditbrandDialog btn btn-link"  data-toggle="modal" data-target="#editBrandModal"  ><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button></li>
                                <li class="divider"></li>
                                <li>
                                    <li><button type="button" id="kt-delete"  class="DeletebrandDialog btn btn-link" data-id="{{$brand->id}}" data-name="{{$brand->name}}"   ><i class="dripicons-trash"></i> {{trans('file.delete')}}</button></li>
                                   
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Create Modal -->
       <br> <br>

</section>

<div id="createModal" tabindex="-1" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="kt_modal_new_address_form_update">
        <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Bank')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
           <div class="row"> 
          <div class="col-md-6 form-group">
                <label>{{trans('file.bankname')}} *</label>
                
                <input type="text" class="form-control" id="bank_name" name="bank_name" required="true" placeholder="Type bank name...">
                <div class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
            </div>
            <div class="col-md-6 form-group">
                <label>{{trans('file.bankcontact')}} </label>
                
                <input type="text" class="form-control" id="contact" name="contact" placeholder="Type bank contact...">
              
            </div>
           </div>
            <br>
            <div class="form-group">
              <input id="DocUpdate" type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
        </div>
        </form>
      </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editBrandModal" tabindex="-1"  aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
          <form class="form"  action="{{route('bank.update')}}" method="POST"  id="bank-form">
          @csrf
            <div class="modal-header">
              <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Bank')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
          </div>
          <div class="modal-body">
            <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>

            <div class="col-md-6 form-group">
                <label>{{trans('file.bankname')}} *</label>
                <input type="hidden" class="form-control " id="edit_id" name="edit_id">
                <input type="hidden" class="form-control " id="pre_name" name="pre_name">
                <input type="text" class="form-control" id="edit_name" name="edit_name" required="true" placeholder="Type brand name...">
                <div id="edit-error-msg" class="alert alert-warning" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
            </div>
            <div class="col-md-6 form-group">
                <label>{{trans('file.bankcontact')}} </label>
                <input type="text" class="form-control" id="edit_contact" name="edit_contact" placeholder="Type bank contact...">
            </div>
        
              <br>
          <button onclick="ValidateSubmit();" class="btn btn-primary"> {{trans('file.submit')}}</button>
               
          </div>
          </form>
        </div>
      </div>
  </div>
  
  

@foreach($brands as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->title }}">
@endforeach


@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">

     $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #bank-menu").addClass("active");

// New Javascript Added 

    var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });
    function ValidateSubmit(){
        if($('#edit_name').val() == ''){
            var value = "Please enter bank name ...";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            return false;
        }
        else if(jQuery.inArray($('#edit_name').val(),myArray) !== -1){
            var value = "Bank already exists. Please use different name.";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            return false;
        }else{
            $(".edit_error_msg").css('display','none');
           $('#bank-form').serialize();
           $('#bank-form').submit();

        }
    }
// Brand Create
$("#DocUpdate").click(function(e){
        e.preventDefault();
        $(".print-error-msg").css('display','none');
        if($('#bank_name').val() == ''){
            var value = "Please enter bank name ...";
            $(".print-error-msg").css('display','block');
            $(".print-error-msg").text(value);
            return false;
        }
        else if(jQuery.inArray($('#bank_name').val(),myArray) !== -1){
            var value = "Bank already exists. Please add different one.";
            $(".print-error-msg").css('display','block');
            $(".print-error-msg").text(value);
            return false;
        }else{
            $(".print-error-msg").css('display','none');
        }
        var data = $('#kt_modal_new_address_form_update').serialize();

        $.ajax({
            url:"{{ route('bank.store')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Bank added successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        location.reload();
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });


$('.DeletebrandDialog').click(function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure you want to delete ?",
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
                    url: "{{url('bank/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({
                                    title: "Success",
                                    text: "Bank "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    location.reload();
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
    //End
    var brand_id = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".open-EditbrandDialog", function(){
          var url= "{{url('/bank/edit')}}/";
          var id = $(this).data('id').toString();
          url = url.concat(id);

          $.get(url, function(data){
            $("#editBrandModal input[name='edit_id']").val(data['id']);
            $("#editBrandModal input[name='pre_name']").val(data['title']);
            $("#editBrandModal input[name='edit_name']").val(data['title']);
            $("#editBrandModal input[name='edit_contact']").val(data['contact']);
          
          });
          $("#editBrandModal").modal('show');
    });

    $( "#select_all" ).on( "change", function() {
        if ($(this).is(':checked')) {
            $("tbody input[type='checkbox']").prop('checked', true);
        }
        else {
            $("tbody input[type='checkbox']").prop('checked', false);
        }
    });


    $('#biller-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 3]
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
                text: '<button  style="color:#1a0000; border:none; background-color:inherit;"> Delete Selected <i title="delete selected" class="dripicons-cross"></i> </button>',
                className: 'btn btn-warning',
                action: function ( e, dt, node, config ) {
                   
                        brand_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                brand_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(brand_id.length ) {
                            swal.fire({ 
                            title: "Are you sure, you want to delete selected records ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!",
                            closeOnConfirm: false,
                            preConfirm: function(result) {
                            console.log(brand_id);
                            $.ajax({
                                type:'POST',
                                url:"{{ route('bank.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                    brandIdArray: brand_id,
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
                                            location.reload();
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
                        else if(!brand_id.length)
                        {
                            alert('No bank is selected!');
                        }
                        else
                        {
                            alert('You cancelled the action !');
                        }
                      
                    
                   
                }
            },
          
        ],
    } );

</script>
@endpush
