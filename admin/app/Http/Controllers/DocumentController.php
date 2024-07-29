<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsDocumentAddnew;
use App\Models\Document;
use App\Models\Notification;
use App\Models\UploadCategory;
use App\Traits\ImageUpload;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DocumentController extends Controller
{
    use UploadTraits;
    use ImageUpload;

    public function index()
    {
        $material = Document::with('category')->paginate(6);

        return view(
            'admin.tools.uploadMeterial.index',
            compact('material')
        );
    }

    public function add()
    {
        $category = UploadCategory::get();

        return view(
            'admin.tools.uploadMeterial._add',
            compact('category')
        );
    }

    public function addnew(RequestsDocumentAddnew $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            DB::beginTransaction();
            $document = new Document();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $folder = 'material';
                $prefix = 'DOC';
                if (!$this->documentUpload($file, $document, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'file upload failed.',
                        ], 400);
                    }

                    return redirect()->back()->withErrors('file upload failed.');
                }
            }

            $document->cat_id = $request->category;
            $document->file_title = $request->title;
            $document->file_description = $request->description;
            $document->save();

            DB::commit();
            $this->sendMaterialUploadedNotification($request->title);

            return redirect(route('material'))
                ->with('success', 'Material Uploaded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $document = Document::find($id);
            Storage::delete('/public/uploads/materials/' . $document->file_name);
            $document->delete();

            return back()
                ->with('success', 'record deleted successfully.');
        } catch (\Throwable $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function sendMaterialUploadedNotification($materialTitle) {

        $newsData = [
            "type"      =>"material_added",
            "title"     =>"material_added_title",
            "heading"   => $materialTitle,
            "icon"      =>'<i class="fa fa-file-text-o"></i>'
        ];

        $notify = new Notification();
        $notify->id = (string) Str::uuid();
        $notify->type = 'Material Uploaded Notification';
        $notify->notifiable_type = 'App\Models\User';
        $notify->notifiable_id = 0;
        $notify->data = json_encode($newsData);
        $notify->save();
        return true;
    }
}
