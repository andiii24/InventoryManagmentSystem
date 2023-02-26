<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{

    public function index()
    {
        $lims_brand_all = Brand::where('is_active',true)->get();
        return view('brand.create', compact('lims_brand_all'));
    }

    public function store(Request $request)
    {
        $request->title = preg_replace('/\s+/', ' ', $request->title);
        $this->validate($request, [
            'title' => [
                'max:255',
                    Rule::unique('brands')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $Brand = new Brand();
        $Brand->title = $request->title;
        $Brand->is_active = true;
        $Brand->save();
        return redirect('brand')->with('message', 'Brand added successfully!');
    }

    public function edit($id)
    {
        $lims_brand_data = Brand::findOrFail($id);
        return $lims_brand_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => [
                'max:255',
                    Rule::unique('brands')->ignore($request->brand_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $lims_brand_data = Brand::find($request->brand_id);
        $lims_brand_data->title = $request->title;
        $lims_brand_data->save();
        return redirect('brand')->with('message', 'Brand updated successfully!');
    }

    public function deleteBySelection(Request $request)
    {
        $brand_id = $request['brandIdArray'];
        foreach ($brand_id as $id) {
            $lims_brand_data = Brand::findOrFail($id);
            $lims_brand_data->is_active = false;
            $lims_brand_data->save();
        }
        return redirect('brand')->with('not_permitted', 'Selected records deleted successfully!');
    }

    public function destroy($id)
    {
        $lims_brand_data = Brand::findOrFail($id);
        $lims_brand_data->is_active = false;
        $lims_brand_data->save();
        return redirect('brand')->with('not_permitted', 'Brand deleted successfully!');
    }

    public function exportBrand(Request $request)
    {
        $lims_brand_data = $request['brandArray'];
        $csvData=array('Brand Title, Image');
        foreach ($lims_brand_data as $brand) {
            if($brand > 0) {
                $data = Brand::where('id', $brand)->first();
                $csvData[]=$data->title.','.$data->image;
            }   
        }        
        $filename=date('Y-m-d').".csv";
        $file_path=public_path().'/downloads/'.$filename;
        $file_url=url('/').'/downloads/'.$filename;   
        $file = fopen($file_path,"w+");
        foreach ($csvData as $exp_data){
          fputcsv($file,explode(',',$exp_data));
        }   
        fclose($file);
        return $file_url;
    }
}
