<?php
/*
@classinfo maintainer=kristo
*/
classload("applications/graph/tt");
class PieGraph extends TTGraph
{
	var $image;
	var $dataset;
	var $labels;
	var $width;
	var $height;
	var $radius;
	var $colorset  = array("skyblue","lred","green","lblue","pink","purple","gold","marine");

	function PieGraph($border,$inside,$frame) 
	{
		if ($border=="") 
		{
			$border=1;
		}
		if ($frame=="") 
		{
			$border=5;
		}
		if ($inside=="") 
		{
			$inside=40;
		}
		$this->setBorderWidth($border);
		$this->setFrameWidth($frame);
		$this->setInsideWidth($inside);
		lc_load("definition");
	}// pie 

	function parsedata($darr)
	{
		$this->labels=explode (",",$darr["labels"]);
		$d=$darr["data"];
		$data = explode (",", $d);
		$sum=0;
		$this->count=count($data);
		if ($this->count) 
		{
			foreach($data as $k=>$v)
			{
				$sum+=$v;
			}
			if($sum <= 0) 
			{ 
				$sum = 1; 
			}
			reset($data);
			while (list($k,$v)=each($data))
			{
				$degrees[$k]=round(((double)abs($v)/(double)$sum)*360.0);
			}
			$this->dataset=$degrees;
		}

	}//parsedata

	function create($r=150,$percent=1,$label=0) 
	{		
		//debug
		$drawmark=$percent;
		$drawlabel=$label;	
		$RGB=$this->RGB;
		//$col_ar=$this->rgb2Array($_col);
		//$color= imagecolorallocate($this->image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		//imagefill($this->image, 0, 0, $color);
		$black= imagecolorallocate($this->image, 0,0,0);
		$cy=$this->imageHeight/2; $cx=$this->imageWidth/2;
		$this->radius=$r;
		$wid1=290; $hei1=260;
		$start=0;
		$dataset=$this->dataset;
		$numclr=count($this->colorset);
		imagearc($this->image, $cx, $cy, $r*2, $r*2, 0, 360, $black);
		for($i = 0; $i < count($dataset) ; ++$i) 
		{		
			$startx	= $cx + round($r * cos($start*pi()/180));
			$starty	= $cy - round($r * sin($start*pi()/180));
			$end		= $start + $dataset[$i];
			$centerx= $cx + round(0.7*$r*cos(($start+0.5*$dataset[$i])*pi()/180));
			$centery= $cy - round(0.7*$r*sin(($start+0.5*$dataset[$i])*pi()/180));
			$outerx = $cx + round(1.25*$r*cos(($start+0.5*$dataset[$i])*pi()/180));
			$outery = $cy - round(1.15*$r*sin(($start+0.5*$dataset[$i])*pi()/180));
			ImageLine($this->image,$cx,$cy,$startx,$starty,$black);

			$col = $this->colorset[$i];
			$col_ar=$RGB[$col];
			$col=imagecolorallocate($this->image, $col_ar["0"], $col_ar["1"], $col_ar["2"]);
			imagefilltoborder($this->image,$centerx,$centery,$black,$col);
			
			if ($drawmark) 
			{
				$str= round($dataset[$i] * 10 / 36) . '%';
				$fx=$centerx-imagefontwidth(3)*strlen($str)/2;
				$fy=$centery-4;
				imagestring($this->image,3,$fx,$fy,$str,$black);
			}

			if ($drawlabel) 
			{
				$str=$this->labels[$i];
				$fx=$outerx-imagefontwidth(3)*strlen($str)/2;
				$fy=$outery-4;
				imagestring($this->image,3,$fx,$fy,$str,$black);
			}
			$start = $end;
		}
	} //create

	function title($title="Graph", $_col="000000")
	{
		$col_ar=$this->rgb2Array($_col);
		$color = imagecolorallocate($this->image,  $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		$centerx=($this->imageWidth/2)-((imagefontwidth(3)*strlen($title))/2);
		$centery=(($this->imageHeight/2)-($this->radius)-(imagefontheight(3)/2))/2; 
		imagestring($this->image, 5, $centerx, $centery, $title, $color);
	}
}//class pie
?>
