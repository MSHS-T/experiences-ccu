<?php

namespace App\Console\Commands;

use App\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FreeUnconfirmedSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:free-unconfirmed-slots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes subject data from slots that been booked but not confirmed before expiration time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slots = Slot::whereNotNull('subject_email')
            ->where('subject_confirmed', '!=', true)
            ->where('subject_confirm_before', '<', Carbon::now())
            ->get();

        $total = $slots->count();
        $this->line("Cleaning booking information for $total slots that have not been confirmed.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($slots as $slot) {
            $slot->clearBooking(true);
            $bar->advance();
        }
        $bar->finish();
        $this->line("");
        $this->line("Unconfirmed slots have all been unbooked.");
    }
}
