<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * 包含外部css文件，$file不为空则包含，为空则输出css文件
 * 
 * @param string $file
 */
function tpl_include_ex_css($file='')
{
    global $_TPL, $_SCONFIG;
    
    //include
    if( trim($file) != '' && substr($file, -4) == '.css' ) {
        $_TPL['header']['css'][$file] = $file;//避免重复包含css
        return true;
    }
    
    //echo
    $paths = array();
	if( !empty($_TPL['header']['css'] ) ) {
	    foreach ($_TPL['header']['css'] as $val) {
	        if( !empty($_SCONFIG['template']) ) {
	            $tmp_path = 'template/'.$_SCONFIG['template'].'/css/'.$val;
	        }else{
	            $tmp_path = 'css/'.$val;
	        }
	        file_exists(A_ROOT.$tmp_path) && $paths[] = '/'.$tmp_path;
	    }
	}
	
	if( !empty($paths) ) {
	    $url_q = implode(';', $paths);
	    
	    //css合并文件，若修改此文件路径算法，请同步css.php文件算法
        $_all_in_one_file = A_ROOT.'/tmp/'.md5($url_q).'.css';
        $file_time = file_exists($_all_in_one_file) ? filemtime($_all_in_one_file) : '';
        
	    echo '<link href="/get.css?f='.urlencode($url_q).'&_='.$file_time.'" rel="stylesheet" type="text/css" />';
	}
}


/**
 * 输出css文件
 */
function tpl_echo_ex_css()
{
    return tpl_include_ex_css();
}



/**
 * 获取Page Title
 * 
 * @return string
 */
function tpl_get_page_title_str()
{
	global $_TPL, $_SCONFIG;
	
	if( empty($_TPL['title']) ) return $_SCONFIG['site_name'];
	
	if( is_array($_TPL['title']) ) {
	    $_TPL['title'] = array_map('trim', $_TPL['title']);
	    $_TPL['title'] = array_filter($_TPL['title']);
		return implode("_", array_merge($_TPL['title'], array($_SCONFIG['site_name'])));
	}
	
	//string
	return $_TPL['title']."_".$_SCONFIG['site_name'];
}

/**
 * tpl article search
 * @param string $query_word
 * @param string $region_name
 * @param int $pg
 * @param int $ps
 */
function tpl_article_search($query_word, $region_name, $pg, $ps = 5) {
    global $_SGLOBAL;
    $query = array(
        'keyword' => $query_word,
        'region'  => $region_name,
    );
    $documents = $_SGLOBAL['pagedb']->keywordsParagraph_select(
        $query, null, array('score' => -1), $ps, $pg);
    $total_res_cnt = ceil($_SGLOBAL['pagedb']->res_count / $ps);
    foreach ($documents AS &$document) {
    	$article = $_SGLOBAL['pagedb']->Article_select_one(
    					array('_id' => new MongoID($document['documentID'])),
    					array('title', 'optDateTime', 'author')
    				);
    	empty($article) && $article = array();
    	$document['keyword'] = array(); //get_article_keywords($document['documentID'], 'tpl_article_keyword_format');
    	$document['images'] = array();
    	$document = array_merge($document, $article);
    	$tmp_paragraph_arr = array();
    	$tmp_cnt_arr = array();
    	foreach ($document['paragraphs'] AS $index => $paragraph) {
    		!isset($tmp_paragraph_arr[$paragraph['wordCount']]) &&
    		       $tmp_paragraph_arr[$paragraph['wordCount']] = $index;
    		$tmp_cnt_arr[$index] = $paragraph['wordCount'];
    	}
    	$selected_paragraph_num = $tmp_paragraph_arr[max($tmp_cnt_arr)];
    	$paragraph_start = $document['paragraphs'][$selected_paragraph_num]['start'];
    	$paragraph_end   = $document['paragraphs'][$selected_paragraph_num]['end'];
    	$selected_paragraphs_all = $_SGLOBAL['pagedb']->articleParagraphs_select_one(
    								array('documentID' => $document['documentID']),
    							    array('paragraphs')
    							);
    	if (empty($selected_paragraphs_all['paragraphs'])) {
    		continue;		
    	}
    	$selected_paragraphs = array_slice($selected_paragraphs_all['paragraphs'], $paragraph_start, $paragraph_end - $paragraph_start + 1);
    	
    	$document['content'] = strip_tags(implode("", array_map('tpl_trim', $selected_paragraphs)));
    	// modified by 2012-6-26
    	//$document['images'] = $document['paragraphs'][$selected_paragraph_num]['pictures'];
    	!isset($document['pictures']) && $document['pictures'] = array();
    	foreach ($document['pictures'] AS &$image) {
    		if(preg_match_all("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches)) {
    			$image = 'src="'.$matches[1][0].'"';
    		}
    		preg_match_all("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
    		$image = $matches[1][0];
    	}
    	unset($image);
    	$document['pictures'] = array_filter($document['pictures'], 'is_article_image_exists');
    }
    unset($document);
    return array($documents, $total_res_cnt);
}


function tpl_echo_article_image($images) {
    list($images, $width, $height) = tpl_article_sort_filter($images, 'width', 205, 110);
    if (($case = $count = count($images)) == 0) { return; } 
    if ($count < 3 && $width < 640) { return; }
    if ($count < 6 && $count > 4 && $width < 345) { $case = 3; }
    if ($count > 6 && $width < 640) { $case = 6; }
    echo <<<HTML
		<div class="youji_tu">
HTML;
    switch ($case) {
        case 1: case 2:
            $img = get_article_image($images[0], 640, 320);
            echo <<<HTML
            <h2><img src="$img" width="640" height="320"/></h2>
HTML;
        break;
        case 3: case 4:
            foreach ($images AS $index => $image) {
                if($index >= 3) { break; }
                $class = !$index ? 'class="wuno"' : "";
                $img = get_article_image($image, 205, 210);
                echo <<<HTML
            <h3 $class><img src="$img" width="205" height="210"/></h3>
HTML;
            }
        break;
        case 5:
            foreach ($images AS $index => $image) {
                switch ($index) {
                    case 0:
                        $img = get_article_image($image, 345, 210);
                        echo <<<HTML
            <h3 class="wuno"><img src="$img" width="345" height="210"/></h3>
HTML;
                    break;
                    case 1:
                        $img = get_article_image($image, 280, 210);
                        echo <<<HTML
            <h3><img src="$img" width="280" height="210"/></h3>
HTML;
                    break;
                    case 2:
                        $img = get_article_image($image, 205, 110);
                        echo <<<HTML
            <h3 class="wuno"><img src="$img" width="205" height="110"/></h3>
HTML;
                    break;
                    default:
                        $img = get_article_image($image, 205, 110);
                        echo <<<HTML
            <h3><img src="$img" width="205" height="110"/></h3>
HTML;
                    break;
                }
            }
        break;
        case 6:
            foreach ($images AS $index => $image) {
                if($index >= 6) { break; }
                $class = ($index == 0 || $index == 3) ? 'class="wuno"' : "";
                $height = ($index < 3) ? 210 : 110;
                $img = get_article_image($image, 205, $height);
                echo <<<HTML
            <h3 $class><img src="$img" width="205" height="$height"/></h3>
HTML;
            }
        break;
        default:
            foreach ($images AS $index => $image) {
                if($index > 6) { break; }
                switch ($index) {
                    case 0:
                        $img = get_article_image($image, 640, 320);
                        echo <<<HTML
            <h2><img src="$img" width="640" height="320"/></h2>
HTML;
                    break;
                    case 1: case 4:
                        $img = get_article_image($image, 205, 110);
                        echo <<<HTML
            <h3 class="wuno"><img src="$img" width="205" height="110"/></h3>
HTML;
                    break;
                    default:
                        $img = get_article_image($image, 205, 110);
                        echo <<<HTML
            <h3><img src="$img" width="205" height="110"/></h3>
HTML;
                    break;
                }
            } 
        break;
    }
    echo <<<HTML
		</div>
HTML;
}

function tpl_article_sort_filter($images, $type, $min_width, $min_height) {
    global $_SC;
    if (count($images) == 0) { return array($images, 0, 0);}
    $image_width_arr = $image_height_arr = $images_filter = array();
    foreach ($images AS $index => $img) {
        $filename = $_SC['article_img_dir'].md5($img);
        list($width, $height) = getimagesize($filename);
        if($width < $min_width || $height < $min_height) { continue; }
        $image_width_arr[$index] = $width;
        $image_height_arr[$index] = $height;
    }
    switch (strtolower($type)) {
        case 'width':
            arsort($image_width_arr);
            foreach ($image_width_arr AS $index => $width) {
                $images_filter[] = $images[$index];
            }
        break;
        case 'height':
            arsort($image_height_arr);
            foreach ($image_height_arr AS $index => $height) {
                $images_filter[] = $images[$index];
            }
        break;
        default:
            return array($images, max($image_width_arr), max($image_height_arr));
        break;
    }
    return array($images_filter, max($image_width_arr), max($image_height_arr));
}

