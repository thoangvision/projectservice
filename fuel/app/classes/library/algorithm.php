<?php
class Library_Algorithm
{
  //---------------------------------------------------------------------------------------
  public function __construct() {
  }

  //---------------------------------------------------------------------------------------
  // $params = ['to' => 'example@example.com', 'subject' => 'Example Title', 'body' => 'Example body']
  public static function send_email($params) {
    $params['url'] = @$params['url'] ?: Uri::create('/function/email/send');
    $params['token'] = md5(gmdate('Y-m-d'));

    Library_Algorithm::send_request($params['url'], 'POST', $params);

    // IF DEBUG UNCOMMENT
    // echo Library_Algorithm::send_request($params['url'], 'POST', $params, true);
    // exit;
  }

  //---------------------------------------------------------------------------------------
  public static function send_request($url, $method, $params = [], $return_reponse = false) {
    if (!$url_info = parse_url($url)) {
      return false;
    }

    $stream_context = stream_context_create([
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
      ),
    ]);

    switch ($url_info['scheme']) {
      case 'https':
        $scheme = 'ssl://';
        $port = 443;
        break;
      case 'http':
      default:
        $scheme = '';
        $port = 80;
    }

    switch (strtolower($method)) {
      case 'get':
        $fp = @stream_socket_client($scheme . $url_info['host'] . ':' . $port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream_context);

        if ($fp) {
          $out = "GET " . $url_info['path'];
          $out .= isset($url_info['query']) ? '?' . $url_info['query'] : '';
          $out .= " HTTP/1.1\r\n";
          $out .= "Host: " . $url_info['host'] . "\r\n";
          $out .= "Connection: Close\r\n\r\n";

          fwrite($fp, $out);

          if ($return_reponse) {
            $response = '';

            while (!feof($fp))
              $response .= fgets($fp, 1024);

            fclose($fp);

            return explode("\r\n\r\n", $response, 2)[1];
          } else {
            // sleep(1);
          }

          fclose($fp);

          return true;
        } else {
          return false;
        }

        break;

      case 'post':
        $fp = @stream_socket_client($scheme . $url_info['host'] . ':' . $port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream_context);

        if ($fp) {
          $content = http_build_query($params);

          $out = "POST " . $url_info['path'];
          $out .= isset($url_info['query']) ? '?' . $url_info['query'] : '';
          $out .= " HTTP/1.1\r\n";
          $out .= "Host: " . $url_info['host'] . "\r\n";
          $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
          $out .= "Content-Length: ".strlen($content)."\r\n";
          $out .= "Connection: Close\r\n\r\n";
          $out .= $content . "\r\n\r\n";

          fwrite($fp, $out);

          if ($return_reponse) {
            $response = '';

            while (!feof($fp))
              $response .= fgets($fp, 1024);

            fclose($fp);

            return explode("\r\n\r\n", $response, 2)[1];
          } else {
            // sleep(1);
          }

          fclose($fp);

          return true;
        } else {
          return false;
        }

        break;
    }
  }

  //---------------------------------------------------------------------------------------
  public static function repeat_days($params) {
    $result = [];

    switch ($params['repeat_type']) {
      case 'daily':
        while (strtotime($params['start_date']) <= strtotime($params['repeat_to'])) {
          array_push($result, [
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
          ]);

          // increase interval
          $interval_str = "+" . $params['interval'] . " days";

          $params['start_date'] = date('Y-m-d', strtotime($params['start_date'] . $interval_str));
          $params['end_date'] = $params['start_date'];
        }

        return $result;

      case 'weekly':
        $day_value = ['monday' => 0, 'tuesday' => 1, 'wednesday' => 2, 'thursday' => 3, 'friday' => 4, 'saturday' => 5, 'sunday' => 6];
        $num = date('N', strtotime($params['start_date']));
        $first_monday = date('Y-m-d', strtotime($params['start_date'] . (1-$num) . ' days'));

        $dates = [];

        $day_names = explode(',', $params['day_weeks']);

        foreach($day_names as $day_name) {
          array_push($dates, date('Y-m-d', strtotime($first_monday . $day_value[$day_name] . ' days')));
        }

        while (true) {
          foreach ($dates as &$date) {
            if (strtotime($date) > strtotime($params['repeat_to']))
              return $result;

            if (strtotime($params['start_date']) <= strtotime($date))
              array_push($result, [
                'start_date' => $date,
                'end_date' => $date,
              ]);

            // increase interval
            $interval_str = "+" . $params['interval'] * 7 . " days";

            $date = date('Y-m-d', strtotime($date . $interval_str));
          }
        }

        return $result;
      case 'monthly':
        $ordinal_numbers = ['first', 'second', 'third', 'fourth', 'fifth'];

        // increase interval
        $interval_str = '';

        switch ($params['repeat_by']) {
          case 'day_of_the_month':
            $interval_str = '+' . $params['interval'] . ' months';
            break;

          case 'day_of_the_week':
            $week_number = Library_Algorithm::week_of_month($params['start_date']);
            $day_name = date('l', strtotime($params['start_date']));

            $interval_str = ' ' . $ordinal_numbers[$week_number - 1] . ' ' . $day_name;
            break;
        }

        while (strtotime($params['start_date']) <= strtotime($params['repeat_to'])) {
          array_push($result, [
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
          ]);

          $first_day_of_interval_month = date('Y-m-00', strtotime($params['start_date'] . '+' . $params['interval'] . ' months'));

          $params['start_date'] = date('Y-m-d', strtotime($first_day_of_interval_month . $interval_str));
          $params['end_date'] = $params['start_date'];
        }

        return $result;
    }
  }

  //---------------------------------------------------------------------------------------
  public static function array_unique_by_fields($array, $fields) {
    $result = array();

    foreach ($array as $v) {
      $k = '';

      foreach ($fields as $f) {
        $k .= $v[$f] . '|';
      }

      isset($result[$k]) or $result[$k] = $v;
    }

    return array_values($result);
  }

  //---------------------------------------------------------------------------------------
  public static function time_range_str($start, $end, $options = []) {
    $options['lang'] = @$options['lang'] ?: 'ja';
    $options['time_zone'] = @$options['time_zone'] ?: '+09:00';

    $s = new Library_Moment( $start, $options['lang'] );
    $e = new Library_Moment( $end, $options['lang'] );

    $s->utcToTimeZone( $options['time_zone'] );
    $e->utcToTimeZone( $options['time_zone'] );

    if (substr($start, 0, 10) == substr($end, 0, 10)) {
      return $s->format('LL, LT') . ' ~ ' . $e->format('LT');
    } else {
      return $s->format('LL, LT') . ' ~ ' . $e->format('LL, LT');
    }
  }

  //---------------------------------------------------------------------------------------
  public static function direction_to($latitude, $longitude) {
    if ($latitude != 0 && $longitude != 0) {
      $url_gg_map = "https://www.google.com/maps?saddr=Current+Location";
      $url_gg_map .= "&daddr=" . $latitude . "," . $longitude;
      $url_gg_map .= "&dirflg=d";

      return '<a href="' . $url_gg_map . '" style="font-weight: bold; text-decoration: none; color: #3A87AD;">地図</a>';
    }

    return '';
  }

  //---------------------------------------------------------------------------------------
  public static function week_of_month($date) {
    $firstOfMonth = strtotime(date('Y-m-01', strtotime($date)));

    $t = strtotime($date . '-7 days');
    $n = 1;

    while ($t >= $firstOfMonth) {
      $t = strtotime(date('Y-m-d', $t) . ' -7 days');
      $n++;
    }

    return $n;
  }

  //---------------------------------------------------------------------------------------
  public static function is_email($email) {
    $regex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i';

    if (!preg_match($regex, $email))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function base64_encode_image($url, $type) {
    $imgbinary = file_get_contents($url);
    return 'data:image/' . $type . ';base64,' . base64_encode($imgbinary);
  }

  //---------------------------------------------------------------------------------------
  public static function is_password($password) {
    $regex = '/^(?=.*[\p{Ll}])(?=.*[\p{Lu}])(?=.*\d)(?=.*[.?~!@#$%^&*])[\p{L}\d\.?~!@#$%^&*]{8,}$/';

    if (!preg_match($regex, $password))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function is_image($image) {
    $regex = '/^.*\.{1}(jpg|jpeg|png|gif)$/i';

    if (!preg_match($regex, $image))
      return false;
    else
      return true;
  }

  //---------------------------------------------------------------------------------------
  public static function generate_code($len = 6) {
    $seed = str_split('ABCDEFGHIJKLMNPQRSTUVWXYZ123456789');
    shuffle($seed);
    $rand = '';
    foreach (array_rand($seed, $len) as $k) $rand .= $seed[$k];

    return $rand;
  }
}