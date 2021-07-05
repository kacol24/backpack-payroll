<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushbullet\PushbulletChannel;
use NotificationChannels\Pushbullet\PushbulletMessage;

class EmployeeAttendance extends Notification implements ShouldQueue
{
    use Queueable;

    protected $attendance;

    protected $attendanceType;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($attendance, $attendanceType)
    {
        $this->attendance = $attendance;
        $this->attendanceType = $attendanceType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            PushbulletChannel::class,
        ];
    }

    /**
     * Get the pushbullet representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Pushbullet\PushbulletMessage
     */
    public function toPushbullet($notifiable)
    {
        return PushbulletMessage::create($this->getMessage())
                                ->title($this->getTitle())
                                ->link()
                                ->url(route('attendance.show', $this->attendance->id));
    }

    private function getTitle()
    {
        $template = 'Clock-{attendance_type}: {employee_name} has just clocked-{attendance_type}!';

        return str_replace([
            '{employee_name}',
            '{attendance_type}',
        ], [
            $this->attendance->employee->name,
            $this->attendanceType,
        ], $template);
    }

    private function getMessage()
    {
        $template = '{employee_name} clocked-{attendance_type} at {attendance_at}';

        return str_replace([
            '{employee_name}',
            '{attendance_type}',
            '{attendance_at}',
        ], [
            $this->attendance->name,
            $this->attendanceType,
            $this->getAttendanceAt(),
        ], $template);
    }

    private function getAttendanceAt()
    {
        if ($this->attendanceType == Attendance::TYPE_CLOCK_IN) {
            return $this->attendance->start_at->format('d M Y, H:i:s');
        }

        return $this->attendance->end_at->format('d M Y, H:i:s');
    }
}
