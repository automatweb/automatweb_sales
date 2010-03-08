<?php
/*

	QOTD: Real programmers do not comment their code - it was hard to write,
		it should also be hard to understand.

*/
/*
@classinfo maintainer=kristo
*/
classload("applications/graph/tt");
class LineGraph extends TTGraph
{
	function LineGraph($border,$inside,$frame)
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

	function makeGraph($values,$colorarr)
	{
		return $this->makeLine($values,$colorarr);
	}

	//Joonistab base pildi peale jooned, sisendiks tahab saada väärtusi arrayna ja värvi
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

		if (($this->maxValue - $this->minValue) > 0)
		{
			$yp = (($minHeight - $maxHeight))/(($this->maxValue - $this->minValue));
		}
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
	
				//Draws rectangles on every point [NOT USED]
				if ($this->vertice_rect)
				{
					imagefilledrectangle($this->image, $nextCol + ($this->colSize/2)-2, $height-2, $nextCol +($this->colSize/2)+$markSize-2, $height+$markSize-2, $color);	
				}
					
				//Draws triangles on every point [NOT USED]
				if ($this->vertice_tri) 
				{
					$point=array($nextCol + ($this->colSize/2)-4,$minHeight - $height,$nextCol + ($this->colSize/2)+4,$minHeight - $height,$nextCol + ($this->colSize/2),$minHeight - $height+5);
					imagefilledpolygon($this->image,$point,3,$color);
				}
			}
			$nextCol += $this->colSize;
		}
	}

}//end class LineGraph
?>
