<?php
/**
 * get filcker photo url
 * @param array $photo_item
 * @param string $type
 */
function flicker_photo_url($photo_item, $type) {
	return "http://farm{$photo_item['farm']}.staticflickr.com/{$photo_item['server']}/{$photo_item['id']}_{$photo_item['secret']}_$type.jpg";
}