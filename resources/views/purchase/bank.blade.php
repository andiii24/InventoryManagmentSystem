@extends('layout.main') @section('content')

@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if($errors->has('image'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> Add Bank</button>&nbsp;
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Name</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</section>


    <!-- Edit Modal -->
    <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form class="form" id="form-update-bank" >
            <div class="modal-header">
                <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-bank"></i> Update Bank</h3>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body" overflow-hidden>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="edit_id">
                            <input type="hidden" id="pre_name" name="pre_name">
                            <label><strong> Bank Name * </strong> </label>
                            <input type="text" class="form-control"  value=""  id="bname" name="edit_bank_name" required="true" placeholder="Enter bank name ...">
                            <div id="edit_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                        </div>
                    </div> 

                    <div  class="col-md-6">
                        <div class="form-group" style="margin-left: 50px; margin-top: 30px;">
                            <input id="updateBank"  type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                 </div>
                    

                
            </div>
        </form>
        </div>
        </div>
    </div>
        <!-- Create Modal -->
    <div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="form" class="modal-dialog">
        <div class="modal-content">
            <form class="form" id="form-add-bank" >
            <div class="modal-header">
                <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-bank"></i> Add Bank Used For Purchase Payments</h3>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body overflow-hidden">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong> Bank Name * </strong> </label>
                            <input type="text" value="" class="form-control"   id="kname" name="bank_name" required="true" placeholder="Enter bank name ...">
                            <div id="name_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                        </div>
                    </div>
                    <div  class="col-md-6">
                        <div class="form-group" style="margin-left: 20px; margin-top: 30px;">
                            <input id="createBank"  type="submit" value="Add Bank" class="btn btn-primary">
                        </div>
                    </div>  
                </div>

                
            </div>
        </form>
        </div>
        </div>
    </div>
@foreach($banks as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->name }}">
@endforeach
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #purchase-bank-menu").addClass("active");
    var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });
    $("#createBank").click(function(e){
        e.preventDefault();
        var bank = $('#kname').val();
        if ($('#kname').val() == "" ){
            var value = "Please enter bank name ...";
            $("#name_error_msg").css('display','block');
            $("#name_error_msg").text(value);
            $("#kname").css('borde-color','red');
            return false;
        }
       else if($.inArray(bank,myArray) !== -1){
            var value = "Bank  already exists ! Please add different one";
            $("#name_error_msg").css('display','block');
            $("#name_error_msg").text(value);
            $("#kname").css('borde-color','red');
            return false;
        }
        else{ 
            $("#name_error_msg").css('display','none');
        }
        var data = $('#form-add-bank').serialize();

        $.ajax({
            url:"{{ route('purchase.bank.add')}}",
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
                        window.location.href='{{ route('purchases.bank') }}';
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });

    $("#updateBank").click(function(e){
        e.preventDefault();
        if ($('#bname').val() == '' ){
            var value = "Please enter bank name ...";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            $("bname").css('borde-color','red');
            return false;
        }
        else if($('#bname').val() != $('#pre_name').val() && jQuery.inArray($('#bname').val(),myArray) !== -1){
            var value = "Bank already exists ! Please add different one";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            $("#bname").css('borde-color','red');
            return false;
        }
        
        else{
            $("#edit_error_msg").css('display','none');
        }
        var data = $('#form-update-bank').serialize();

        $.ajax({
            url:"{{ route('purchase.bank.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Bank updated successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.bank') }}';
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });
    $(document).on("click", ".DeleteCategoryDialog", function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure want to delete "+name+"?",
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
                    url: "{{url('/purchase/bank/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({ 
                                    title: "Success",
                                    text: "Bank "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    window.location.href='{{ route('purchases.bank') }}';
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
        var id = $(this).data('id');
        var name = $(this).data('name');
        $("#editModal input[name='edit_id']").val(id);
        $("#editModal input[name='edit_bank_name']").val(name);
        $("#editModal input[name='pre_name']").val(name);
        $("#editModal").modal('show');
    });

    $('#category-table').DataTable( {
        responsive: true,
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"{{ route('purchase.bank.data')}}",
            data:{
                "_token" : "{{ csrf_token() }}",
                },
            dataType: "json",
            type:"post"
        },
        "createdRow": function( row, data, dataIndex ) {
            $(row).attr('data-id', data['id']);
        },
        "columns": [
            {"data": "key"},
            {"data": "name"},
            {"data": "user"},
            {"data": "date"},
            {"data": "options"}
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
        order:[['1', 'asc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 2, 3,4]
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
                                category_id[i-1] = $(this).closest('tr').data('id');
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
                                url:"{{ route('purchase.bank.deleteselected')}}",
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
                                            window.location.href='{{ route('purchases.bank') }}';
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
                            alert('No bank is selected!');
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
                      

</script>
@endpush