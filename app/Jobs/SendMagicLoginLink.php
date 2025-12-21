<?php

namespace App\Jobs;

use App\Features\SystemManagements\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\MagicLoginMail;
use Illuminate\Support\Facades\Mail;

class SendMagicLoginLink implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public string $token) {}

    /**
     * Execute the job.
     */

     public function handle(): void
     {
         // if frontend_url is set in config, use it, otherwise use the current url
         $url = config('app.frontend_url')
             ? rtrim(config('app.frontend_url'), '/')."/magic-login/?token={$this->token}"
             : url("/magic-login/?token={$this->token}");

         Mail::to($this->user->email)->send(new MagicLoginMail($url));
     }
}
