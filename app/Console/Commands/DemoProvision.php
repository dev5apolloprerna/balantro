<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Creates (or repairs) the inactive users row that owns the sample company.
 *
 * It only touches that one row. It does not create vouchers, ledgers or years:
 * those come from syncing the fictional Tally company with the normal sync
 * agent, using the GUID this command prints.
 */
class DemoProvision extends Command
{
    protected $signature = 'demo:provision
        {--guid= : PartyGUID of the sample Tally company (UUID). Generated if omitted and not configured}
        {--email= : Login e-mail for the row (defaults to config demo.email)}
        {--name= : Company name (defaults to config demo.company_name)}';

    protected $description = 'Create or repair the read-only sample company user row';

    public function handle(): int
    {
        $email = strtolower((string) ($this->option('email') ?: config('demo.email')));
        $name = (string) ($this->option('name') ?: config('demo.company_name'));
        $guid = (string) ($this->option('guid') ?: config('demo.party_guid') ?: Str::uuid());

        if (! Str::isUuid($guid)) {
            $this->error("The GUID must be a UUID; got: {$guid}");
            return self::FAILURE;
        }

        if ($email === '' || ! str_ends_with($email, '.invalid')) {
            $this->warn('Use an address on the reserved .invalid domain so no mail can ever be sent for the sample company.');
        }

        $clash = User::withTrashed()
            ->where('guid', $guid)
            ->where('email', '<>', $email)
            ->first();
        if ($clash) {
            $this->error("GUID {$guid} already belongs to user #{$clash->id} ({$clash->email}). Choose another GUID.");
            return self::FAILURE;
        }

        $user = User::withTrashed()->where('email', $email)->first();
        $creating = $user === null;

        if (! $creating && (int) $user->role !== User::ROLES['client']) {
            $this->error("{$email} already exists with a staff role. Refusing to change it.");
            return self::FAILURE;
        }

        // Without model events: no default group, no activity log, no welcome mail.
        $user = User::withoutEvents(function () use ($user, $email, $name, $guid) {
            $user ??= new User();

            $attributes = [
                'name' => $name,
                'email' => $email,
                'role' => User::ROLES['client'],
                'type' => 'Client',
                'guid' => $guid,
                'is_active' => false,
                'deleted_at' => null,
            ];

            if (! $user->exists) {
                // Nobody knows this password; logins are also blocked by id.
                $attributes['password'] = Hash::make(Str::random(64));
            }

            if (Schema::hasColumn('users', 'is_demo')) {
                $attributes['is_demo'] = true;
            }

            $user->forceFill($attributes)->save();

            return $user;
        });

        $this->info(($creating ? 'Created' : 'Updated') . " sample company user #{$user->id}.");
        $this->newLine();
        $this->line('Add these lines to .env, then run `php artisan config:clear`:');
        $this->line('DEMO_PARTY_ID=' . $user->id);
        $this->line('DEMO_PARTY_GUID=' . $guid);
        $this->line('DEMO_PARTY_EMAIL=' . $email);
        $this->line('DEMO_DATA_ENABLED=true');
        $this->newLine();
        $this->line("Next: sync the fictional Tally company with iPartyId {$user->id} and PartyGUID {$guid},");
        $this->line('then run `php artisan demo:verify`.');

        if (! Schema::hasColumn('users', 'is_demo')) {
            $this->warn('Column users.is_demo is missing. Run the add_is_demo_to_users migration (optional; isolation uses DEMO_PARTY_ID).');
        }

        return self::SUCCESS;
    }
}
