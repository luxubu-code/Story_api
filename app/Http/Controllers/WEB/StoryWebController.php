<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\API\StoryController;
use App\Models\Category;
use App\Models\Story;
use App\Services\ZipFileService;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ImageController;

class StoryWebController extends StoryController
{

    protected $imageController;

    public function __construct(ImageController $imageController)
    {
        $this->imageController = $imageController;
    }
    public function showAll()
    {
        $response = parent::index();
        $storiesArray = json_decode(json_encode($response->getData()->data), true);
        $categories = Category::all();
        return view('stories.index', compact('storiesArray', 'categories'));
    }

    public function store(Request $request)
    {
        $response = parent::store($request);

        if ($response->status() === 200) {
            return redirect()->route('stories.index')->with('success', 'Story added successfully!');
        } else {
            return redirect()->route('stories.index')->with('error', $response->getData()->message);
        }
    }
    public function show($story_id)
    {
        $response = parent::show($story_id);

        $story = json_decode(json_encode($response->getData()->data), true);

        return view('stories.show', compact('story'));
    }
    public function upload(Request $request, $story_id, ZipFileService $zipFileService)
    {
        $response = $this->imageController->upload($request, $story_id, $zipFileService);
        if ($response->status() === 200) {
            return redirect()->route('stories.show', $story_id)->with('success', 'Images uploaded successfully!');
        } else {
            return redirect()->route('stories.show', $story_id)->with('error', $response->getData()->message);
        }
    }
    public function destroyChapter($id)
    {
        $response = $this->imageController->destroy($id);
        if ($response->status() === 200) {
            return redirect()->route('stories.show', $response->getData()->data->story_id)->with('success', 'Chapter deleted successfully!');
        } else {
            return redirect()->route('stories.show', $response->getData()->data->story_id)->with('error', $response->getData()->message);
        }
    }
    public function destroyStory($id){
        $response = parent::destroy($id);   
        if($response->status()==200){
            return redirect()->route('stories.index')->with('success', 'Story deleted successfully!');
        } else {
            return redirect()->route('stories.index')->with('error', $response->getData()->message);
        }

    }
    public function searchStory(Request $request)
    {
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $response = parent::search($request);
            $storiesArray = json_decode(json_encode($response->getData()->data), true);
        } else {
            $storiesArray = [];
        }
        $categories = Category::all();

        return view('stories.index', compact('storiesArray', 'categories'));
    }
}
