<?php
/**
* File holding the Google chart API wrapper class
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/

/***********************************************************************

  Copyright (C) 2008  Fabrice Douteaud (admin@clearbudget.net)

    This file is part of ClearBudget.

    ClearBudget is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ClearBudget is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ClearBudget.  If not, see <http://www.gnu.org/licenses/>.

************************************************************************/


class GoogleChart {
	// Constants
	const BASE_URL = 'http://chart.apis.google.com/chart?';
	protected $type = null;
	protected $title = '';
	protected $colors = '';
  protected $legend = '';
  protected $cols = '';
  protected $xAxis = null;
  protected $yAxis = null;
  protected $width = 0;
  protected $height = 0;
  protected $scaling = null;
  protected $lineStyle = null;
  protected $transparent = false;

  public function addxAxis($value) {
    $this->xAxis .= $value.'|';
    }
  
  public function addyAxis($value) {
    $this->yAxis .= $value.'|';
    }
      
  public function addValueSerie() {
    $this->cols = substr($this->cols, 0, strlen($this->cols)-1);
    $this->cols .= '|';
    }
    
  public function addValue($value) {
    $this->cols .= $value.',';
    }

  public function addLineStyle($value) {
    // thickness,length of line segment,length of blank segment
    if($this->lineStyle != null) $this->lineStyle .= '|';
    $this->lineStyle .= $value;
  }

  public function addColor($color) {
    $this->colors .= $color.',';
    }
  
  public function addLegend($legend) {
    $this->legend .= urlencode($legend).'|';
    }
  public function addTransparency() {
    $this->transparent = true;
  }
  private function setType($type) {
    $this->type = $type;
    }
    
  private function setTitle($title) {
    $this->title = urlencode($title);
    }
  
  private function setGraphSize($width, $height) {
    $this->width = $width;
    $this->height = $height;
    }
    
  public function addScaling($min, $max) {
    $this->scaling = "chds={$min},{$max}";
    }
  public function getURL() {
    if($this->cols != '') $cols = substr($this->cols, 0, strlen($this->cols)-1);
    else $cols = '';
    if($this->legend != '') $legend = substr($this->legend, 0, strlen($this->legend)-1);
    else $legend = '';
    if($this->colors != '') $colors = substr($this->colors, 0, strlen($this->colors)-1);
    else $colors = '';
    ($this->xAxis != '')?$xAxis = substr($this->xAxis, 0, strlen($this->xAxis)-1):$xAxis='';
    ($this->yAxis != '')?$yAxis = substr($this->yAxis, 0, strlen($this->yAxis)-1):$yAxis='';
    
        
    $url = 'cht='.$this->type.'&';
    if(strlen($this->title)>0) $url .= 'chtt='.$this->title.'&';
    if($this->transparent) $url .= 'chf=a,s,AAAAAAAA&';
    $url .= 'chco='.$colors.'&';
    $url .= 'chs='.$this->width.'x'.$this->height.'&';
    $url .= 'chxt=x,y&';
    $url .= 'chxl=0:|'.$xAxis.'|1:|'.$yAxis.'&';
    if(strlen($legend)>0 && ($this->type == 'bvs'||$this->type == 'lc')) $url .= 'chdl='.$legend.'&';
    elseif(strlen($legend)>0 && $this->type == 'p3') $url .= 'chl='.$legend.'&';
    $url .= 'chd=t:'.$cols.'&';
    if($this->lineStyle) $url .= 'chls='.$this->lineStyle.'&';
    if($this->scaling) $url .= $this->scaling;
    

    // build the final URL
    $url = self::BASE_URL.$url;
    return $url;
    }
  public function __construct($type, $title, $width, $height) {
    $this->setType($type);
    $this->setTitle($title);
    $this->setGraphSize($width, $height);
    }
  }
?>