<?php
class Exception_Code {

  const LOGIN_OK                          = 100;
  const LOGIN_ER                          = 101;
  const TOKEN_ERROR                       = 102;

//  const HTTP_


  # 2xx Success
  const E_OK                                = 200;

  # 4xx Client Error
  const E_BAD_REQUEST                       = 400;
  const E_UNAUTHORIZED                      = 401;
  const E_PAYMENT_REQUIRED                  = 402;
  const E_FORBIDDEN                         = 403;
  const E_NOT_FOUND                         = 404;
  const E_METHOD_NOT_ALLOWED                = 405;

  # 5xx Server Error
  const E_INTERNAL_SERVER_ERROR             = 500;
  const E_NOT_IMPLEMENTED                   = 501;
  const E_BAD_GATEWAY                       = 502;
  const E_SERVICE_UNAVAILABLE               = 503;
  const E_GATEWAY_TIMEOUT                   = 504;
  const E_HTTP_VERSION_NOT_SUPPORTED        = 505;

  # 3xxx: Validation error
  const E_VALIDATE_ERR                      = 3000;


  public static function getMessage($code) {
    $_codeMessage = [
        self::LOGIN_OK => __('login success'),
        self::LOGIN_ER => __('Login not success'),
        self::E_INTERNAL_SERVER_ERROR => __('internal_server_error'),
        self::TOKEN_ERROR => __('token_error'),
    ];

    $message = __('An unexpected error occurred', [], 'An unexpected error occurred');
    if(array_key_exists($code, $_codeMessage)){
        $message = $_codeMessage[$code];
    }
    return $message;
  }
}
