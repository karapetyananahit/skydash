<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use Illuminate\Http\Request;

class InfluencersCardsController extends Controller

{
    public function index()
    {
        $influencers = Influencer::with('socialMedias')->limit(6)->get();
        return view('influencers-cards.index', compact('influencers'));
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $influencers = Influencer::where('name', 'like', '%' . $search . '%')->with('socialMedias')->get();
            return view('influencers-cards.partials.influencers-cards', compact('influencers'));
        } catch (\Exception $e) {
            \Log::error('Error searching influencers: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while searching for influencers.'], 500);
        }
    }

    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = 6;

        $influencers = Influencer::with('socialMedias')->offset($offset)->limit($limit + 1)->get();
        $hasMore = $influencers->count() > $limit;
        $influencers = $influencers->take($limit);
        if ($influencers->isEmpty()) {
            return response()->json(['html' => '', 'hideButton' => true]);
        }
        $html = view('influencers-cards.partials.influencers-cards', compact('influencers'))->render();
        return response()->json(['html' => $html, 'hideButton' => !$hasMore]);
    }

}
