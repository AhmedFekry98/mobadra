<?php

namespace App\Features\AuthManagement\Services;

use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Update basic user info
            if (isset($data['name']) || isset($data['email'])) {
                $user->update([
                    'name' => $data['name'] ?? $user->name,
                    'email' => $data['email'] ?? $user->email,
                ]);
            }

            // Update profile image
            if (isset($data['image'])) {
                $this->updateUserImage($user, $data['image']);
            }

            // Update user information
            if (isset($data['user_information'])) {
                $this->updateUserInformation($user, $data['user_information']);
            }

            // Reload relationships
            return $user->fresh(['userInformation']);
        });
    }

    /**
     * Update or create user information
     */
    protected function updateUserInformation(User $user, array $informationData): void
    {
        if ($user->userInformation) {
            $user->userInformation->update($informationData);
        } else {
            $user->userInformation()->create($informationData);
        }
    }

    /**
     * Update user profile image
     */
    protected function updateUserImage(User $user, UploadedFile $image): void
    {
        // Clear existing images in the user-image collection
        $user->clearMediaCollection('user-image');

        // Add new image to the user-image collection
        if ($image) {
            $user->addMediaFromRequest('image')
                ->toMediaCollection('user-image');
        }

    }

}

