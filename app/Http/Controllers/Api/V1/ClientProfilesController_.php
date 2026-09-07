<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientProfilesController extends BaseApiController
{
    public function update(Request $request)
	{
		try {
			$user = auth()->user();
			if (!$user) {
				return response()->json(['User not authenticated'], 401);
			}
			$request->validate([
				'name' => 'nullable|string|max:255',
				'email' => 'nullable|email|max:255',
				'short_name' => 'nullable|string|max:100',
				'profile.business_type' =>
					'required|in:individual,partnership,corporation,llc,sole_proprietorship',
				'profile.pan_no' =>
					'nullable|string|max:20',
				'profile.gst_no' =>
					'nullable|string|max:30',
				'profile.mobile_no' =>
					'required|digits:10',
				'profile.whatsapp_no' =>
					'nullable|digits:10',
				'profile.address' =>
					'required|string|max:500',
				'profile.alternative_email' =>
					'nullable|email|max:255',
				'profile.profile_image' =>
					'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:2048',
			]);
			// ✅ fetch from nested profile array
			$profileData = $request->input('profile', []);
        	$profile = $user->profile()->first();

			// ✅ image handling
			if ($request->hasFile('profile.profile_image')) {
				$image = $request->file('profile.profile_image');

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
			
			// ✅ create/update profile
			$profile = $user->profile()->updateOrCreate([], $profileData);
			 $user->name =
				$request->name ?? $user->name;

			// $user->email =
				// $request->email ?? $user->email;

			$user->short_name =
				$request->short_name ?? $user->short_name;

			$user->save();

			return $this->success(
				__("response_message.profile.update_success"),
				$this->profileData($profile)
			);

		} catch (\Exception $e) {
			return response()->json([
				__("response_message.profile.create_error"),
				500,
				$e->getMessage()
			]);
		}
	}

    public function show()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return  response()->json(['User not authenticated', 401]);
            }

            $profile = $user->profile;
            if (!$profile) {
                return  response()->json([
                    __("response_message.profile.profile_not_found"),
                    422
                ]);
            }

            return $this->success(
                __("response_message.profile.show_success"),
                $this->profileData($profile)
            );
        } catch (\Exception $e) {
            return  response()->json([
                __("response_message.profile.show_error"),
                500,
                $e->getMessage()
            ]);
        }
    }

    private function profileData(Profile $profile)
    {
        $user = auth()->user();
        return [
            'id' => $user->id,
            'name' => $user->name,
			'short_name' => $user->short_name,
            'email' => $user->email,
            'mobile_no' => $profile->mobile_no,
            'whatsapp_no' => $profile->whatsapp_no,
            'gender' => $profile->gender,
            'business_type' => $profile->business_type,
            'preferred_language' => $profile->preferred_language,
			'alternative_email' => $profile->alternative_email,
            'pan_no' => $profile->pan_no,
            'gst_no' => $profile->gst_no,
            'profile_image_url' => $profile->profile_image ? asset($profile->profile_image) : null,
            'address' => $profile->address
        ];
    }
	
	public function changePassword(Request $request)
    {
		try {
			$user = auth()->user();
			if (!$user) {
				return response()->json(['User not authenticated'], 401);
			}
			// Validate request
			$validator = Validator::make($request->all(), [
				'current_password' => 'required',
				'new_password'     => 'required|min:8|confirmed', // need new_password_confirmation
			]);

			if ($validator->fails()) {
				return response()->json([
					'status'  => false,
					'message' => 'Validation error',
					'errors'  => $validator->errors(),
					'code' => 422
				], 422);
			}

			$user = $request->user();

			// Check old password
			if (!Hash::check($request->current_password, $user->password)) {
				return response()->json([
					'status'  => false,
					'message' => 'Current password is incorrect',
					'code' => 400
				], 400);
			}

			// Update password
			$user->password = Hash::make($request->new_password);
			$user->save();

			return response()->json([
				'status'  => true,
				'message' => 'Password changed successfully',
				'code' => 200
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				__("response_message.profile.create_error"),
				'code' => 500,
				$e->getMessage()
			]);
		}	
	}

	public function uploadDocuments(Request $request)
	{
		try {

			$user = auth()->user();

			if (!$user) {
				return response()->json([
					'status' => false,
					'message' => 'User not authenticated',
					'code' => 401
				], 401);
			}

			$profile = $user->profile()->firstOrFail();

			$validator = Validator::make($request->all(), [
				'pan_card_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,heic,heif|max:2048',
				'gst_certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,heic,heif|max:2048',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => false,
					'message' => 'Validation error',
					'errors' => $validator->errors(),
					'code' => 422
				], 422);
			}

			$path = public_path('profiles/' . $user->id);

			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}

			$updated = [];

			/*
			|--------------------------------------------------------------------------
			| PAN CARD
			|--------------------------------------------------------------------------
			*/
			if ($request->hasFile('pan_card_file')) {

				if (
					$profile->pan_card_file &&
					file_exists(public_path($profile->pan_card_file))
				) {
					unlink(public_path($profile->pan_card_file));
				}

				$file = $request->file('pan_card_file');

				$filename = 'pan_' . time() . '.' . $file->getClientOriginalExtension();

				$file->move($path, $filename);

				$profile->pan_card_file = 'profiles/' . $user->id . '/' . $filename;

				$updated[] = 'PAN';
			}

			/*
			|--------------------------------------------------------------------------
			| GST CERTIFICATE
			|--------------------------------------------------------------------------
			*/
			if ($request->hasFile('gst_certificate_file')) {

				if (
					$profile->gst_certificate_file &&
					file_exists(public_path($profile->gst_certificate_file))
				) {
					unlink(public_path($profile->gst_certificate_file));
				}

				$file = $request->file('gst_certificate_file');

				$filename = 'gst_' . time() . '.' . $file->getClientOriginalExtension();

				$file->move($path, $filename);

				$profile->gst_certificate_file = 'profiles/' . $user->id . '/' . $filename;

				$updated[] = 'GST';
			}

			$profile->save();

			return response()->json([
				'status' => true,
				'message' => count($updated)
					? implode(', ', $updated) . ' document(s) uploaded successfully!'
					: 'No documents uploaded',
				'data' => [
					'pan_card_file_url' => $profile->pan_card_file
						? asset($profile->pan_card_file)
						: null,

					'gst_certificate_file_url' => $profile->gst_certificate_file
						? asset($profile->gst_certificate_file)
						: null,
				],
				'code' => 200
			], 200);

		} catch (\Exception $e) {

			return response()->json([
				'status' => false,
				'message' => 'Document upload failed',
				'error' => $e->getMessage(),
				'code' => 500
			], 500);
		}
	}

	public function documents()
	{
		try {
			$user = auth()->user();
			if (!$user) {
				return response()->json([
					'status' => false,
					'message' => 'User not authenticated',
					'code' => 401
				], 401);
			}
			$profile = $user->profile()->first();
			if (!$profile) {
				return response()->json([
					'status' => false,
					'message' => 'Profile not found',
					'code' => 404
				], 404);
			}
			return response()->json([
				'status' => true,
				'message' => 'Documents fetched successfully',
				'data' => [
					'pan_card_file' => $profile->pan_card_file
						? asset($profile->pan_card_file)
						: null,
					'gst_certificate_file' => $profile->gst_certificate_file
						? asset($profile->gst_certificate_file)
						: null,
				],
				'code' => 200
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Failed to fetch documents',
				'error' => $e->getMessage(),
				'code' => 500
			], 500);
		}
	}

	public function businessTypes()
	{
		try {
			$data = [];

			foreach (Profile::BUSINESS_TYPES as $key => $value) {

				$data[] = [
					'key' => $key,
					'value' => $value
				];
			}
			return response()->json([
				'status' => true,
				'message' => 'Business types fetched successfully',
				'data' => $data,
				'code' => 200
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Failed to fetch business types',
				'error' => $e->getMessage(),
				'code' => 500
			], 500);
		}
	}
}
