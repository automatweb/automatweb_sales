<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?=$lang?>" xml:lang="<?=$lang?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>" />
<title><?=$title?></title>
<?if ($meta_description){?><meta name="description" content="<?=$meta_description?>" /><?}?>
<?if ($meta_keywords){?><meta name="keywords" content="<?=$meta_keywords?>" /><?}?>
<?if ($meta_abstract){?><meta name="abstract" content="<?=$meta_abstract?>" /><?}?>
<?if ($meta_author){?><meta name="author" content="<?=$meta_author?>" /><?}?>
<?if ($meta_copyright){?><meta name="copyright" content="<?=$meta_copyright?>" /><?}?>
<?if ($meta_revisit_after){?><meta name="revisit-after" content="<?=$meta_revisit_after?>" /><?}?>
<?if ($meta_distribution){?><meta name="distribution" content="<?=$meta_distribution?>" /><?}?>
<?if ($meta_robots){?><meta name="robots" content="<?=$meta_robots?>" /><?}?>
<?if ($meta_rating){?><meta name="rating" content="<?=$meta_rating?>" /><?}?>
<?if ($meta_generator){?><meta name="generator" content="<?=$meta_generator?>" /><?}?>

<?if ($meta_content_language){?><meta http-equiv="content-language" content="<?=$meta_content_language?>" /><?}?>
<?if ($meta_pragma){?><meta http-equiv="pragma" content="<?=$meta_pragma?>" /><?}?>
<?if ($meta_refresh){?><meta http-equiv="refresh" content="<?=$meta_refresh?>" /><?}?>
<?if ($meta_expires){?><meta http-equiv="expires" content="<?=$meta_expires?>" /><?}?>
<?if ($meta_window_target){?><meta http-equiv="window-target" content="<?=$meta_window_target?>" /><?}?>

<?foreach ($style_files as $file){?><link href="<?=$file?>" rel="stylesheet" type="text/css" /><?}?>
<?if ($style){?>
<style type="text/css">
<?=$style?>
</style>
<?}?>
<?foreach ($javascript_files_header as $file){?>
<script language="Javascript" type="text/javascript" src="<?=$file?>"></script>
<?}?>
<?if ($javascript_header){?>
<script language="Javascript" type="text/javascript">
<?=$javascript_header?>
</script>
<?}?>
</head>

<body>
<?=$content?>
</body>

<?if ($javascript_footer){?>
<script language="Javascript" type="text/javascript">
<?=$javascript_footer?>
</script>
<?}?>

</html>
