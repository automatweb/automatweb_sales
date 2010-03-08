<?php
/*
@classinfo maintainer=kristo
*/
classload("applications/graph/tt");
class BarGraph extends TTGraph
{
	var $ycnt;

	function BarGraph($border,$inside,$frame)
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
					if ($max < $v) 
					{
						$max = $v;
					}
					if ($min > $v) 
					{
						$min = $v;
					}
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
			$colsz=floor((($this->imageWidth - 2*$this->getTriple())-4)/$this->xItemCount);
			$nextCol += 4;
/*			$ycnt=$this->ycnt;
			$ms=9;
			$sm_colsize=floor(($this->imageWidth -(2*$this->getTriple())-4-(($this->xItemCount-1)*$ms))/$this->xItemCount/$ycnt);			
			$calculated=$sm_colsize*$this->xItemCount*$ycnt+($this->xItemCount-1)*$ms;
			$actual=$this->imageWidth - (2*$this->getTriple())-4;
			($actual>$calculated)?$sm_cur=$nextCol+($actual-$calculated)/2:$sm_cur=$nextCol;
*/			
			for ($i = 0; $i < $this->xItemCount; $i++)
			{				
				//	imagestring($this->image, 1, ($sm_cur)-((imagefontwidth(1)*strlen($values[$i]))/2), ($this->imageHeight - ($this->insideWidth - 3)), $values[$i], $color);
				//	$sm_cur += $colsz;
				imagestring($this->image, 1, ($nextCol + ($this->colSize/2))-(imagefontwidth(3)*strlen($values[$i])/2), ($this->imageHeight - ($this->insideWidth - 3)), $values[$i], $color);
				$nextCol += $colsz;
			}
		}
		$centerx=($this->imageWidth/2)-((imagefontwidth(3)*strlen($label))/2);
		$y=$this->imageHeight-$this->insideWidth/2-$this->borderWidth-$this->frameWidth;		
		imagestring($this->image,2,$centerx,$y,$label,$color);
	}

	function makeGraph($values,$colorarr)
	{
		return $this->makeBar($values,$colorarr);
	}

	function makeBar($values,$colorarr)
	{	
		$ycnt=count($values);
		$this->minValue=0;

		/*
		* Lisame paindlikkust, argumentideks sobivad nii teatud kujul esitadud data arrayde arrayd, kui ka lihtsalt data arrayd.
		*
		*/
		
		if (!is_array($values["ydata_0"]))
		{
			$values["ydata_0"]=$values;
			$ycnt=1;
		}
		if (!is_array($colorarr)) 
		{
			$colorarr=array("ycol_0" => $colorarr);		
		}

		$nextCol = $this->getTriple();
		$colorBlack= imagecolorallocate($this->image, 0, 0, 0);

		$maxHeight = $this->getTriple(); 
		$minHeight = $this->imageHeight - $this->getTriple();
		$maxValue = $this->maxValue;
	
		$yp = (($minHeight - $maxHeight))/(($this->maxValue - $this->minValue));

		$ms=9;
		$sm_colsize=floor(($this->imageWidth - (2*$this->getTriple())-4-(($this->xItemCount-1)*$ms))/$this->xItemCount/$ycnt);
		
		$calculated=$sm_colsize*$this->xItemCount*$ycnt+($this->xItemCount-1)*$ms;
		$actual=$this->imageWidth - (2*$this->getTriple())-4;
		($actual>$calculated)?$sm_cur=$nextCol+($actual-$calculated)/2:$sm_cur=$nextCol;

//		imagestring($this->image,3,520,15,$sm_colsize,$colorBlack);
//		imagestring($this->image,3,520,25,$calculated,$colorBlack);	
	
		$numofval=count($values["ydata_0"]);
		for ($i = 0; ($i < $this->xItemCount)&&($i<$numofval); $i++)		
		{		
			for ($j = 0; $j<$ycnt; $j++)
			{		
				$height = ($minHeight - (abs($values["ydata_".$j][$i] - $this->minValue)*$yp));
				if ($height != $minHeight)
				{
					imagerectangle($this->image, $sm_cur, $height, $sm_cur+$sm_colsize, $minHeight, $colorBlack);
					$col_ar=$this->rgb2Array($colorarr["ycol_".$j]);
					$color= imagecolorallocate($this->image, $col_ar["r"], $col_ar["g"], $col_ar["b"]);
					imagefilltoborder($this->image, $sm_cur+1,$height+1, $colorBlack, $color);
				}
				$sm_cur+=$sm_colsize;
			}			
			$sm_cur+=$ms;
		}
	}
}
?>
