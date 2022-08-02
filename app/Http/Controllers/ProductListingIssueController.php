<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListingIssueCollection;
use App\Models\Product;
use App\Models\ProductListingIssue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductListingIssueController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->authorizeResource(ProductListingIssue::class, 'product_listing_issue');
    }

    protected function resourceMethodsWithoutModels()
    {
        return array_merge(parent::resourceMethodsWithoutModels(), ['resolved', 'resolved']);
    }

    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), [
            'resolved' => 'resolved'
        ]);
    }
     

    public function index()
    {
        $request = request()->all();
        $per_page = $request['per_page'] ?? 50;

        $query = ProductListingIssue::filter($request)->orderBy('product_listing_issues.created_at', 'desc');

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }

        return (new ProductListingIssueCollection($query));
    }

    public function resolved(ProductListingIssue $product_listing_issue, Request $request)
    {

        $user = Auth::user();

        $update = ProductListingIssue::where('product_id', $product_listing_issue->product_id)->update([
            'resolved_by' => $user->id,
            'resolved_at' => Carbon::now()
        ]);

        if ($update) {
            return response()->json('', 200);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }
}
