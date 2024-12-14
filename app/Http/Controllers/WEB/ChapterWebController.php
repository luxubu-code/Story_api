<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Image;
use Illuminate\Http\Request;

class ChapterWebController extends Controller
{
    public function showImages($id)
    {
        $chapter = Chapter::with('images')->findOrFail($id);
        return view('chapters.images', compact('chapter'));
    }
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $image->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'nullable|image|max:2048', // Validate new image
        ]);

        $image = Image::findOrFail($id);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('images');
            $image->file_path = $filePath;
        }

        $image->save();

        return redirect()->route('chapters.images', $image->chapter_id)->with('success', 'Image updated successfully.');
    }
}