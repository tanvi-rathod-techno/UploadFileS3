<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function uploadimage(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if ($validator->fails()) {
        return [
            'code' => config('constant.VALIDATION_ERROR_CODE'),
            'status' => false,
            'message' => 'Validation errors',
            'result' => $validator->errors()
        ];
        }    
        $image = $request->file('image');
        $imageName = $image->getClientOriginalName(); // $imageName = $request->image->extension();  
        $path = Storage::disk('s3')->put('images', $request->image);
        $path = Storage::disk('s3')->url($path);
        $data =[];
        $data['imagename'] = $imageName;
        $data['imagepath'] = $path;
        if(empty($data)){
            return response()->json(['code'=>404,'status'=>false,'message'=>'Data not found','response'=>NULL]);
        } else{
            return response()->json(['code'=>200,'status'=>true,'message'=>'Image upload successfully','response'=>$data]);
        }
        }catch (Exception $ex) {
        return response()->json(['code'=>401,'false'=>true,'message'=>'Invalid File, Please try again.','response'=>NULL]);
        }
    }

    public function send_email(){
        $test= env('MAIL_HOST', 'smtp.mailgun.org');
        // dd($test);
        $recipient = 'trushang@technostacks.in';
        $subject = 'Sample Email Subject';
        $message = 'This is a sample email sent using Amazon SES and Laravel.';

        Mail::raw($message, function ($email) use ($recipient, $subject) {
            $email->to($recipient)->subject($subject);
        });

        return 'Sample email sent successfully!';
    } 
}
 