<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Request;
class DriverRequestNotificationTimeout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $url;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request, $url, $user_token)
    {
        $this->request = $request;
        $this->url = $url;
        $this->token = $user_token;
        \Log::info($this->url);
        \Log::info("Dispatched Job : RequestId:".$this->request->id." | Time:".time()." Status:".$this->request->status." ");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->request->status == 'Pending')
        {
            try
            {
                // $ping = file_get_contents($this->url.'?request_id='.$this->request->id.'&status=Cancelled');
                $client = new \GuzzleHttp\Client();
                $res = $client->get($this->url.'?request_id='.$this->request->id.'&status=Cancelled'.'&token='.$this->token);
                \Log::info($res->getStatusCode());
            }
            catch(\Exception $e)
            {
                \Log::info("Error on Process : ".@$e->getMessage());
            }
        }

        \Log::info($this->url);
        \Log::info("Handled Job : RequestId:".$this->request->id." | Time:".time()." Status:".$this->request->status." ");
    }
}
