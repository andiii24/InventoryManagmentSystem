<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Bank;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class BankController extends Controller
{
    //
    public function brand()
    {
            $brands = Bank::where('is_active',true)->orderBy('title', 'asc')->get();
            return view('common.bank',  compact('brands'));

    }
    public function store(Request $request)
    {
        
        $time=Carbon::now();
        $data = $request->all();
        $myvalue = array();
        parse_str($data['data'],$myvalue);
       // $ref_id = $myvalue['brand_name'];
        //dd($myvalue['reference']);
        $Brand = new Bank();
        $Brand->title = $myvalue['bank_name'];
        $Brand->contact = $myvalue['contact'];
        $Brand->is_active = true;
        $Brand->created_at = $time;
        $Brand->save();
        return response()->json(['Success'=>'Brand Added Successfully']);

    }
    public function edit($id)
    {
        $lims_category_data = Bank::findOrFail($id);
        return $lims_category_data;
    }
    public function update(Request $request)
    {
        $request->title = preg_replace('/\s+/', ' ', $request->title);
        $this->validate($request, [
            'title' => [
                'max:255',
                    Rule::unique('bank')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $time=Carbon::now();
        $ref_id= $request->edit_id;
        //dd($myvalue['reference']);
        $Brand = Bank::find($ref_id);
        $Brand->title = $request->edit_name;
        $Brand->contact = $request->edit_contact;
        $Brand->updated_at = $time;
        $Brand->save();
        return redirect('banks')->with('message', 'Bank updated successfully!');

    }
    public function delete($id)
    {
        $Export = Bank::findOrFail($id);
        $Export->is_active = false;
        $Export->save();
            $success = true;
        return response()->json([
            'success' => $success
        ]);

    }
    public function deleteBySelection(Request $request)
    {
        foreach ($request['brandIdArray'] as $id) { 
           $cat= Bank::findOrFail($id);
           $cat->is_active = false;
           $cat->save();
           }
       return response()->json(['Success'=>'Bank Deleted Successfully']);
    }




 

}
   