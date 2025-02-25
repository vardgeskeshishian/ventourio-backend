<?php

namespace App\Events;

use App\Models\Certificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnseenCertificatesCount  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public function broadcastAs(): string
	{
		return 'unseen_certificates_count';
	}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return  Channel
	 */
    public function broadcastOn(): Channel
	{
        return new Channel("certificates-count");
    }

	public function broadcastWith(): array
	{
		return [
            'unseenCertificateCount' => Certificate::query()->unseen()->count(),
        ];
	}
}
