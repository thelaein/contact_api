<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $contact = Contact::get()->each(function ($contact){
//            if ($contact->photo === null){
//                $contact->photo = asset('Sample_User_Icon.png');
//            }else{
//                $contact->photo = asset('storage/photo/'.$contact->photo);
//            }
//            $contact->makeHidden(['created_at','updated_at']);
//            $contact->date = $contact->created_at->format("d M Y");
//            $contact->time = $contact->created_at->format("h:i:s");
//
//        });
        $contact = ContactResource::collection(Contact::all());

        return response()->json([
            'message'=>'success',
            'data'=>$contact,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'photo'=>'nullable|file|mimes:jpeg,png|max:2000',
        ]);
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->phone = $request->phone;

        if ($request->hasFile('photo')){
            $newname = 'photo_'.uniqid().".".$request->file('photo')->extension();
            $request->file('photo')->storeAs('public/photo',$newname);
            $contact->photo = $newname;
        }
        $contact->save();
//        $contact = Contact::create($request->all());
        return response()->json([
            'message'=>'success',
            'data'=>new ContactResource($contact),
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Contact::find($id);
        if (is_null($contact)){
            return response()->json([
                'message'=>'fail',
            ],404);
        }

        return response()->json([
            'message'=>'success',
            'data'=>new ContactResource($contact),
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
//        return $request;
        $request->validate([
            'name'=>'nullable|min:3',
            'phone'=>'nullable|min:8|max:20',
            'photo'=>'nullable|file|mimes:jpeg,png|max:2000',
        ]);
        $contact = Contact::find($id);
        if (is_null($contact)){
            return response()->json([
                'message'=>'fail'
            ],404);
        }
        if ($request->name){
            $contact->name = $request->name;
        }
        if ($request->phone){
            $contact->phone = $request->phone;
        }
        if ($request->hasFile('photo')){
            Storage::delete('public/photo'.$contact->photo);
            $newName = 'photo_'.uniqid().".".$request->file('photo')->extension();
            $request->file('photo')->storeAs('public/photo',$newName);
            $contact->photo = $newName;
        }

        $contact->update();


        return response()->json([
            'message'=>'update success',
            'data'=>new ContactResource($contact),
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return response()->json([
            'message'=>'deleted',
        ],204);
    }
}
