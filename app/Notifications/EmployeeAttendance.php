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
        $employee = $this->attendance->employee;

        $title = 'Clock-{attendance_type}: {employee_name} has just clocked-{attendance_type}!';
        $message = '{employee_name} clocked-{attendance_type} at {attendance_at}';

        $title = str_replace([
            '{employee_name}',
            '{attendance_type}',
        ], [
            $employee->name,
            $this->attendanceType,
        ], $title);

        $attendanceAt = $this->attendance->start_at->format('d M Y, H:i:s');
        if ($this->attendanceType == Attendance::TYPE_CLOCK_OUT) {
            $attendanceAt = $this->attendance->end_at->format('d M Y, H:i:s');
        }

        $message = str_replace([
            '{employee_name}',
            '{attendance_type}',
            '{attendance_at}',
        ], [
            $employee->name,
            $this->attendanceType,
            $attendanceAt,
        ], $message);

        return PushbulletMessage::create($message)
                                ->title($title)
                                ->link()
                                ->url(route('attendance.show', $this->attendance->id));
    }
}
