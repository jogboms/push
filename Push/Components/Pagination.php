<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Components;

class Pagination
{

  public $sql, $current, $total, $pages, $link;
  public $limit, $offset, $start, $end, $skip, $next, $prev;
  public $param = array(
    'seoLinks' => false
    );

   ##-[ Methods ]-----------------#
  function __construct($param=null)
  {
    if(is_array($param)){
      foreach ($param as $key => $value) {
        if(!isset($this->param[$key]))
          trigger_error('Pagination Class : Invalid parameter key <b>'.$key.'</b>');
        else $this->param[$key] = $value;
      }
    }
  }
  /**
  * @param array|string $object : an array of items, | a valid SQL string with LIMIT
  * @param int $limit : limit of items per page
  * @param int $skip : Number of links per page
  */
  function set($object, $limit=10, $skip=5)
  {
    $this->style = '';
    if(!isset($this->current)){
      $this->current = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    }
    if(!isset($this->link)) $this->link = $_SERVER['REQUEST_URI'];

    $link = parse_url($this->link);

    if(isset($link['query'])){
      $this->link = $link['query'];
      if(preg_match('~&page=~i', $this->link)) {
        $this->link = str_replace('&page='.$this->current, '', $this->link).'&amp;';
      }
      elseif(preg_match('~page=~i', $this->link)) {
        $this->link = str_replace('page='.$this->current, '', $this->link);
      }
      else {
        $this->link .= '&amp;';
      }
    }
    else $this->link = '';
    $this->link = $link['path'].'?'.$this->link;

    if($this->param['seoLinks'] and !strpos($this->link,'?')) $this->link = $link['path'].'?';

    if(is_array($object) and !empty($object)) $this->total = count($object);
    elseif(is_string($object)){
      if(strpos($object,'LIMIT')) trigger_error('Pagination Class : Forbids the Use of the <strong>`LIMIT`</strong> clause in your SQL Syntax.');
      $this->total = mysql_num_rows(mysql_query($object));
    }
    $this->limit = ($limit <= 0) ? $this->total : $limit;
    $this->skip = $skip != 0 ? $skip : 5;
    $this->next = $this->current + 1;
    $this->prev = $this->current - 1;
    $this->pages = @ceil($this->total/$this->limit);
    if($this->current > $this->pages) $this->current = $this->pages;
    if($this->current < 0) $this->current = 1;
    $this->offset = ($this->current*$this->limit) - $this->limit;

    if(is_array($object) and !empty($object)){
      return array_splice($object, $this->offset, $this->limit);
    }
    elseif(is_string($object)){
      return $object.' LIMIT '.$this->offset.', '.$this->limit;
    }
    return $object;
  }
  function skip($skip = 10)
  {
    $this->skip = $skip;
  }
  function render($now_showing=true)
  {
    if($this->pages > 1){
      $rem = ceil($this->current/$this->skip);
      $starter = $rem > 1 ? ($rem * $this->skip)-($this->skip-1) : 1;
      $_prev = $starter - $this->skip;
      $_next = $starter + $this->skip;
      $ender = $_next <= $this->pages ? $_next : $this->pages + 1;
      ##-[ HTML DISPLAY ]---------------#
      // $a = $this->style;
      // $a .= '<div style="display:block;">';
      // $a .= '<div>';
      $a = ($now_showing===true) ? $this->_showing() : '';
      $a .= '<ul class="pagination">';
      $b = '<li><a title="reverse skip" data-page-id="'.$_prev.'" href="'.$this->link.'page='.$_prev.'">&laquo;</a></li>';
      $c = '<li><a title="previous page" data-page-id="'.$this->prev.'" href="'.$this->link.'page='.$this->prev.'">&lt;</a></li>';
      $d = '<li><a title="next page" data-page-id="'.$this->next.'" href="'.$this->link.'page='.$this->next.'">&gt;</a></li>';
      $e = '<li><a title="forward skip"  data-page-id="'.$_next.'"href="'.$this->link.'page='.$_next.'">&raquo;</a></li>';
      $f = '</ul>';
      $html = $a.($_prev>0?$b:'').($this->prev>0?$c:false);
      for($i = $starter; $i < $ender; $i++)
        $html .= '<li class="'.($i==$this->current?'active':'').'"><a data-page-id="'.$i.'" href="'.$this->link.'page='.$i.'" title="page '.$i.'">'.$i.'</a></li>';
      $html .= ($this->next<=$this->pages?$d:false).($_next>$this->pages?'':$e).$f;
      ##-[ END HTML DISPLAY ]---------------#
      return '<div class="pagination_block" style="display:block;clear:both;">'.$html.'</div>';
    }
  }
  function render_alt($now_showing=true)
  {
    if($this->pages > 1){
      ##-[ HTML DISPLAY ]---------------#
      // $a = $this->style;
      $a = '<div class="pagination">';
      $a .= ($now_showing===true) ? $this->_showing() : '';
      $b = '<a class="" data-page-id="'.$this->prev.'" title="previous page" href="'.$this->link.'page='.$this->prev.'">&lt; Prev</a>';
      $c = ' '; // link separator
      $d = '<a class="" data-page-id="'.$this->next.'" title="next page" href="'.$this->link.'page='.$this->next.'">Next &gt;</a>';
      $e = '</div>';
      $html = $a.($this->prev>0?$b:false).(($this->prev>0 and $this->pages>=$this->next)?$c:false).($this->next>$this->pages?false:$d).$e;
      ##-[ END HTML DISPLAY ]---------------#
      return $html;
    }
  }
  function render_alt_2($now_showing=true)
  {
    if($this->pages > 1){
       ##-[ HTML DISPLAY ]---------------#
       // $a = $this->style;
       $a = ($now_showing===true) ? $this->_showing() : '';
       $a .= '<div class="pagination">';
       $b = '<a class="" data-page-id="'.$this->prev.'" title="previous page" href="'.$this->link.'page='.$this->prev.'">&lt; Newer Posts</a>';
       $c = ' '; // link separator
       $d = '<a class="" title="next page" href="'.$this->link.'page='.$this->next.'">Older Posts  &gt;</a>';
       $e = '</div>';
       $html = $a.($this->prev>0?$b:false).(($this->prev>0 and $this->pages>=$this->next)?$c:false).($this->next>$this->pages?false:$d).$e;
       ##-[ END HTML DISPLAY ]---------------#
       return $html;
    }
  }
  function _this($link)
  {
    $this->link = $link;
  }
  function _current($int)
  {
    $this->current = $int;
  }
  function _showing()
  {
    return '<small class="pagination_showing">Page '.$this->current.' of '.$this->pages.' pages. '.$this->total.' total results. </small><br />';
  }
}

