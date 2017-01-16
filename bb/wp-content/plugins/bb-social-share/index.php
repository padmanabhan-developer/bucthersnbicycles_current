<?php 

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

function getAbsoluteImageUrl($imgUrl)
{
	if (!strpos($imgUrl, $_SERVER['HTTP_HOST']))
		$imgUrl = 'http://' . $_SERVER['HTTP_HOST'] .$imgUrl;
	
	return $imgUrl;
}

if (isset($_GET['share_to'])
		&&isset($_GET['og_title']) 
		&& isset($_GET['og_descr']) 
		&& isset($_GET['share_url']) 
		&& isset($_GET['og_image']))
{
	$title = base64_encode($_GET['og_title']);
	$descr = base64_encode($_GET['og_descr']);
	$url = $_GET['share_url'];
	$image = base64_encode(getAbsoluteImageUrl($_GET['og_image']));
	
	switch ($_GET['share_to'])
	{
		case 'facebook':
			$shareUrl = 'http://www.facebook.com/sharer.php?u=' 
				. urlencode(
						plugins_url('/index.php', __FILE__) 
						.'?og_title='.$title
						.'&og_description='.$descr
						.'&og_image='.$image
						.'&og_url='. base64_encode($url)
						.'&ts='.  base64_encode(time())
				);
			echo json_encode($shareUrl);
		break;
	
		case 'googleplus':
			$shareUrl = 'https://plus.google.com/share?url=' 
				. urlencode(
						plugins_url('/index.php', __FILE__)
						.'?og_title='.$title
						.'&og_description='.$descr
						.'&og_image='.$image
						.'&og_url='.  base64_encode($url)
						.'&ts='.  base64_encode(time())
				);
			echo json_encode($shareUrl);
		break;
	
		case 'twitter':
			$shareUrl = 'https://twitter.com/home?status=' . $_GET['og_title'] . ' '
				. urlencode($url);
			echo json_encode($shareUrl);
		break;
	
		case 'stumbleupon':
			$shareUrl = 'http://www.stumbleupon.com/submit?url='
				. urlencode(
						plugins_url('/index.php', __FILE__)
						.'?og_title='.$title
						.'&og_description='.$descr
						.'&og_image='.$image
						.'&og_url='.  base64_encode($url)
						.'&ts='.  base64_encode(time())
				);
			echo json_encode($shareUrl);
		break;
	
		case 'pinterest':
			$shareUrl = 'http://www.pinterest.com/pin/create/button/?media=' . $_GET['og_image'] 
				. '&url=' . urlencode($_GET['share_url'])
				. '&description=' . urlencode($_GET['og_descr']
				.'&ts='.  base64_encode(time())
			);
			
			echo json_encode($shareUrl);
		break;
	}
}

if (isset($_GET['og_title']) 
		&& isset($_GET['og_description']) 
		&& isset($_GET['og_url']) 
		&& isset($_GET['og_image']))
{
	$url = base64_decode($_GET['og_url']);
	
	if (isset($_GET['ts']) && base64_decode($_GET['ts']) + 60 < time()) 
	{
		header("Location: ". $url);
		die();
	}
	
	ob_start();
	?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>B&amp;B Share</title>
			<meta name="description" content="Our first cargo bike — simply called the Mk I – is for anyone who loves cycling. 
				  With it, we intend to challenge the perception of how fun and easy riding a cargo bike can be without compromising usability.">
			<meta property="og:title" content="<?php echo base64_decode($_GET['og_title']); ?>">
			<meta property="og:type" content="website">
			<meta property="og:description" content="<?php echo base64_decode($_GET['og_description']); ?>">
			
			<meta property="og:url" content="<?php echo $url; ?>"/>
			<link rel="canonical" href="<?php echo $url; ?>" />
			
			<meta property="og:image" content="<?php echo base64_decode($_GET['og_image']); ?>"/>
		</head>
		<body>
			<g:plus action="share"></g:plus>
		</body>
		</html>
	<?php
	
	ob_flush();
}