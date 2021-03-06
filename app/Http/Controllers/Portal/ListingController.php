<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{

    public function index()
    {
        $user_id        = Auth::user()->id;

        $listings       = Listing::select('*')
            ->where('user_id', $user_id)
            ->get();

        return view('portal.listing.index', ['listings' => $listings]);
    }

    public function create()
    {

        $categories     = Category::select('*')->get();

        return view('portal.listing.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        // Validate Request
        $request->validate([
            'title'     => 'required',
            'address'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'phone'     => 'required',
            'email'     => 'required',
            'website'   => 'required',
            'category_id'   => 'required|integer|exists:categories,id'
        ]);
        $user_id        = Auth::user()->id;

        // Get form data as array

        //GET ALL DATA
        $data           = $request->all();
        $data['user_id'] = $user_id;
        // GET ALL DATA EXCEPT SPEICIFIED DATA
        // $data           = $request->except('_token');

        // GET ONLY DATA SPECIFIED
        // $data           = $request->only(['title', 'address', 'latitude']);
        // dd($data);

        //CREATE METHOD LONGHAND
        // $listing        = Listing::create([
        //     'title'     => $data['title'],
        //     'address'   => $data['address'],
        //     'latitude'  => $data['latitude'],
        //     'longitude' => $data['longitude'],
        //     'phone'     => $data['phone'],
        //     'email'     => $data['email'],
        //     'website'   => $data['website'],
        // ]);

        //CREATE METHOD SHORTHAND
        // $listing            = Listing::create($data);

        // //SAVE METHOD 
        // // -- create empty object
        // $listing            = new Listing();

        // // -- Inject data to object 
        // $listing->title     = $data['title'];
        // $listing->address   = $data['address'];
        // $listing->latitude  = $data['latitude'];

        // // -- Save object to db 
        // $listing->save();

        //INSERT METHOD 
        // $listing               = Listing::insert($data);

        // MY CHOICE
        $listing                = Listing::create($data);

        // If create success
        if ($listing) {

            return redirect(route('portal.listing.index'));
        }

        flash('Sorry Something went wrong!', 'notification reject');
    }

    public function edit($id)
    {
        //FIND
        //FINDORFAIL 
        //WHERE 
        $listing        = Listing::findOrFail($id);

        return view('portal.listing.edit', ['listing' => $listing]);
    }

    public function update(Request $request, $id)
    {

        //CHECK IF ID EXIST IN DB
        $listing        = Listing::findOrFail($id);

        // VALIDATE SUBMITTED DATA
        $validate = $request->validate([
            'title'     => 'required',
            'address'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'phone'     => 'required',
            'email'     => 'required',
            'website'   => 'required'
        ]);

        //GET ALL FORM INPUT
        $data           = $request->all();

        // CHECK IF HAS IMAGE FILE
        if ($request->file('new_image')) {
            // GET original file name
            $imageName = $request->file('new_image')->getClientOriginalName();

            // Move file to uploads directory
            $request->file('new_image')->move(public_path('uploads'), $imageName);

            // DELETE OLD IMAGE
            @unlink(public_path('uploads/' . $listing->image));

            //SET image to uploaded file 
            $data['image'] = $imageName;
        }

        // SAVE METHOD 
        // $listing->title     = $data['title'];
        // $listing->address   = $data['address'];
        // $listing->save();

        // UPDATE METHOD 
        $listing->update($data);

        if ($listing) {
            flash('Updated Successfully! ', 'notification success');

            return redirect(route('portal.listing.index'));
        }

        flash('Sorry Something went wrong!', 'notification reject');
    }

    public function delete($id)
    {
        $listing        = Listing::findOrFail($id);

        return view('portal.listing.delete', ['listing' => $listing]);
    }

    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();

        if ($listing) {
            flash('Deleted Successfully! ', 'notification success');

            return redirect(route('portal.listing.index'));
        }

        flash('Sorry Something went wrong!', 'notification reject');
    }
}
