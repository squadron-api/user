<?php

namespace Squadron\User\Console\Commands;

use Squadron\User\Models\User;
use Illuminate\Console\Command;

class BanUser extends Command
{
    protected $signature = 'squadron:user:ban {email}';
    protected $description = 'ban / unban user by email';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userEmail = trim($this->argument('email'));
        $user = User::where('email', $userEmail)->first();

        if ($user !== null)
        {
            $userTokens = $user->tokens;

            foreach ($userTokens as $token)
            {
                $token->revoke();
            }

            $user->active = ! $user->active;
            $user->save();

            if ($user->active)
            {
                $this->info(sprintf('User `%s` was unbanned!', $userEmail));
            }
            else
            {
                $this->error(sprintf('User `%s` was banned!', $userEmail));
            }
        }
    }
}
