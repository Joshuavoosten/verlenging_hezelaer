<?php

namespace App\Console\Commands;

use Config;
use Illuminate\Console\Command;
use Mail;

class MailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test
                            {to_address : Email address}
                            {to_name : Name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email';

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
        $from_address = Config::get('mail.from.address');
        $from_name = Config::get('mail.from.name');

        $to_address = $this->argument('to_address');
        $to_name = $this->argument('to_name');

        $subject = 'Mailer Test';

        echo 'From: '.$from_name.' '.'<'.$from_address.'>'."\n";
        echo 'To: '.$to_name.' '.'<'.$to_address.'>'."\n";
        echo 'Subject: '.$subject."\n";

        /*
        Mail::send('emails.test', [], function ($m) use ($from_address, $from_name, $to_address, $to_name, $subject) {
            $m->from($from_address, $from_name);
            $m->to($to_address, $to_name)->subject($subject);
        });
        */

        $aMergeVars = [
            'myvar1' => 'Hallo Wereld',
            'myvar2' => 'Dit is een test'
        ];

        Mail::send('emails.test', [], function($m) use ($from_address, $from_name, $to_address, $to_name, $subject, $aMergeVars) {
            $headers = $m->getHeaders();
            $headers->addTextHeader('X-MC-MergeVars', json_encode($aMergeVars));
            $headers->addTextHeader('X-MC-Template', 'Test');
            $m->to($to_address, $to_name)->subject($subject);
        });
    }

}