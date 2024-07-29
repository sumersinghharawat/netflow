<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestsFaq;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faq = Faq::AscOrder()->paginate(6);

        return view(
            'admin.tools.Faq.index',
            compact('faq')
        );
    }

    public function create(RequestsFaq $request)
    {
        try {
            $data = [
                'sort_order' => $request->sort_order,
                'question' => $request->question,
                'answer' => $request->answer,
                'status' => 1,
            ];
            Faq::insert($data);

            return back()
                ->with('success', 'record created successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            Faq::find($id)->delete();

            return back()
                ->with('success', 'record deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function checkSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'numeric|unique:faqs|max_digits:4',
        ]);
    }
}
