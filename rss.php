<?php
header('Content-Type: application/rss+xml');

require('lib/common.php');

$fieldlist = '';
$ufields = array('id','name','group_id');
foreach($ufields as $field)
	$fieldlist .= "u1.$field u1$field, u2.$field u2$field, ";

$mintime = time()-3*86400;

$threads = $sql->query("SELECT $fieldlist t.*, f.id fid, f.title ftitle "
		."FROM threads t "
		."LEFT JOIN users u1 ON u1.id=t.user "
		."LEFT JOIN users u2 ON u2.id=t.lastuser "
		."LEFT JOIN forums f ON f.id=t.forum "
		."LEFT JOIN categories c ON f.cat=c.id "
		."WHERE f.id IN ".forums_with_view_perm()." "
		.  "AND c.id IN ".cats_with_view_perm()." "
		.  "AND t.lastdate>$mintime "
		."ORDER BY t.lastdate DESC "
		."LIMIT 20");
$t = $sql->fetch($threads);

echo '<?xml version="1.0"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>
	<title><?=$boardtitle?></title>
	<copyright>Posts are owned by the poster. Acmlmboard <?=$abversion?> software Copyright 2005-2015 <?=$boardprog?></copyright>
	<generator>Acmlmboard <?=$abversion?> (<?=$abdate?>)</generator>
	<ttl>5</ttl>
	<atom:link href="<?=$config['base']?><?=$url?>" rel="self" type="application/rss+xml" />
	<language>en</language>
	<category>forum</category>
	<link><?=$config['base']?><?=$config['path']?></link>
	<description>The latest active threads of <?=$boardtitle?></description>
	<image>
		<url><?=$config['base']?><?=$config['path']?>theme/abII.png</url>
		<title>$boardtitle</title>
		<link><?=$config['base']?><?=$config['path']?></link>
	</image>
	<lastBuildDate><?=date('r',$t['lastdate'])?></lastBuildDate>
<?php do { ?>
	<item>
		<title><?=$t['title']?> - <?=date("[$loguser[timeformat]]",$t['lastdate'])?> by <?=$t['u2name']?></title>
		<description>Last post by &lt;a href="<?=$config['base']?><?=$config['path']?>profile.php?id=<?=$t['u2id']?>"&gt;<?=$t['u2name']?>&lt;/a&gt;,
			thread by &lt;a href="<?=$config['base']?><?=$config['path']?>profile.php?id=<?=$t['u1id']?>"&gt;<?=$t['u1name']?>&lt;/a&gt;
			in &lt;a href="<?=$config['base']?><?=$config['path']?>forum.php?id=<?=$t['forum']?>"&gt;<?=$t['ftitle']?>&lt;/a&gt;</description>
		<pubDate><?=date("r",$t['lastdate'])?></pubDate>
		<category><?=$t['ftitle']?></category>
		<guid><?=$config['base']?><?=$config['path']?>thread.php?pid=<?=$t['lastid']?>#<?=$t['lastid']?></guid>
	</item>
<?php } while($t = $sql->fetch($threads)); ?>
</channel></rss>
