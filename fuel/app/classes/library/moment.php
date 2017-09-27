<?php
class Library_Moment
{
  private $time;
  private $lang;

  public $defineLocale = [
    'en' => [
      'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      'monthsShort' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      'weekdays' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
      'weekdaysShort' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      'weekdaysMin' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
      'longDateFormat' => [
        'LT'   => 'H:mm',
        'LTS'  => 'H:mm:ss',
        'L'    => 'MM/DD/YYYY',
        'LL'   => 'MMMM D, YYYY',
        'LLL'  => 'MMMM D, YYYY H:mm',
        'LLLL' => 'dddd, MMMM D, YYYY H:mm'
      ],
    ],

    'ja' => [
      'months' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      'monthsShort' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      'weekdays' => ['月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日'],
      'weekdaysShort' => ['月', '火', '水', '木', '金', '土', '日'],
      'weekdaysMin' => ['月', '火', '水', '木', '金', '土', '日'],
      'longDateFormat' => [
        'LT' => 'H時mm分',
        'LTS' => 'H時mm分s秒',
        'L' => 'YYYY/MM/DD',
        'LL' => 'YYYY年M月D日',
        'LLL' => 'YYYY年M月D日H時mm分',
        'LLLL' => 'YYYY年M月D日H時mm分 dddd'
      ],
    ],
  ];

  private $formattingTokens = '/(\[[^\[]*\])|(\\\\)?([Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|YYYYYY|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?|.)/';

  //---------------------------------------------------------------------------------------
  public function __construct($time, $lang = 'en') {
    $this->time = strtotime($time);
    $this->lang = $lang;
    return $this;
  }

  //---------------------------------------------------------------------------------------
  public function add($str) {
    $this->time = strtotime(date('Y-m-d H:i:s', $this->time) . ' ' . $str);
    return $this;
  }

  //---------------------------------------------------------------------------------------
  public function timeZoneToUTC($current_time_zone = null) {
    if (empty($current_time_zone))
      $current_time_zone = date('Z') / 3600 * -1;
    else
      $current_time_zone = substr($current_time_zone, 0, 3) * -1;

    $this->time = strtotime(date('Y-m-d H:i:s', $this->time) . ' ' . $current_time_zone . ' hour');
    return $this;
  }

  //---------------------------------------------------------------------------------------
  public function utcToTimeZone($current_time_zone = null) {
    if (empty($current_time_zone))
      $current_time_zone = date('Z') / 3600;
    else
      $current_time_zone = substr($current_time_zone, 0, 3);

    $this->time = strtotime(date('Y-m-d H:i:s', $this->time) . ' ' . $current_time_zone . ' hour');
    return $this;
  }

  //---------------------------------------------------------------------------------------
  public function format($format) {
    $formatLang = $this->defineLocale[$this->lang];

    $s = 0; // start
    $l = 1; // length

    // parse format to  y,m,h,d,.....
    $parse_format = '';

    for ($i = 0; $i < strlen($format) + 1; $i++) {
      $tmp = substr($format, $s, $l);

      if (empty($formatLang['longDateFormat'][$tmp]) || $s + $l == strlen($format)) {

        if ($l > 1) {
          $t = substr($format, $s, ($s + $l == strlen($format)) ? $l : --$l);
          $parse_format .= $formatLang['longDateFormat'][$t] . @$format[$s + $l];
        } else {
          $parse_format .= @$format[$s];
        }

        if ($s + $l == strlen($format)) break;

        $s = $i + 1;
        $l = 1;

      } else {
        $l++;
      }
    }

    $format = $parse_format;

    preg_match_all($this->formattingTokens, $format, $tokens);

    // mapping to format
    $tokens = $tokens[0];

    foreach ($tokens as &$token) {
      switch ($token) {
        case 'YYYY':
          $token = date('Y', $this->time);
          break;
        case 'MMM':
          $token = $formatLang['monthsShort'][date('m') - 1];
          break;
        case 'MMMM':
          $token = $formatLang['months'][date('m') - 1];
          break;
        case 'dd':
          $token = $formatLang['weekdaysMin'][date('N') - 1];
          break;
        case 'ddd':
          $token = $formatLang['weekdaysShort'][date('N') - 1];
          break;
        case 'dddd':
          $token = $formatLang['weekdays'][date('N') - 1];
          break;
        case 'D':
        case 'DD':
          $token = date('d', $this->time);
          break;
        case 'h':
          $token = date('g', $this->time);
          break;
        case 'hh':
          $token = date('h', $this->time);
          break;
        case 'H':
          $token = date('G', $this->time);
          break;
        case 'HH':
          $token = date('H', $this->time);
          break;
        case 'mm':
          $token = date('i', $this->time);
          break;
        case 'M':
        case 'MM':
          $token = date('m', $this->time);
          break;
        case 'ss':
          $token = date('s', $this->time);
          break;
        default:
          $token = date($token, $this->time);
      }
    }

    return implode('', $tokens);
  }
}