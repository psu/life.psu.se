<?php 
error_reporting(E_ERROR);
$time = microtime();

/* life.php */

//error_reporting(E_ALL);
$state = $new = $old = array();
$cols = 5;

//--------------------------------------------------------------------------
if (!isset($_GET['widthxheight']) || $_GET['widthxheight'] == '') {
   $_GET['widthxheight'] = '5x5';
}
list($width, $height) = explode('x', strtolower($_GET['widthxheight']));
$width+=2;
$height+=2;
$size = $width * $height;
//--------------------------------------------------------------------------
// fill it up!
for ($i=1;$i<=$size;$i++) {
   set_cell($state, 0);
}
if (!isset($_GET['start_values'])) {
   $_GET['start_values'] = '9,41,40,38,37,31,30,27,20,19,17,16,13,10,';
}
foreach (explode(',', $_GET['start_values']) as $v) {
   set_cell($state, trim($v), 1); 
}
//--------------------------------------------------------------------------
if (!isset($_GET['num_gen']) || $_GET['num_gen'] == '') {
   $_GET['num_gen'] = 2;
}
$num_gen = $_GET['num_gen'];
//--------------------------------------------------------------------------
if (!isset($_GET['rule']) || $_GET['rule'] == '') {
   $_GET['rule'] = "3/3";
}
$rule = $_GET['rule'];
//--------------------------------------------------------------------------
if (!isset($_GET['cellsize']) || $_GET['cellsize'] == '') {
   $_GET['cellsize'] = "10";
}
$cellsize = $_GET['cellsize'];
//--------------------------------------------------------------------------

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
   <title>Conway's Game of Life - Generation Viewer</title>
   <style type="text/css">

BODY {
   background: #FFF;
   text-align: center;
   font-family: Georgia;
   font-size: 1em;
}
   
.life_table {
   border: 1px solid #ccc;
   font-size: 0px;
   line-height: 0px;
   margin-bottom: 10px;
   margin-right: 10px;
}

.life_table TD {
   border-bottom: 1px solid #ccc;
   border-right: 1px solid #ccc;

}   

.life_cell {
   width: <?=$cellsize?>px;
   height: <?=$cellsize?>px;
   border: 0;
}

.hline {
   border-bottom: 0px double #ccc;
   margin: 15px 50px;
}   
   
.form_field {
   width: 300px;
   font-family: Georgia;
   font-size: .91em;   
   margin-left: 30px;
   padding: 4px 6px 4px 6px;
   border: 1px solid #f0f0f0;
   background: #f9f9f9;
} 

.form_button {
   font-family: Georgia;
   font-size: 1.1em;
   margin-top: 10px;
} 

.form_table {
   margin-bottom: 15px;
}

.life_header {
   font-size: 1.9em;
   margin-bottom: 1em;
   margin-top: 1em;
   padding: 10px 30px 10px 30px;
   background: #f9f9f9;
}

.life_header .sub {
   font-size: .6em;
   padding: 10px 30px 10px 30px;
   font-variant: normal;
}
   
#gen_number {
   position: relative;
   top: .9em;
   left: 2px;
   z-index: 99;
   margin-top: -13pt;
   font-size: 12pt;
   color: #666;
   font-weight: bold;
}   
   
   </style>
   <script type="text/javascript">
  
   function toggle_cell(a) {
      var obj = document.getElementById('formstart');
      var arr = obj.value.split(',');
      if (obj.value.indexOf(a) == -1) { // not found
         document.getElementById('cellimg'+a).src = 'rpx.gif';
         arr.push(a);
         arr.sort();
         arr.reverse();
      } else { // found
         document.getElementById('cellimg'+a).src = 'px.gif';
         var tmp = arr.join('|')+'|';
         var needle = a+'|';
         var cut_start = tmp.indexOf(needle);
         var cut_end = cut_start + needle.length;
         var tmp2 = tmp.substr(0, cut_start) + tmp.substr(cut_end);
         arr = tmp2.split('|');
         arr.sort();
         arr.shift();
         arr.reverse();
      }
      obj.value = arr.join(',');
   }
   
   function random_cell(w,h) {
      var obj = document.getElementById('formstart');
      var tmp='';
      var size = w * h;
      for (i=1; i<=size; i++) {
         if (  i % w != 1 &&
               i % w != 0 &&
               i > w &&
               i < (size-w)  ) {
            document.getElementById('cellimg'+i).src = 'px.gif';
            var r = Math.floor(Math.random()*2);
            if (r == 1) {
               tmp += i+',';
               document.getElementById('cellimg'+i).src = 'rpx.gif';
            }
         }
      }
      obj.value = tmp;
   }

   function clear_cell(w,h) {
      for (i=1; i<=(w*h); i++) {
         if (  i % w != 1 &&
               i % w != 0 &&
               i > w &&
               i < ((w*h)-w)  ) {      
            document.getElementById('cellimg'+i).src = 'px.gif';
         }
      }
      document.getElementById('formstart').value = '';
   }
   
   </script>
</head>
<body marginleft="0" margintop="0">
<div align="center">

<div class="life_header">
   Conway's Game of Life - Generation Viewer
   <div class="sub">Made by <a href="mailto:pontus@psu.se">Pontus</a>, version 0.2</div>
</div>

<form action="" method="get">
   <table border="0">
      <tr>
         <td align="left" valign="top">

            <table border="0" class="form_table">
               <tr>
                  <td align="right">Size (WxH)</td>
                  <td><input type="text" name="widthxheight" value="<?=$_GET['widthxheight']?>" class="form_field" tabindex="1"></td>
               </tr>
               <tr>
                  <td align="right">Rule (N/N)</td>
                  <td><input type="text" name="rule" value="<?=$rule?>" class="form_field" tabindex="2"></td>
               </tr>
               <tr>
                  <td align="right">Startvalues (A,B,...)</td>
                  <td><input type="text" name="start_values" value="<?=$_GET['start_values']?>" class="form_field" id="formstart" tabindex="3"></td>
               </tr>
               <tr>
                  <td align="right">Limit #gen</td>
                  <td><input type="text" name="num_gen" value="<?=$num_gen?>" class="form_field" tabindex="4"></td>
               </tr>
               <tr>
                  <td colspan="2" align="center">
                     <input type="submit" value="   Go   " class="form_button" tabindex="5">
                     <input type="hidden" name="cellsize" value="<?=$_GET['cellsize']?>">
                  </td>
               </tr>
            </table>
            
         </td>
         <td align="center" valign="top" style="padding-left:10px;padding-top: 4px;">

            <? print_html($state, true); ?>
            <a href="#" onclick="random_cell(<?=($width.','.$height)?>);">Random!</a> &nbsp;  
            <a href="#" onclick="clear_cell(<?=($width.','.$height)?>);">Clear</a>
            
         </td>
      </tr>
   </table>
</form>

<div class="hline"></div>

<?php

print_html($state);
$old = $state;

for ($g=1; $g<=$num_gen-1; $g++) {

   $new = array();
   for ($i=1; $i<=$size; $i++) {
	 if ( check_neighbours( count_neighbours($state, $i), get_cell($state, $i) ) ) { // rule, get_cell== 1=alive, 0=dead
		set_cell($new, $i, 1);
	 } else {
		set_cell($new, $i, 0);
	 }
   }

   clear_buffer($new);
   print_html($new);

   if ($new == $state || $new == $old || !array_search(1, $new)) {
      break;
   } else {
      $old = $state;
      $state = $new;
   }

}

// foot
echo '
         </tr>
      </table>
   </div>
   <div class="hline"></div>
   tic-toc: '.((microtime() - $time)*1000).' ms &nbsp; &nbsp; <a href="">Link me</a> &nbsp; &nbsp; &copy; 2011 <a href="mailto: pontus@psu.se">Pontus Sundén</a>
';


//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

function get_cell(&$s, $a) {
   global $width, $size;
   if (  $a > 0 &&
         $a < $size  ) {
      $r = (integer)$s[$a];
   } else {
      $r = 0;
   }
   return $r;
}//------------------------------------------------------------------

function set_cell(&$s, $a, $v) {
   global $width, $size;
   if (  $a > 0 &&
         $a < $size  ) {
      $s[$a] = $v;
   }
}//------------------------------------------------------------------

// these two get and set doesn't need clear_buffer, they do the check them selfs...
function __get_cell(&$s, $a) {
   global $width, $size;
   if (  $a % $width != 1 &&
         $a % $width != 0 &&
         $a > $width &&
         $a < ($size-$width)  ) {
      $r = (integer)$s[$a];
   } else {
      $r = 0;
   }
   return $r;
}//------------------------------------------------------------------
function __set_cell(&$s, $a, $v) { 
   global $width, $size;
   if (  $a % $width != 1 &&
         $a % $width != 0 &&
         $a > $width &&
         $a < ($size-$width)  ) {
      $s[$a] = $v;
   }
}//------------------------------------------------------------------

function count_neighbours(&$s, $a) {
   global $width;
   $n = 
      get_cell($s, $a-$width-1) +
      get_cell($s, $a-$width)   +
      get_cell($s, $a-$width+1) +
      get_cell($s, $a-1) +
      get_cell($s, $a+1) +
      get_cell($s, $a+$width-1) +
      get_cell($s, $a+$width)   +
      get_cell($s, $a+$width+1);
   return $n;
}//------------------------------------------------------------------

function clear_buffer(&$s) {
   global $width, $size;
   // clear the buffer area
   for ($i=1; $i<=$width; $i++) {
      $s[$i] = 0;
      $s[$size-$width+$i] = 0;
   }
   for ($i=1; $i<=$size; $i+=$width) {
      $s[$i] = 0;
      $s[$width-1+$i] = 0;
   }
}//------------------------------------------------------------------

function print_html(&$s, $link=false) {
   global $width, $height, $size, $cols;
   static $count=1;
   
   if (!$link) {
      if ($count == 1) {
         echo '<table border="0" class="large_table"><tr>';
      }
      echo '<td><div id="gen_number">'.$count.'</div>';
   }
   
   echo '<table border="0" cellpadding="0" cellspacing="0" class="life_table"><tr>';
     
   for ($i=1; $i<=$size; $i++) {
   
      $href1=$href2='';
      if (  $i % $width == 1 ||
            $i % $width == 0 ||
            $i <= $width ||
            $i >= ($size-$width)  ) {
         $file = 'gpx.gif';
      } else {
         if (get_cell($s, $i) == 1) {
            $file = (!$link)? 'bpx.gif': 'rpx.gif';
         } else {
            $file = 'px.gif';
         }
         if ($link) {
            $href1 = '<a href="#" onclick="toggle_cell('.$i.')" onfocus="this.blur();">';
            $href2 = '</a>';
         }
      }
   
   
      echo '<td>'.$href1.'<img src="./'.$file.'" class="life_cell" id="cellimg'.$i.'">'.$href2.'</td>';
      if ($i % $width == 0) {
         echo '</tr><tr>';
      }
   }

   echo '</tr></table>';

   if (!$link) {
      echo '</td>';
      $count++;
      if (($count-1) % $cols == 0) {
         echo '</tr><tr>';
      }
   }
   
}//------------------------------------------------------------------

function check_neighbours($neighbours, $deadoralive) { //1=alive
   global $rule;
   $neighbours = (string)$neighbours;
   list($rule_alive, $rule_dead) = split('/', $rule);
   $use_rule = ($deadoralive == 1)? $rule_alive: $rule_dead;
   return (strpos($use_rule, $neighbours)===FALSE)? 0: 1;
}//------------------------------------------------------------------


?>

</body>
</html>