<?php
/**
 * get filcker photo url
 * @param array $photo_item
 * @param string $type
 */
function flicker_photo_url($photo_item, $type) {
	return "http://farm{$photo_item['farm']}.staticflickr.com/{$photo_item['server']}/{$photo_item['id']}_{$photo_item['secret']}_$type.jpg";
}

/**
 * 初始化memcached
 */
function init_memcached() {
    global $_SGLOBAL, $_SC;

    if( !empty($_SGLOBAL['m']) ) { return; }

    //new
    $m = new Memcached();
    //add servers
    $m->addServers($_SC['memcached']);
    //get stats
    if( $m->getStats() == false ) {
        header("HTTP/1.0 500 Internal Server Error" , true , 500);
        die('connetct to memcached server failure, please check all memcached servers.');
    }

    return $m;
}