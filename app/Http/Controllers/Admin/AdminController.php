<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Hash;
use Image;
use App\Models\Admin;
use Session;

class AdminController extends Controller
{
    public function dashboard(){
        Session::put('page','dashboard');
        return view('admin.dashboard');
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|max:30'
            ];
            $customMessages = [
                'email.required' => "Email is required",
                'email.email' => "Valid email is required",
                'password.required' => "Password is required"
            ];
            $this->validate($request,$rules,$customMessages);
            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
                return redirect("admin/dashboard");
            }else{
                return redirect()->back()->with("error_message","Invalid email or password!");
            }
        }
        return view('admin.login');
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function updatePassword(Request $request){
        Session::put('page','update-password');
        if($request->isMethod('post')){
            $data = $request->all();
            if (Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)) {
                if($data['new_pwd']==$data['confirm_pwd']){
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_pwd'])]);
                    return redirect()->back()->with('success_message','Password has been updated successfully!');
                }else{
                    return redirect()->back()->with('error_message','New password & re-type password are incorrect!');
                }
            } else {
                return redirect()->back()->with('error_message','Your current password is incorrect!');
            }      
        }
        return view('admin.update_password');
    }

    public function checkCurrentPassword(Request $request){
        $data = $request->all();
        if (Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updateDetails(Request $request){
        Session::put('page','update-details');
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $rules = [
                'admin_name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'admin_mobile' => 'required|numeric|digits:10',
                'admin_image' => 'image'
            ];
            $customMessages = [
                'admin_name.required' => "Name is required",
                'admin_name.regex' => "Valid name is required",
                'admin_name.max' => "Valid name is required",
                'admin_mobile.required' => "Mobile is required",
                'admin_mobile.numeric' => "Vaild mobile is required",
                'admin_mobile.digits' => "Vaild mobile is required",
                'admin_image.image' => "Vaild image is required",
            ];
            $this->validate($request,$rules,$customMessages);
            if ($request->hasFile('admin_image')) {
                $imageTmp = $request->file('admin_image');
                if ($imageTmp->isValid()) {
                    $extension = $imageTmp->getClientOriginalExtension();
                    $imageName = rand(111, 99999).'.'.$extension;
                    $imagePath = 'admin/images/photos/'.$imageName;
                    Image::make($imageTmp)->save($imagePath);
                }
            }else if(!empty($data['current_image'])){
                $imageName = $data['current_image'];
            }else{
                $imageName ="";
            }
            Admin::where('email',Auth::guard('admin')->user()->email)->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            return redirect()->back()->with('success_message','Admin details has been updated successfully!');
        }
        return view('admin.update_details');
    }
}
