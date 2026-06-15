<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Database\Query\Sorter\OrderByKey;
use Throwable;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('profiles.create', [
            'profile' => auth()->user()->profile ?? null
        ]);
    }

    public function store(ProfileRequest $request)
    {
        auth()->user()->profile()->create($request->validated());
        return redirect()->route('client.dashboard')
            ->with('notice', __('Profile created successfully!'));
    }

    public function show()
    {
        $profile = Profile::firstOrCreate([
            'user_id' => auth()->id()
        ]);
        // $states = DB::table('state')
        //     ->get();
        
        // return view('profiles.show', compact('profile','states'));
        $states = DB::table('state')->orderBy('stateName')->get();
        $cities = DB::table('cities')->orderBy('city_name')->get();

        $cityName = null;
        if (!empty($profile->city)) {
            $cityName = $cities->firstWhere('id', (int) $profile->city)->city_name ?? null;
        }

        $stateName = null;
        if (!empty($profile->state)) {
            $stateName = $states->firstWhere('stateId', (int) $profile->state)->stateName ?? null;
        }
        $district_name = null;
        
        $districts = DB::table('districts')
            ->when($profile->state, function ($query) use ($profile) {
                $query->where('state_id', $profile->state);
            })
            ->orderBy('district_name')
            ->get();
         if (!empty($profile->state)) {
            $district_name = $districts->firstWhere('district_id', (int) $profile->district)->district_name ?? null;
        }
        return view('profiles.show', compact('profile', 'states', 'cityName', 'stateName','district_name'));
    }

    public function edit()
    {
        $profile = Profile::firstOrCreate([
            'user_id' => auth()->id()
        ]);
        $states = DB::table('state')
            ->orderBy('stateName')
            ->get();
        $cities = DB::table('cities')
            ->when($profile->state, function ($query) use ($profile) {
                $query->where('state_id', $profile->state);
            })
            ->orderBy('city_name')
            ->get();
        $districts = DB::table('districts')
            ->when($profile->state, function ($query) use ($profile) {
                $query->where('state_id', $profile->state);
            })
            ->orderBy('district_name')
            ->get();
        return view('profiles.edit', compact('profile','states', 'cities', 'districts'));
    }

    // public function profileEdit($id)
    // {
    //     dd($id);
    //     $profile = Profile::findOrFail($id);
    //     dd($profile);
    //     if ($profile->user_id != auth()->id()) {
    //         abort(403);
    //     }

    //     return view('profiles.client_edit', compact('profile'));
    // }

    public function fetchPincodeDetails(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6',
        ]);

        try {

            $response = Http::withoutVerifying()
                ->timeout(20)
                ->retry(3, 1000)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0'
                ])
                ->get("https://api.postalpincode.in/pincode/" . $request->pincode);

            if (!$response->ok()) {
                return response()->json([
                    'message' => 'Unable to fetch pincode details.'
                ], 422);
            }

            $data = $response->json();

            if (
                empty($data) ||
                !isset($data[0]['Status']) ||
                $data[0]['Status'] !== 'Success' ||
                empty($data[0]['PostOffice'][0])
            ) {
                return response()->json([
                    'message' => 'No records found for this pincode.'
                ], 404);
            }
            // $postOffice = $data[0]['PostOffice'][0];
            $postOffices = collect($data[0]['PostOffice']);
            // $stateName = $postOffice['State'] ?? null;
            $normalizeName = static function ($name) {
                return strtolower(preg_replace('/\s+/', ' ', trim((string) $name)));
            };

            $dedupeSimilar = static function ($values) use ($normalizeName) {
                $result = [];

                foreach ($values as $value) {
                    $value = trim((string) $value);

                    if ($value === '') {
                        continue;
                    }

                    $normalizedValue = $normalizeName($value);
                    $isDuplicate = false;

                    foreach ($result as $existing) {
                        $normalizedExisting = $normalizeName($existing);

                        if ($normalizedValue === $normalizedExisting) {
                            $isDuplicate = true;
                            break;
                        }

                        similar_text($normalizedValue, $normalizedExisting, $similarity);
                         $distance = levenshtein($normalizedValue, $normalizedExisting);

                        if ($similarity >= 80 || $distance <= 2) {
                            $isDuplicate = true;
                            break;
                        }
                    }

                    if (!$isDuplicate) {
                        $result[] = $value;
                    }
                }

                return collect($result)->values();
            };

            $cities = $dedupeSimilar($postOffices
                ->map(fn ($office) => $office['Block'] ?? $office['Name'] ?? null)
                ->filter()
                ->values());

            $districts = $dedupeSimilar($postOffices
                ->pluck('District')
                ->filter()
                ->values());

            $stateName = $postOffices
                ->pluck('State')
                ->filter()
                ->first();              

            $stateId = null;

            if ($stateName) {
                $stateId = DB::table('state')
                    //->where('stateName', $stateName)
                    ->whereRaw('LOWER(TRIM(stateName)) = ?', [strtolower(trim($stateName))])
                    ->value('stateId');
            }

            $data = response()->json([
                'cities' => $cities,
                'districts' => $districts,
                'state_name' => $stateName,
                'state_id'   => $stateId
            ]);

            return $data;

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Pincode service temporarily unavailable.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Profile $profile)
    {
        // Validate that the profile belongs to the authenticated user
        if ($profile->user_id != auth()->id()) {
            abort(403);
        }
         
        $validated = $request->validate([
            'business_type' => 'required|in:individual,partnership,corporation,llc,sole_proprietorship',
            'pan_no' => 'nullable|string|max:20',
            'gst_no' => 'nullable|string|max:30',
            'mobile_no' => 'required|digits:10',
            'whatsapp_no' => 'nullable|digits:10',
            'address' => 'required|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:2048',
            'alternative_email' => 'nullable',
            'TAN_no' => 'nullable|string|max:30',
            'trade_name' => 'nullable|string|max:255',
            'city_name' => 'nullable|string|max:150',
            'state_name' => 'nullable|string|max:150',
            'district_name' => 'nullable|string|max:150',
            'pincode' => 'nullable|digits:6',
            'address_2' => 'nullable|string|max:500',
        ]);
        
        // Handle profile image upload if present
        $profileData = $validated;
        
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');

            $filename = time() . '_' . $image->getClientOriginalName();
            $path = public_path('profiles');

            // ensure directory exists
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // check if profile already has an image
            if (!empty($profile->profile_image) && file_exists(public_path($profile->profile_image))) {
                unlink(public_path($profile->profile_image));
            }
            
            // move new file
            $image->move($path, $filename);

            // update path
            $profileData['profile_image'] = 'profiles/' . $filename;
        }
        // =========================================
        // STATE
        // =========================================
         
        if (empty($request->state) && !empty($request->state_name)) {
            $state = DB::table('state')->whereRaw('LOWER(stateName) = ?', [strtolower(trim($request->state_name))])->first();
            if (!$state) {
                $newStateId = (DB::table('state')->max('stateId') ?? 0) + 1;
                DB::table('state')->insert([
                    'stateId'   => $newStateId,
                    'stateName' => trim($request->state_name),
                ]);
                $profileData['state'] = $newStateId;
            } else {
                $profileData['state'] = $state->stateId;
            }
        }
        $stateId = $profileData['state'] ?? $request->state;

        if (!$stateId) {
            return back()->withErrors([
                'state_name' => 'Please select state first.'
            ]);
        }
        // =========================================
        // DISTRICT
        // =========================================
        if (empty($request->district) && !empty($request->district_name)) {
            $district = DB::table('districts')->whereRaw('LOWER(district_name) = ?', [strtolower(trim($request->district_name))])->first();
            if (!$district) {
                $districtId = DB::table('districts')->insertGetId([
                    'state_id'      => $stateId,
                    'district_name' => trim($request->district_name),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $profileData['district'] = $districtId;
            } else {
                $profileData['district'] = $district->district_id;
            }
        }
        // =========================================
        // CITY
        // =========================================
        if (empty($request->city) && !empty($request->city_name)) {
            $city = DB::table('cities')->whereRaw('LOWER(city_name) = ?', [strtolower(trim($request->city_name))])->first();
            if (!$city) {
                $cityId = DB::table('cities')->insertGetId([
                    'state_id'    => $stateId,
                    'district_id' => $profileData['district'] ?? null,
                    'city_name'   => trim($request->city_name),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $profileData['city'] = $cityId;
            } else {
                $profileData['city'] = $city->id;
            }
        }
        $profile->update($profileData);

        $user = Auth::user();
        $user->name = $request->name ?? Auth::user()->name;
        $user->email = $request->email ?? Auth::user()->email;
        $user->short_name = $request->short_name ?? Auth::user()->short_name;
        $user->save();

        // return redirect()->back()->with('notice', __('Profile updated successfully!'));
        return redirect()->route('profile.show', $profile)->with('notice', __('Profile updated successfully!'));
    }

    public function userProfileEdit()
    {
        $user = auth()->user();
        $profile = $user->user_profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);
        
        return view('profiles.userProfileEdit', [
            'profile' => $profile
        ]);
    }

    public function userProfileUpdate(Request $request)
    {
        // Get existing profile OR create new one for this user
        $profile = UserProfile::firstOrNew(['user_id' => auth()->id()]);

        // Validate user input
        $validated = $request->validate([
            'gender' => 'required|in:male,female',
            'mobile_no' => 'required|digits:10',
            'whatsapp_no' => 'nullable|digits:10',
            'address' => 'required|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:2048',
        ]);

        $profileData = $validated;

        // Handle profile image
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = public_path('profiles/'.auth()->id());

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Delete old image if exists
            if ($profile->profile_image && file_exists(public_path($profile->profile_image))) {
                unlink(public_path($profile->profile_image));
            }

            // Save new image
            $image->move($path, $filename);
            $profileData['profile_image'] = 'profiles/' .auth()->id(). '/' . $filename;
        }

        // Assign user_id when creating new
        $profile->user_id = auth()->id();

        // Insert OR Update
        $profile->fill($profileData)->save();

        $user = Auth::user();
        // ✅ Update user table
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('notice', __('Profile updated successfully!'));
    }


    public function changePassword()
    {
        return view('profiles.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);


        $user = Auth::user();

        // dd($request);
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function documents()
    {
        $profile = Profile::where('user_id', auth()->id())->firstOrFail();
        return view('profiles.documents', compact('profile'));
    }

    public function uploadDocuments(Request $request)
    {
        $profile = Profile::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'pan_card_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,heic,heif|max:2048',
            'gst_certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,heic,heif|max:2048',
        ]);

        $file = $request->file('pan_card_file');

        if ($request->hasFile('gst_certificate_file') && !$request->file('gst_certificate_file')->isValid()) {
            return back()->withErrors(['gst_certificate_file' => 'Invalid GST file']);
        }

        $path = public_path('profiles/' . auth()->id());

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $updated = [];
        if ($request->hasFile('pan_card_file')) $updated[] = 'PAN';
        if ($request->hasFile('gst_certificate_file')) $updated[] = 'GST';
        // PAN
        if ($request->hasFile('pan_card_file')) {
            if ($profile->pan_card_file && file_exists(public_path($profile->pan_card_file))) {
                unlink(public_path($profile->pan_card_file));
            }

            $file = $request->file('pan_card_file');
            $filename = 'pan_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            $profile->pan_card_file = 'profiles/' . auth()->id() . '/' . $filename;
        }

        // GST
        if ($request->hasFile('gst_certificate_file')) {
            if ($profile->gst_certificate_file && file_exists(public_path($profile->gst_certificate_file))) {
                unlink(public_path($profile->gst_certificate_file));
            }

            $file = $request->file('gst_certificate_file');
            $filename = 'gst_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            $profile->gst_certificate_file = 'profiles/' . auth()->id() . '/' . $filename;
        }

        $profile->save();

        //return back()->with('notice', 'Documents updated successfully!');
        $message = count($updated)
            ? implode(', ', $updated) . ' document(s) updated successfully!'
            : 'No documents updated';

        return back()->with('notice', $message);
    }

    public function downloadDocument($type)
    {
        $profile = Profile::where('user_id', auth()->id())->firstOrFail();

        if ($type == 'pan' && $profile->pan_card_file) {
            return response()->download(public_path($profile->pan_card_file));
        }

        if ($type == 'gst' && $profile->gst_certificate_file) {
            return response()->download(public_path($profile->gst_certificate_file));
        }

        abort(404);
    }
}
