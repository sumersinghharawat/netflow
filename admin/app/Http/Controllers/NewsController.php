<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsNews;
use App\Models\News;
use App\Models\Notification;
use App\Traits\ImageUpload;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class NewsController extends Controller
{
    use ImageUpload;
    use UploadTraits;


    public function index()
    {
        $news = News::cursor();

        return view(
            'admin.tools.news.index',
            compact('news')
        );
    }

    public function add()
    {
        return view(
            'admin.tools.news._add'
        );
    }

    public function addNews(RequestsNews $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            DB::beginTransaction();
            $news = new News;
            $news->title = $request->title;
            $news->description = $request->description;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $folder = 'news';
                $prefix = 'new';
                if (!$this->singleFileUpload($file, $news, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'file upload failed.',
                        ], 400);
                    }

                    return redirect()->back()->withErrors('file upload failed.');
                }
            } else {
                $news->image = asset('assets/images/icons/no-image.png');
                $news->save();
            }
            DB::commit();
            $this->sendNewsNotification($news->title);

            return redirect('admin/news')->with(
                'success',
                'News added successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()
                ->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $news = News::find($id);

        return view(
            'admin.tools.news._edit',
            compact('news')
        );
    }

    public function delete(Request $request, $id)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        DB::beginTransaction();
        try {
            $news = News::find($id);
            $host = request()->getSchemeAndHttpHost();
            $image = str_replace($host, '', $news->image);
            $publicPath = public_path();
            if (File::exists($publicPath . $image)) {
                File::delete($publicPath . $image);
            }
            $news->delete();
            DB::commit();
            return back()
                ->with('success', 'record deleted successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function update(RequestsNews $request, $id)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        try {
            $new = News::find($id);
            $new->title = $request->title;
            $new->image = $new->image;
            $new->description = $request->description;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $folder = 'news';
                $prefix = 'new';
                $filename = $prefix . '-' . $new->id . time() . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($folder, $file, $filename);
                $host = request()->getSchemeAndHttpHost();
                $image = str_replace($host, '', $new->image);
                $publicPath = public_path();
                if (File::exists($publicPath . $image)) {
                    File::delete($publicPath . $image);
                }
                $new->image = $host . "/storage/$folder/$filename";
            }
            $new->push();
            DB::commit();

            return redirect('admin/news/')
                ->with('success', 'record updated successfully.');
        } catch (\Exception $e) {
            return redirect('admin/news/')
                ->with('error', $e->getMessage());
        }
    }

    public function sendNewsNotification($newsTitle)
    {
        $newsData = [
            "type"      => "news_added",
            "title"     => "news_added_title",
            "heading"  => $newsTitle,
            "icon"      => '<i class="fa fa-newspaper-o"></i>'
        ];

        $notify = new Notification();
        $notify->id = (string) Str::uuid();
        $notify->type = 'News Notification';
        $notify->notifiable_type = 'App\Models\User';
        $notify->notifiable_id = 0;
        $notify->data = json_encode($newsData);
        $notify->save();
        return true;
    }
}
