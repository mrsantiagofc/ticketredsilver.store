<?php

require_once("vimeo_api.php");

//if(IsNullOrEmpty($vimeo_key) || IsNullOrEmpty($vimeo_secret) || IsNullOrEmpty($vimeo_token)) exit("PHP Vimeo access information missing!");

if(empty($vimeo_key)){
	$vimeo_key = "80bddb606ace9d6d9f331c6d89303a1d17e18e18"; 
	$vimeo_secret = "UhVyfik6YRFrHnui0OdTRe7vOQjH1MOhQArApO+q90PkweZOth/28kVoxUVX3pI1HKTYAgz2WDHOI7q5AdSmPg4g3YbPSv2IHteuyOVUU1axSRVR7MJawaYQKC3ncrHf";
	$vimeo_token = "0494c1c544fabda79ea36ae62db3487b";
}

function IsNullOrEmpty($v){
	return (!isset($v) || trim($v)==='');
}

$type = $_REQUEST['type'];
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : null;
$perPage = isset($_REQUEST['perPage']) ? $_REQUEST['perPage'] : null;
$path = isset($_REQUEST['path']) ? $_REQUEST['path'] : null;
$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'date';
$sortDirection = isset($_REQUEST['sortDirection']) ? $_REQUEST['sortDirection'] : 'asc';
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
$filter_embeddable = isset($_REQUEST['filter_embeddable']) ? $_REQUEST['filter_embeddable'] : '';

require("autoload.php");
use Vimeo\Vimeo;
$vimeo = new Vimeo($vimeo_key, $vimeo_secret, $vimeo_token);


if($type == 'next_page'){//resume

	$result = $vimeo->request($path);

}else if($type == 'vimeo_channel'){

	//Get a list of videos in a Channel - https://developer.vimeo.com/api/playground/channels/{channel_id}/videos
	$result = $vimeo->request("/channels/$path/videos", array(
													'page'=> $page,
													'per_page' => $perPage,
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													'sort' => $sort,
													'filter' => $filter,
				 									'filter_embeddable' => $filter_embeddable
													));

	if($sort != 'default')$arr['direction'] = $sortDirection;	

}else if($type == 'vimeo_group'){														
												
	//Get a list of videos in a Group - https://developer.vimeo.com/api/playground/groups/{group_id}/videos
	$result = $vimeo->request("/groups/$path/videos", array(
													'page'=> $page,
													'per_page' => $perPage,
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													'sort' => $sort,
													'filter' => $filter,
				 									'filter_embeddable' => $filter_embeddable
													));

	if($sort != 'default')$arr['direction'] = $sortDirection;	


}else if($type == 'vimeo_user_album'){	
	
	//Get the list of videos in an Album - https://developer.vimeo.com/api/reference/showcases#get_showcase_videos
	$result = $vimeo->request("/users/$user_id/albums/$path/videos", array(
													'page'=> $page,
													'per_page' => $perPage,
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													'sort' => $sort,
													'filter' => $filter,
												    'filter_embeddable' => $filter_embeddable
													));

	if($sort != 'default')$arr['direction'] = $sortDirection;	
	
}else if($type == 'vimeo_album'){	
	//https://stackoverflow.com/questions/27833848/vimeo-api-v3-get-videos-by-album-id
	
	//Get the list of videos in an Album - https://developer.vimeo.com/api/playground/users/{user_id}/albums/{album_id}/videos
	$result = $vimeo->request("/albums/$path/videos", array(
													'page'=> $page,
													'per_page' => $perPage,
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													'sort' => $sort,
													'filter' => $filter,
				 									'filter_embeddable' => $filter_embeddable
													));

	if($sort != 'default')$arr['direction'] = $sortDirection;	

}else if($type == 'vimeo_user_videos'){	

	$arr = array('page'=> $page,
				 'per_page' => $perPage,
				 'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
				);
	
	$result = $vimeo->request("/users/$user_id/videos", $arr);

}else if($type == 'vimeo_ondemand'){	
	//https://stackoverflow.com/questions/59132417/how-to-get-ondemand-ids-for-vimeo-api

	$arr = array('page'=> $page,
				 'per_page' => $perPage,
				 'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
				 'sort' => $sort,
				 'filter' => $filter
				);	

	if($sort != 'default')$arr['direction'] = $sortDirection;	
	
	$result = $vimeo->request("/ondemand/pages/$path/videos", $arr);


}else if($type == 'vimeo_folder'){	

	$arr = array('page'=> $page,
				 'per_page' => $perPage,
				 'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
				 'sort' => $sort,
				);	

	if($sort != 'default')$arr['direction'] = $sortDirection;	
	
	$result = $vimeo->request("/users/$user_id/projects/$path/videos", $arr);


}else if($type == 'vimeo_video_query'){	
												
	//Search for videos - https://developer.vimeo.com/api/playground/videos
	$result = $vimeo->request("/videos", array(
													'page'=> $page,
													'per_page' => $perPage,
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													'sort' => $sort,
													'query' => $path,
													'filter' => 'content_rating',
													'filter_content_rating' => ['language','drugs','violence','nudity','safe','unrated'],
													));

	if($sort != 'default')$arr['direction'] = $sortDirection;	
													
}else if($type == 'vimeo_single'){	

	//Get a video - https://developer.vimeo.com/api/playground/videos/{video_id}
	$result = $vimeo->request("/videos/$path", array(
													'fields' => 'uri,name,description,download,link,duration,release_time,privacy,pictures.sizes,user.account',
													));
}

echo json_encode($result);
exit;

?>