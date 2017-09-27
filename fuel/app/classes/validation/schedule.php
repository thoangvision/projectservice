<?php
class Validation_Schedule {

  //---------------------------------------------------------------------------------------
  public static function action_create($params, $options) {
    if (Validation_Schedule::is_past($params))
      throw new Exception(__('you_cant_book_schedule_in_past'));

    if (Validation_Schedule::is_exist_schedule($params))
      throw new Exception(__('room_have_been_booked'));

    if (!Validation_Schedule::have_member($params))
      throw new Exception(__('please_choose_member'));

    if (empty($options['email_user']))
      throw new Exception(__('you_need_set_email_sent_for_user_when_booking_room'));

    if (empty($options['email_customer']))
      throw new Exception(__('you_need_set_email_sent_for_customer_when_booking_room'));
  }

  //---------------------------------------------------------------------------------------
  public static function action_update($params, $options) {
    if (Validation_Schedule::is_past($params))
      throw new Exception(__('you_cant_book_schedule_in_past'));

    if (Validation_Schedule::is_exist_schedule($params))
      throw new Exception(__('room_have_been_booked'));

    if (Validation_Schedule::is_already_checkout($params))
      throw new Exception(__('schedule_have_already_checked_out'));

    if (Validation_Schedule::is_already_checkin($params))
      throw new Exception(__('schedule_have_already_checked_in'));

    if (!Validation_Schedule::have_member($params))
      throw new Exception(__('please_choose_member'));

    if (empty($options['email_user']))
      throw new Exception(__('you_need_set_email_sent_for_user_when_booking_room'));

    if (empty($options['email_customer']))
      throw new Exception(__('you_need_set_email_sent_for_customer_when_booking_room'));
  }

  //---------------------------------------------------------------------------------------
  public static function is_past($params) {
    $time = null;
    if (!empty($params['start_time']))
      $time = $params['start_date'] . ' ' . $params['start_time'];
    else {
      $tmp = new DateTime($params['start_date'] . ' 23:59:59 ' . Session::get('user')->time_zone);

      $tmp->setTimeZone(new DateTimeZone('UTC'));

      $time = $tmp->format('Y-m-d H:i:s');
    }

    return !Validation_Schedule::is_before_time_minute($time);
  }

  //---------------------------------------------------------------------------------------
  public static function have_member($params) {
    if (!empty($params['rooms']) && count($params['rooms']) > 0)
      foreach ($params['rooms'] as $room) {
        if (empty($room['members']))
          return false;
      }

    return true;
  }

  //---------------------------------------------------------------------------------------
  public static function is_already_checkin($params) {
    if (empty($params['check_in_time']))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function is_already_checkout($params) {
    if (empty($params['check_out_time']))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function is_exist_schedule($params) {
    $rooms_ids = [-1];

    if (!empty($params['rooms'])) {
      $rooms_ids = array_column($params['rooms'], 'id');
    } else if (!empty($params['id'])) {
      $schedule = Model_Schedule::find($params['id']);

      foreach ($schedule->rooms as $room)
        array_push($rooms_ids, $room->id);
    }

    if (empty($params['start_time']) && empty($params['end_time']))
      return false;

    $start = $params['start_date'] . ' ' . $params['start_time'];
    $end = $params['end_date'] . ' ' . $params['end_time'];

    $result = DB::select(DB::expr('schedules.*'))
                ->from('schedules')
                ->join('schedule_info')->on('schedules.id', '=', 'schedule_info.schedule_id')
                ->where('schedule_info.room_id', 'IN', $rooms_ids)
                ->where('schedules.status', '<>', 'delete')
                ->where('schedules.check_out_time', 'IS', NULL)
                ->where_open()

                ->or_where_open()
                ->where(DB::expr("concat(start_date, ' ', start_time)"), '>=', $start)
                ->where(DB::expr("concat(end_date, ' ', end_time)"), '<=', $end)
                ->or_where_close()

                ->or_where_open()
                ->where(DB::expr("concat(start_date, ' ', start_time)"), '<=', $start)
                ->where(DB::expr("concat(end_date, ' ', end_time)"), '>', $start)
                ->or_where_close()

                ->or_where_open()
                ->where(DB::expr("concat(start_date, ' ', start_time)"), '<', $end)
                ->where(DB::expr("concat(end_date, ' ', end_time)"), '>=', $end)
                ->or_where_close()

                ->where_close()
                ->limit(1);

    if (!empty($params['id']))
      $result = $result->where('schedules.id', '<>', $params['id']);

    $result = $result->execute()->current();

    if (empty($result))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function is_before_time_minute($time, $minute = 0) {
    return strtotime(gmdate('Y-m-d H:i:s') . ' +' . $minute . ' minutes') < strtotime($time);
  }
}