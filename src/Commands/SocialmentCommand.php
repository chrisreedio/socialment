<?php

namespace ChrisReedIO\Socialment\Commands;

use Illuminate\Console\Command;

class SocialmentCommand extends Command
{
    public $signature = 'socialment';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
