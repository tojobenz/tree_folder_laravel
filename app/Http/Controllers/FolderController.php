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
use App\Http\Requests\UploadFileRequest;

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

public function uploadFile(UploadFileRequest $request) {
    try {
        $file = $request->file('file');
        
        $filename = $request->sanitizeFilename($file->getClientOriginalName());
        
        $path = $request->sanitizePath($request->path);
        $fullPath = public_path() . $path;

        // repository creation 
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true, true);
        }

        // move file
        $file->move($fullPath, $filename);

        $data['filename'] = $file->getClientOriginalName();
        $data['fileExtension'] = $file->getClientOriginalExtension();
        $data['filePath'] = $path . '/' . $filename;

        $key = env('ADMINEMAIL');
        $societe = env('SOCIETENAME', 'Ma Société');
        
        if (!empty($key) && filter_var($key, FILTER_VALIDATE_EMAIL)) {
            try {
                $mailToSend = [$key];
                $data['msg'] = $request->get('msg', '');
                $data['path'] = $data['filePath'];
                
                EmailHelper::sendMail(
                    'emails.upload',
                    $data,
                    $key,
                    $societe,
                    $mailToSend
                );
            } catch (\Exception $e) {
                // Log upload failed
                \Log::error('Email sending failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Fichier uploadé avec succès',
            'data' => [
                'filename' => $data['filename'],
                'path' => $data['filePath']
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
        ], 500);
    }
}


public function downloadFile($path) {
    $sanitizedPath = str_replace('|', '/', $path);
    $file = public_path() . '/' . $sanitizedPath;

    if (!is_file($file)) {
        return response()->json([
            'success' => false,
            'message' => 'Fichier non trouvé'
        ], 404);
    }

    $mimeType = mime_content_type($file);
    if ($mimeType === false) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
        ];
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    $fileName = basename($file);

    return response()->file($file, ['Content-Type' => $mimeType]);
}

public function showPdf($path) {
    $sanitizedPath = str_replace('|', '/', $path);
    $file = public_path() . '/' . $sanitizedPath;

    if (!is_file($file)) {
        return response()->json([
            'success' => false,
            'message' => 'Fichier non trouvé'
        ], 404);
    }

    $mimeType = mime_content_type($file);
    if ($mimeType === false) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
        ];
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    return response()->file($file, ['Content-Type' => $mimeType]);
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
    }

    return response()->json([
        'success' => true,
        'message' => 'Dossier mis à jour avec succès'
    ]);
}

public function deleteFolder($id) {
    $folder = Folder::find($id);
    $folder->delete();
    Folder::where('parent',$id)->delete();

    return response()->json([
        'success' => true,
        'message' => 'Dossier supprimé avec succès'
    ]);
}

public function deleteFolderCabinet($id) {
    Folder::where('cabinet_id',$id)->delete();

    return response()->json([
        'success' => true,
        'message' => 'Dossiers du cabinet supprimés avec succès'
    ]);
}

public function removeFolder(Request $request) {
    $input = $request->all();   
    $folderPath = public_path($request->path);
    if($request->isFolder == 0) {
        File::delete(public_path($request->path));
        return response()->json([
            'success' => true,
            'message' => 'Fichier supprimé avec succès'
        ]); 
    } else {
        File::deleteDirectory(public_path($request->path));
        return response()->json([
            'success' => true,
            'message' => 'Dossier supprimé avec succès'
        ]); 
    }
}
}
