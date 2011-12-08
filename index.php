<?
// VideoGrid
// Uses Youtube API to search for videos, order by views, and output thumbnails.
// Thumbnails may be 3x3, 2x2, or 1x1 based on number of views.
// Thumbnails placed on grid 

$grid = array(
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
	array( 0,  0,  0,  0,  0,  0,  0,  0,  0,  0 ),
);
$start = array(3,3 , 6,6); // range on grid where to place first thumb (x1,y1 , x2,y2)

if (isset($_GET['q']) && !empty($_GET['q'])) {

	// dim some vars
	$vids = array();
	
	// get results from search; converting xml to array
	$url = "http://gdata.youtube.com/feeds/api/videos?q=".urlencode($_GET['q'])."&orderby=viewCount&max-results=10&v=2";
	$arr = json_decode(json_encode((array) simplexml_load_string(file_get_contents($url))),1);
	unset($url);
	$arr = $arr['entry'];
	foreach ($arr as $a) {
		$id = array_pop(explode(":",$a['id']));
		// get views for this video
		$JSON_Data = json_decode(file_get_contents("http://gdata.youtube.com/feeds/api/videos?q={$id}&alt=json"));
		$views = $JSON_Data->{'feed'}->{'entry'}[0]->{'yt$statistics'}->{'viewCount'};
		// save vid to array
		$vids[] = array( "id" => $id , "title" => $a['title'], "views" => $views);
	}
	unset($url,$xml,$arr);

	// now that we've got all the vids, lets compare views
	$maxviews = $vids[0]['views'];
	foreach ($vids as &$vid) {
		$vid['cells'] = ceil(((float)$vid['views'] / (float)$maxviews) * 3.0 );
	}
	print_r($vids);
	
	// now that you have vids and the number of cells each should occupy, fill out the grid.
	
}

function outputGrid($grid, $return=false) {
	$echo = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" >";
	foreach($grid as $row) {
		$echo .= "<tr height=\"75\">";
		foreach ($row as $cell) {
			$echo .= "<td align=\"center\" width=\"75\">" . print_r($cell,1) . "</td>";
		}
		$echo .= "</tr>";
	}
	$echo .= "</table>";
	if ($return) {
		return $echo;
	} else {
		echo $echo;
		return true;
	}
}
?>
<!doctype html>
<html>
<head>
<title>Youtube Grid v0.1</title>
<style>
#container {
	width: 776px;
	text-align:center;
	margin: 0 auto;
}
.left {float: left}
.right{float:right}
.clear{clear: both}
</style>
</head>
<body>
<div id="container">
<div class="left">
	<p><strong>Youtube Grid v0.1</strong></p>
</div>
<div class="right">
	<form action="" method="GET"><p>Search: <input type="search" name="q" value="<?=(isset($_GET['q']) ? $_GET['q'] : "")?>"><input type="submit" value="Search"></p></form>
</div>
<div class="clear"></div>
<?
outputGrid($grid);
?> 
</div>
</body>
</html>