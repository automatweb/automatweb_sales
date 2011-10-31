<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $lang?>" xml:lang="<?php echo $lang?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>" />
<title><?php echo $title?></title>
<?php if ($meta_description){?><meta name="description" content="<?php echo $meta_description?>" /><?php }?>
<?php if ($meta_keywords){?><meta name="keywords" content="<?php echo $meta_keywords?>" /><?php }?>
<?php if ($meta_abstract){?><meta name="abstract" content="<?php echo $meta_abstract?>" /><?php }?>
<?php if ($meta_author){?><meta name="author" content="<?php echo $meta_author?>" /><?php }?>
<?php if ($meta_copyright){?><meta name="copyright" content="<?php echo $meta_copyright?>" /><?php }?>
<?php if ($meta_revisit_after){?><meta name="revisit-after" content="<?php echo $meta_revisit_after?>" /><?php }?>
<?php if ($meta_distribution){?><meta name="distribution" content="<?php echo $meta_distribution?>" /><?php }?>
<?php if ($meta_robots){?><meta name="robots" content="<?php echo $meta_robots?>" /><?php }?>
<?php if ($meta_rating){?><meta name="rating" content="<?php echo $meta_rating?>" /><?php }?>
<?php if ($meta_generator){?><meta name="generator" content="<?php echo $meta_generator?>" /><?php }?>

<?php if ($meta_content_language){?><meta http-equiv="content-language" content="<?php echo $meta_content_language?>" /><?php }?>
<?php if ($meta_pragma){?><meta http-equiv="pragma" content="<?php echo $meta_pragma?>" /><?php }?>
<?php if ($meta_refresh){?><meta http-equiv="refresh" content="<?php echo $meta_refresh?>" /><?php }?>
<?php if ($meta_expires){?><meta http-equiv="expires" content="<?php echo $meta_expires?>" /><?php }?>
<?php if ($meta_window_target){?><meta http-equiv="window-target" content="<?php echo $meta_window_target?>" /><?php }?>

<?php foreach ($style_files as $file){?><link href="<?php echo $file?>" rel="stylesheet" type="text/css" /><?php }?>
<?php if ($style){?>
<style type="text/css">
<?php echo $style?>
</style>
<?php }?>
<?php foreach ($javascript_files_header as $file){?>
<script language="Javascript" type="text/javascript" src="<?php echo $file?>"></script>
<?php }?>
<?php if ($javascript_header){?>
<script language="Javascript" type="text/javascript">
<?php echo $javascript_header?>
</script>
<?php }?>
</head>

<body>
<?php echo $content?>
</body>

<?php if ($javascript_footer){?>
<script language="Javascript" type="text/javascript">
<?php echo $javascript_footer?>
</script>
<?php }?>

</html>
