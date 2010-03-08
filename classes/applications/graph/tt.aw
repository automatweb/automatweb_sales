<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/graph/tt.aw,v 1.2 2007/12/06 14:33:30 kristo Exp $
/*

	QOTD: Real programmers do not comment their code - it was hard to write,
		it should also be hard to understand.

*/
/*
@classinfo maintainer=kristo
*/
class TTGraph
{
	//Attributes
	var $image;
	var $imageHeight;
	var $imageWidth;
	var $insideWidth;
	var $frameWidth;
	var $borderWidth;
	var $colSize;
	var $xItemCount;
	var $maxValue;
	var $minValue;
	var $vertice_rect;
	var $vertice_tri;
	var $RGB = array(
        "white"         => array(0xFF,0xFF,0xFF),
        "black"         => array(0x00,0x00,0x00),
        "gray"          => array(0x7F,0x7F,0x7F),
        "lgray"         => array(0xBF,0xBF,0xBF),
        "egray"         => array(0xDD,0xDD,0xDD),
        "dgray"         => array(0x3F,0x3F,0x3F),
        "blue"          => array(0x00,0x00,0xBF),
        "lblue"         => array(0x00,0x00,0xFF),
        "dblue"         => array(0x00,0x00,0x7F),
        "yellow"        => array(0xBF,0xBF,0x00),
        "lyellow"       => array(0xFF,0xFF,0x00),
        "dyellow"       => array(0x7F,0x7F,0x00),
        "green"         => array(0x00,0xBF,0x00),
        "lgreen"        => array(0x00,0xFF,0x00),
        "dgreen"        => array(0x00,0x7F,0x00),
        "red"           => array(0xBF,0x00,0x00),
        "lred"          => array(0xFF,0x00,0x00),
        "dred"          => array(0x7F,0x00,0x00),
        "purple"        => array(0xBF,0x00,0xBF),
        "lpurple"       => array(0xFF,0x00,0xFF),
        "dpurple"       => array(0x7F,0x00,0x7F),
        "gold"          => array(0xFF,0xD7,0x00),
        "pink"          => array(0xFF,0xB7,0xC1),
        "dpink"         => array(0xFF,0x69,0xB4),
        "marine"        => array(0x7F,0x7F,0xFF),
        "cyan"          => array(0x00,0xFF,0xFF),
        "lcyan"         => array(0xE0,0xFF,0xFF),
        "maroon"        => array(0x80,0x00,0x00),
        "olive"         => array(0x80,0x80,0x00),
        "navy"          => array(0x00,0x00,0x80),
        "teal"          => array(0x00,0x80,0x80),
        "silver"        => array(0xC0,0xC0,0xC0),
        "lime"          => array(0x00,0xFF,0x00),
        "khaki"         => array(0xF0,0xE6,0x8C),
        "lsteelblue"    => array(0xB0,0xC4,0xDE),
        "seagreen"      => array(0x3C,0xB3,0x71),
        "lseagreen"     => array(0x20,0xB2,0xAA),
        "skyblue"       => array(0x87,0xCE,0xEB),
        "lskyblue"      => array(0x87,0xCE,0xFA),
        "slateblue"     => array(0x6A,0x5A,0xCD),
        "slategray"     => array(0x70,0x80,0x90),
        "steelblue"     => array(0x46,0x82,0xB4),
        "tan"           => array(0xD2,0xB4,0x8C),
        "violet"        => array(0xEE,0x82,0xEE),
        "wheat"         => array(0xF5,0xDE,0xB3)
       );
	
	//Methods
	function TTGraph($border,$inside,$frame)
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
	}
	function GraphBase($width=400,$height=200,$bg="FFFFFF")
	{
		//Initialize Variables
		if ($width=="") 
		{
			$width=400;
		}
		if ($height=="") 
		{
			$height=200;
		}
		if ($bg=="") 
		{
			$bg=FFFFFF;
		}
		$imageWidth = $width;
		$imageHeight = $height;

		$borderWidth=$this->getBorderWidth();
		$frameWidth=$this->getFrameWidth();
		$insideWidth=$this->getinsideWidth();

		$spacing = $frameWidth + $borderWidth;
		//Create Image
		$image = imagecreate($imageWidth,$imageHeight);

		//Fill Background	
		$col_ar=$this->rgb2Array($bg);
		$colorBg = imagecolorallocate($image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		imagefill($image, 0, 0, $colorBg);
	
		//Draw dark frame border
		$colorDkGray = imagecolorallocate($image, 153, 153, 153);
		imagefilledrectangle($image, $imageWidth - ($spacing +1), $borderWidth, $imageWidth - $borderWidth, 	$imageHeight - $borderWidth, $colorDkGray);
		imagefilledrectangle($image, $borderWidth, $imageHeight - ($spacing +1), $imageWidth - $borderWidth, 	$imageHeight - $borderHeight, $colorDkGray);

		$colorBlack = imagecolorallocate($image, 0, 0, 0);
		imagefilledrectangle($image, 0, 0, $imageWidth, $borderWidth, $colorBlack);
		imagefilledrectangle($image, 0, $imageHeight - $borderWidth, $imageWidth, $imageHeight, $colorBlack);
		imagefilledrectangle($image, 0, 0, $borderWidth , $imageHeight, $colorBlack);
		imagefilledrectangle($image, $imageWidth - $borderWidth , 0, $imageWidth, $imageHeight, $colorBlack);

		//Draw white frame border
		$colorWhite = imagecolorallocate($image, 255, 255, 255);
		imagefilledrectangle($image, $borderWidth, $borderWidth, $imageWidth - $spacing, $spacing, $colorWhite);
		imagefilledrectangle($image, $borderWidth, $borderWidth, $frameWidth, $imageHeight - $spacing, $colorWhite);

		// Draw bottom corner
		imagefilledpolygon($image, 
			array($borderWidth, $imageHeight - ($spacing + 1), $frameWidth, $imageHeight - ($spacing + 1), 0,
			$imageHeight - $borderWidth), 3, $colorWhite);

		//Draw top corner	
		imagefilledpolygon($image, 
			array($imageWidth - ($borderWidth + 1), $borderWidth, $imageWidth - ($spacing + 1), $borderWidth,
			$imageWidth - ($spacing + 1), $spacing), 3, $colorWhite);

		//Draw inside box
		//black box
		$insideWidth += 1;
		imageline($image, $insideWidth, $insideWidth, $imageWidth - ($insideWidth + 3), $insideWidth, $colorBlack);
		imageline($image, $insideWidth, $insideWidth, $insideWidth, $imageHeight - ($insideWidth + 0), $colorBlack);
		imageline($image, $insideWidth, $imageHeight - ($insideWidth + 0), $imageWidth - ($insideWidth + 3), 
			$imageHeight - ($insideWidth + 0), $colorBlack);
		imageline($image, $imageWidth - ($insideWidth + 3), $insideWidth, $imageWidth - ($insideWidth + 3), 
			$imageHeight - ($insideWidth + 0), $colorBlack);
		//white box
		$insideWidth -= 1;
		imageline($image, $insideWidth, $insideWidth, $imageWidth - ($insideWidth + 5), $insideWidth, $colorWhite);
		imageline($image, $insideWidth, $insideWidth, $insideWidth, $imageHeight - ($insideWidth + 2), $colorWhite);
		imageline($image, $insideWidth, $imageHeight - ($insideWidth + 2), $imageWidth - ($insideWidth + 5), 
			$imageHeight - ($insideWidth + 2), $colorWhite);
		imageline($image, $imageWidth - ($insideWidth + 5), $insideWidth, $imageWidth - ($insideWidth + 5), 
			$imageHeight - ($insideWidth + 2), $colorWhite);


		$this->image = $image;
		$this->imageHeight = $imageHeight;
		$this->imageWidth = $imageWidth;

	}

	function getTriple()
	{	
		return ($this->borderWidth+$this->frameWidth+$this->insideWidth);
	}

	function rgb2Array($rgbstr)
	{
		$ar=array();
		$ar["r"]=hexdec(substr($rgbstr,0,2));
		$ar["g"]=hexdec(substr($rgbstr,-4,2));
		$ar["b"]=hexdec(substr($rgbstr,-2));
		return $ar;
	}
	
	function setBorderWidth($bor="1")
	{
		$this->borderWidth=$bor;
	}

	function getBorderWidth()
	{	
		if (isset($this->borderWidth)) 
		{
			return $this->borderWidth;
		}
		else
		{
			$this->setBorderWidth(1);
			return $this->borderWidth;
		}
	}

	function setFrameWidth($fr="4")
	{
		$this->frameWidth=$fr;
	}

	function getFrameWidth()
	{	
		if (isset($this->frameWidth)) 
		{
			return $this->frameWidth;
		} 
		else
		{
			$this->setFrameWidth(4);
			return $this->frameWidth;
		}
	}

	function setInsideWidth($ins="40")
	{
		$this->insideWidth=$ins;
	}

	function getInsideWidth()
	{	
		if (isset($this->insideWidth)) 
		{
			return $this->insideWidth;
		} 
		else
		{
			$this->setInsideWidth(40);
			return $this->insideWidth;
		}
	}
		
	function grid($count="10",$drawval=TRUE,$_col="000000")
	{
		if ($count=="") 
		{
			$count=10;
		}
		if ($_col=="") 
		{
			$_col="000000";
		}
		//Draw division lines
		$col_ar=$this->rgb2Array($_col);
		$padWidth = $this->imageHeight - (2 * ($this->insideWidth + 1));
		$gridHeight = $padWidth/$count;
		$colorDkGray = imagecolorallocate($this->image, 153, 153, 153);
		$colorBlack = imagecolorallocate($this->image,0,0,0);
		$color = imagecolorallocate($this->image,$col_ar["r"], $col_ar["g"], $col_ar["b"]);
		$range=(double)round(($this->maxValue-$this->minValue)/($count));
		$cur_ran=$range;
		for($i=($count-1) ; $i>0 ; $i--) 
		{
			imageline($this->image, $this->insideWidth + 2, $this->insideWidth + 2 + ($i * $gridHeight),
				 $this->imageWidth - ($this->insideWidth + 6), $this->insideWidth + 2 + ($i * $gridHeight), $colorDkGray);
			if ($drawval>0) 
			{
			imagestring($this->image,1,$this->borderWidth+$this->frameWidth+$this->insideWidth/2-(strlen(($cur_ran+$this->minValue))*imagefontwidth(1)/2), $this->insideWidth + ($i * $gridHeight) - 3,abs($cur_ran+$this->minValue),$color);
				$cur_ran+=$range;
			}
		}
	}

	function xaxis($values, $label="", $_col="000000")
	{
		if ($_col=="") 
		{
			$_col="000000";
		}
		$nextCol = $this->getTriple();
		$col_ar=$this->rgb2Array($_col);
		$colblack= imagecolorallocate($this->image, 0,0,0);
		$color= imagecolorallocate($this->image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		if (strlen($values[0])*imagefontwidth(1)>($this->colSize))
		{
//			for($j = 0; $j < $this->xItemCount; $j++)
//			if (($j%2)&&$j!=0) $values[$j]="";
			$count=(strlen($values[0])*imagefontwidth(1))/($this->colSize)+1;
			$k=0;
			for ($i = 0; $i < $this->xItemCount; $i+=$count)
			{
				$tmp[$k++]=$values[$i];
			}
			$colSize = ($this->imageWidth - (2 * ($this->getTriple())))/count($tmp);
			for ($i = 0; $i < count($tmp); $i++)
			{
				imagestring($this->image, 1, ($nextCol - (strlen($tmp[$i])/2)), ($this->imageHeight - ($this->insideWidth - 3)), $tmp[$i], $colblack);
				$nextCol += $colSize;
			}

		}
		else 
		{
			for ($i = 0; $i < $this->xItemCount; $i++)
			{
				imagestring($this->image, 1, ($nextCol + ($this->colSize/2))-(strlen($values[$i])/2), ($this->imageHeight - ($this->insideWidth - 3)), $values[$i], $color);
				$nextCol += $this->colSize;
			}
		}
		$centerx=($this->imageWidth/2)-((imagefontwidth(3)*strlen($label))/2);
		$y=$this->imageHeight-$this->insideWidth/2-$this->borderWidth-$this->frameWidth;		
		imagestring($this->image,2,$centerx,$y,$label,$color);
	}
	
	function yaxis($drawval=TRUE,$ylabel="", $_col="000000",$_col2="000000")
	{
		if ($_col=="") 
		{
			$_col="000000";
		}
		$col_ar=$this->rgb2Array($_col2);
		$col_label=$this->rgb2Array($_col);
		$color = imagecolorallocate($this->image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		$labelcolor = imagecolorallocate($this->image, $col_label["r"], $col_label["g"], $col_label["b"]);
		imagestring($this->image, 1, $this->getTriple()-(strlen((string)$ylabel)*imagefontwidth(1)/2), $this->insideWidth-18,$ylabel, $labelcolor);
		if ($drawval>0) 
		{
			imagestring($this->image,1,$this->borderWidth+$this->frameWidth+$this->insideWidth/2-(strlen(abs($this->maxValue))*imagefontwidth(1)/2), $this->insideWidth - 3,abs($this->maxValue),$color);
			imagestring($this->image,1,$this->borderWidth+$this->frameWidth+$this->insideWidth/2-(strlen(abs($this->minValue))*imagefontwidth(1)/2), $this->imageHeight - $this->insideWidth - $this->frameWidth-$this->borderWidth-3,abs($this->minValue),$color);
		}
	}

	function title($title="Line Graph", $_col="000000")
	{
		$col_ar=$this->rgb2Array($_col);
		$color = imagecolorallocate($this->image,  $col_ar["r"], $col_ar["g"], $col_ar["b"]);
		$centerx=($this->imageWidth/2)-((imagefontwidth(3)*strlen($title))/2);
		$centery=(($this->insideWidth+$this->frameWidth+$this->borderWidth)/2)-(imagefontheight(3)/2); 
		imagestring($this->image, 3, $centerx, $centery, $title, $color);
	}
	
	function parseData($xvalues,$yvalues) 
	{
		$max=-999999999999;
		$min=999999999999;
		$ycnt=0;
		while(list(,$v) = each($yvalues)) 
		{
			if (is_array($v))
			{					
				$ycnt++;
				while(list($ke,$val) = each($v)) 
				{
					if (!$val=="")
					{
						if ($max < $val) 
						{							
							$max = $val;
						}
						if ($min > $val) 
						{
							$min = $val;
						}
					}
//							echo "$ke = $val<br />";						
					if (is_array($val))
					{
						while(list($kee,$va) = each($val)) 
						{
	//								echo "$kee = $va<br />";						
						}
					}
				}
			} 
			else 
			{
				if (!$v=="")
				{
					if ($max < $v) $max = $v;
					if ($min > $v) $min = $v;
				}
//							echo "mh1 = $v<br />";						
			}
		}
		if (!is_array($values["ydata_0"])) 
		{
			$ycnt=1;
		}
		$this->ycnt=$ycnt;
		$xItemCount = count($xvalues);
		$colSize = ($this->imageWidth - (2 * ($this->getTriple()))-3)/$xItemCount;
		$this->colSize = $colSize;
		$this->xItemCount = $xItemCount;
		//Set this here zero for now... until someone dares to write a negative values algorith.
		$this->minValue=0;
		$this->maxValue=$max;
//		print("colsize: $colSize<br />xitemcount: $xItemCount<br />minval: $min<br />maxval: $max<br />");
//exit;		
	}

	function makeLine($values,$_col="000000")
	{	
		$markSize=5;
		$nextCol = $this->insideWidth;
		$colorBlack= imagecolorallocate($this->image, 0, 0, 0);
		$col_ar=$this->rgb2Array($_col);
		$color= imagecolorallocate($this->image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);

		$maxHeight = $this->getTriple(); 
		$minHeight = $this->imageHeight - $this->getTriple();
		$maxValue = $this->maxValue;

		$yp = (($minHeight - $maxHeight))/(($this->maxValue - $this->minValue));
		$height = abs((abs($values[0] - $this->minValue)*$yp)-$minHeight);
		$beginx=$nextCol + ($this->colSize/2);
		$beginy=$height;

		for ($i = 0; ($i < $this->xItemCount)&&($i<count($values)); $i++)
		{	
			if ($values[$i]==0) $values[$i]=0.0000001;
			if ($values[$i] > 0)
			{
				$height = ($minHeight - (abs($values[$i] - $this->minValue)*$yp));
				imageline($this->image,$beginx,$beginy,$nextCol + ($this->colSize/2),$height,$color);
				$beginx=$nextCol + ($this->colSize/2);
				$beginy = ($height+$this->minHeight);
	
				//Draws rectangles on every point
				if ($this->vertice_rect)
				{
					imagefilledrectangle($this->image, $nextCol + ($this->colSize/2)-2, $height-2, $nextCol +($this->colSize/2)+$markSize-2, $height+$markSize-2, $color);	
				}
					
				//Draws triangles on every point
				if ($this->vertice_tri) 
				{
					$point=array($nextCol + ($this->colSize/2)-4,$minHeight - $height,$nextCol + ($this->colSize/2)+4,$minHeight - $height,$nextCol + ($this->colSize/2),$minHeight - $height+5);
					imagefilledpolygon($this->image,$point,3,$color);
				}
			}
			$nextCol += $this->colSize;
		}
	}

	function draw()
	{
		header("Content-type: image/png");
		imagepng($this->image);
		imagedestroy($this->image);
	}
	function getImage()
	{
		return $this->image;
	}
}//end class LineGraph
?>
