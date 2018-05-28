<?php

namespace EthicalJobs\Quantify;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;

class ReportNotice extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Report data
     * 
     * @var array
     */
    protected $report;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slackConfig = config('quantify.channels.slack');

        $content = '```'.json_encode($this->toArray(), JSON_PRETTY_PRINT).'```';

        return (new SlackMessage)
            ->from($slackConfig['from'], $slackConfig['icon'])
            ->to($slackConfig['channel'])
            ->content($content);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray()
    {
        return $this->report;
    }
}
