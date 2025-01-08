<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\Controller;
use App\Models\Ratings;
use Illuminate\Http\Request;

class RatingWebController extends RatingController
{
    public function getAll(Request $request)
    {
        $ratings = parent::getAllRating($request);
        return view('rating.index', compact('ratings'));
    }
    public function deleteRating($id)
    {
        $rating = Ratings::find($id);
        if ($rating) {
            $rating->delete();
            return redirect()->route('rating.index')->with('success', 'Rating deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Rating could not be found or deleted!');
        }
    }
}
