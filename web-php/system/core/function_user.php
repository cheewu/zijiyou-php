<?php
/**
 * User Functions
 * 
 * @author 
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }




/**
 * 写登录cookie信息
 * @param $user_info
 */
function user_write_cookie_info($user_info, $remember_me=0)
{
    global $_SC;
    
    //cookie 过期时间
    $expire_time = $remember_me ? $_SC['cookie_expire'] : 0;
    
    ssetcookie('auth', user_make_cookie_auth($user_info['user_id']), $expire_time);
    ssetcookie('user_id', $user_info['user_id'], $expire_time);
}


/**
 * 清理登录cookie信息
 */
function user_clear_cookie_info()
{
    ssetcookie('auth', '', -86400*365);
    ssetcookie('user_id', '', -86400*365);
}


/**
 * 生成cookie验证信息
 * 
 * @param $var
 * @param bool $allow_multi_login 允许多IP登陆
 */
function user_make_cookie_auth($var, $allow_multi_login=1)
{
    global $_SC;
    
    //允许多IP登陆
    if( $allow_multi_login ) {
        return md5($var."j9aqjkIWF_)23++==Dw`~W#c");
    }
    
    return md5($var."j9aqjkIWF_)23++==Dw`~W#c".get_onlineip()); 
}


