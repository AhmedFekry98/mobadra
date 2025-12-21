<?php

namespace App\Features\AuthManagement\Services;

use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Models\UserInformation;
use App\Features\SystemManagements\Models\UserService;
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

            // Update user services
            if (isset($data['user_services'])) {
                $this->updateUserServices($user, $data['user_services']);
            }

            // Reload relationships
            return $user->fresh(['user_information', 'user_services.service']);
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
     * Update user services
     */
    protected function updateUserServices(User $user, array $serviceIds): void
    {
        // Remove existing services - use where clause to ensure proper deletion
        UserService::where('user_id', $user->id)->delete();

        // Add new services
        foreach ($serviceIds as $serviceId) {
            UserService::create([
                'user_id' => $user->id,
                'service_id' => $serviceId,
            ]);
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

    /**
     * become provider
     */
    public function becomeProvider(User $user): User
    {

        if ($user->roleName == 'provider') {
            throw new \Exception('User is already a provider');
        }

        if ($user->roleName == 'customer') {
            return DB::transaction(function () use ($user) {
                $providerRole = Role::where('name', 'provider')->first();
                if (!$providerRole) {
                    throw new \Exception('Provider role not found');
                }

                $user->role_id = $providerRole->id;
                $user->save();
                return $user->fresh();
            });
        }
        throw new \Exception('User role is not eligible for provider conversion');
    }


}
