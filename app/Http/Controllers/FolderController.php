<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Redirect;
use Response;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $folder = Folder::all()->toArray();
        return array_reverse($folder);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $folder = new Folder([
            'title' => $request->title,
            'isFolder' => $request->isFolder,
            'parent' => $request->parent,
            'cabinet_id' =>$request->cabinet_id,
            'path' => $request->path,
        ]);
        $folder->save();

        return response()->json('The folder successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $folder =  DB::table('folders')->select("folders.*")
            //->join('systeme_tarifaires', 'systeme_tarifaires.uid', '=', 'abonnes.systeme_tarifaire_uid')
            ->where('folders.cabinet_id', $id)
            ->get();
            return $folder;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit(Folder $folder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Folder $folder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Folder $folder)
    {
        //
    }
    public function createDirectory(Request $request)

{

    $path = public_path('upload/file.txt');

   

    if(!File::isDirectory($path)){

        File::makeDirectory($path, 0777, true, true);

    }

   

    dd('done');

}

public function sendEmail(Request $request) {

   
                    // send email with the template
                    $mailToSend = [$request->email];
                    $data['msg'] = $request->msg;
                    $data['path'] = $request->path;
                    return EmailHelper::sendMail(
                        'emails.upload',
                        $data,
                        $request->email,
                        'Société anonyme', $mailToSend);
            

}

public function uploadFile(Request $request) {
    $file = $request->file('file');
    $filename = str_replace(' ', '_', $request->file('file')->getClientOriginalName());
    $path = public_path() . '/'. $request->path;
    $file->move($path, $filename);

    $data ['filename'] = $request->file('file')->getClientOriginalName(); // pathinfo($filename, PATHINFO_FILENAME);
    $data ['fileExtension'] = $file->getClientOriginalExtension();
    $key = env('ADMINEMAIL');
    $societe = env('SOCIETENAME');
                    // send email with the template
                    $mailToSend = [$key];
                    $data['msg'] = $request->msg;
                    $data['path'] = $request->path. '/'.  $request->file('file')->getClientOriginalName();
                    return EmailHelper::sendMail(
                        'emails.upload',
                        $data,
                        $key,
                        $societe, $mailToSend);
            
                   if ( count(Mail::failures()) > 0) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
                    ]);   }else{
    
                        return response()->json([
                            'success' => true,
                            'message' => 'Un email de confirmation vous est envoyez !'
                        ]);
                    }  
}


public function downloadFile($path) {
    $file = public_path().'/'. str_replace('|', '/', $path);///. '.pdf';
    $headers = array('Content-Type: application/pdf',);

    if (is_file($file)) {
        return Response::make(file_get_contents($file));
    } else {
        return;
    }


    //return Response::download($file, 'cv.pdf', $headers);
    //return Response::download($file);
   
}

public function showPdf($path) {
    $file = public_path().'/'. str_replace('|', '/', $path. '.pdf');///. '.pdf';
    $headers = array('Content-Type: application/pdf',);
    if (is_file($file)) {
        return Response::make(file_get_contents($file));
    } else {
        return;
    }

}

public function modifyFile() {
    rename(public_path('/images/player_icons/Ajax.png'), public_path('/images/player_icons/test.png'));
}
public function findFolder() {
    //$files = Storage::disk('public')->files($directory);

// Recursive...
$files =  Storage::disk('public')->exists('Dossier_1');
return  $files;

}


public function updateFolder($id, Request $request)
{ 
    $input = $request->all();                     
      $obj_user = Folder::find($id);
      if($request->exists('title')) { 
        $obj_user->title =  $input['title'];
        $obj_user->path =  $input['path'];
        $obj_user->save();
        rename(public_path($input['oldpath']), public_path($input['path']));
    
          
      } else {
        $obj_user->path =  $input['path'];
        $obj_user->save();
        //rename(public_path($input['oldpath']), public_path($input['path']));
    
      }

    $success = true;
    $message = 'Folder update successfully';
    $response = [
        'success' => $success,
        'message' => $message,
    ]; 
    return response()->json($message);
}
public function deleteFolder($id) {
    $folder = Folder::find($id);
    $folder->delete();
    Folder::where('parent',$id)->delete();

    return response()->json('The folder successfully deleted');


}
public function deleteFolderCabinet($id) {
    Folder::where('cabinet_id',$id)->delete();

    return response()->json('The folder successfully deleted');


}
public function removeFolder(Request $request) {
    $input = $request->all();   
    $folderPath = public_path($request->path);
    if($request->isFolder == 0) {
        File::delete(public_path($request->path));
        return response()->json('The folder successfully deleted'); 
    
    } else {
        File::deleteDirectory(public_path($request->path));
        return response()->json('The folder successfully deleted'); 
    
    }



}
}
