<?
//$id_stud=3565;
session_start();
$id_stud=$_SESSION['student'];
$stud=$id_stud;
//echo $stud."+++";
//require('../admin/templ.php');
require_once("../admin/stran.php");
require_once("../admin/fu.php");
$day_a=array("семестр","понеділок","вівторок","середа","четвер","пятниця","субота");
$block_a=array("","загальні вібіркові дисципліни","професійні вибіркові дисципліни","середа","четвер","пятниця","субота");
//$parse->get_tpl('indx.html');
obd();
//echo "<pre>";
//	print_r($_POST);
//echo "</pre>";
///////////////////
$ip=getRealIP();
if (isset($_POST['i']))
{
$i=explode("|",$_POST['i']);
$list=(int)$i[0];
$summary=(int)$i[1];
$flag=(int)$i[2];
if($flag==1) $sql="update dvv_list set flag=0,sem=0, stud_id=$stud where ((`id` = '$list'))";
else if($flag==0) $sql="update dvv_list set flag=1,sem=0, stud_id=$stud where ((`id` = '$list'))";
else if($flag==2) $sql="INSERT INTO `dvv_list` (`stud_id`, `summary_id`, `dati`, `ip`, `sem`, `flag`)
VALUES ('$stud', '$summary', now(), '$ip', '0', '1');";
//echo $sql."ok!!!";
if (isset($sql)) $result=mysql_query($sql) or die("Invalid query1:" . mysql_error());

}
else {

$arr=$_POST;

foreach ($arr as $key => $val)
{
//опредляем r
if(substr($key,0,1)=='r') 
{$i=explode("|",$val);
$sem=(int)$i[0];
$displ=(int)$i[1];
$summary=(int)$i[2];
$block=(int)$i[3];


$sql="select dvv_list.id, dvv_list.flag from dvv_list 
inner join dvv_summary on dvv_summary.type_id=$block
where summary_id=$summary and stud_id=$stud
";
$result=mysql_query($sql) or die("Invalid query1:" . mysql_error());
$res=mysql_fetch_row($result);
//echo $sql;
{//удаляет запись если не выбран предмет
 //echo "not".$summary;
if(mysql_num_rows($result)!=0) {
// снято // 06-12-16	
//if ($res[1]==1) $sql0="update dvv_list set stud_id=$stud, summary_id=$summary,dati=NOW(), sem=0, ip='$ip', flag=0 where id=$res[0]"; else

if ($res[1]==0||$res[1]==1) $sql0="update dvv_list set stud_id=$stud, summary_id=$summary,dati=NOW(), sem=$sem, ip='$ip', flag=1 where id=$res[0]"; }
else $sql0="INSERT dvv_list (`stud_id`, `summary_id`, `sem`, `dati`, `ip`,`flag`) VALUES('$stud', '$summary', $sem, NOW(), '$ip',1);";
//echo $sql0."<BR>";
if (isset($sql0)) $result0=mysql_query($sql0) or die("Invalid query: " . mysql_error());
}
//else
{
	//3 строки 06.12.16
if(mysql_num_rows($result)!=0) $sql="update dvv_list set stud_id=$stud, summary_id=$summary,dati=NOW(), sem=$sem, ip='$ip' where id=$res[0]";
//else $sql="INSERT dvv_list (`stud_id`, `summary_id`, `sem`, `dati`, `ip` ,`flag` ) VALUES('$stud', '$summary', '$sem', NOW(), '$ip', 1);";
$result=mysql_query($sql) or die("Invalid query: " . mysql_error());
//echo $sql;
}
}
//echo "boom";
//echo $key;
//echo $val."+"."<BR>";
}
//echo strpos('r|omala','r')."=<BR>";
}


///////////////////
$nom_i=0;
$rozklad="";
echo '<form method="POST" id="formx1" action="javascript:void(null);" onsubmit="call()">';
for ($block=1;$block<=2;$block++)
{
$bask=0;
$basket="";
/////////////////////////////////////////
//корзина
//$sql_basket="select kred from dvv_kred where spec_id=$row[6] and type_id=$block and semestr=$row[2]";	
$sql_basket="select kred, semestr from dvv_kred
inner join dvv_spec on dvv_spec.id=dvv_kred.spec_id
inner join stud on stud.spec=dvv_spec.id
where type_id=$block and stud.id=$id_stud";	
//inner join dvv_spec on dvv_spec.id=dvv_kred.spec_id
//inner join dvv_stud on dvv_stud.spec=dvv_spec.id
//echo $sql_basket;
$result_basket=mysql_query($sql_basket) or die("Invalid query: " . mysql_error());
while ($row_basket=mysql_fetch_row($result_basket))
{$basket[$bask][0]=$block;//block
$basket[$bask][1]=$row_basket[1];//semestr
$basket[$bask][2]=$row_basket[0];//kred
//ищем количество выбранных
//узнать сколько человек уже записалось на єтот курс
$sql_choise="select kred from dvv_summary
inner join dvv_list on dvv_summary.id=dvv_list.summary_id and dvv_list.sem=$row_basket[1] and  dvv_list.stud_id=$stud
where dvv_summary.type_id=$block ";

//$sql_choise="select kred from dvv_summary
//inner join dvv_list on dvv_summary.id=dvv_list.summary_id and dvv_list.sem=$row_basket[1] and  dvv_list.stud_id=$stud
//where dvv_summary.type_id=$block ";
//echo $sql_choise."<BR>";
$result_choise=mysql_query($sql_choise) or die("Invalid query: " . mysql_error());
while ($row_choise=mysql_fetch_row($result_choise)) 
{$basket[$bask][3]+=$row_choise[0]; 
}
$bask++;
}
///// ? как сделать семестр неактивнім
//print_r ($basket); echo "<BR>";

	$kred=0;
//echo "out";
//$block=2;
//inner join dvv_kred on dvv_spec.id=dvv_kred.spec_id
//inner join dvv_type on dvv_kred.type_id=dvv_type.id 
//общий запрос

$tab="<table>";
$sql="select dvv_summary.displ_id, dvv_summary.id, dvv_summary.sem, dvv_summary.inn, dvv_displ.displn, dvv_prpd.prpd, 
dvv_summary.spec_id, dvv_summary.kred, dvv_summary.prpd_id, dvv_kolgrup.kol from dvv_displ
inner join dvv_summary on dvv_summary.displ_id=dvv_displ.id
inner join dvv_kolgrup on dvv_displ.kolgrup=dvv_kolgrup.id
inner join dvv_spec on dvv_spec.id=dvv_summary.spec_id
inner join stud on stud.spec=dvv_spec.id
inner join dvv_prpd on dvv_prpd.id=dvv_summary.prpd_id
where stud.id=$id_stud and dvv_summary.type_id=$block group by dvv_displ.displn" ;
//echo $sql;
$result=mysql_query($sql) or die("Invalid query: " . mysql_error());
while ($row=mysql_fetch_row($result))
{
//семестры
$sql_sem="select sem from dvv_summary where displ_id=$row[0] and spec_id=$row[6]";
$result_sem=mysql_query($sql_sem) or die("Invalid query: " . mysql_error());
$sem="";
$i=0;
while ($row_sem=mysql_fetch_row($result_sem))
{
$sem[$i++]=$row_sem[0];
////////////////////////////////
///!!
//$sql_day_par="select day, nompar from dvv_rozkl_prpd where id_prpd=$row[8] and id_displ=$row[0] and id_spec=$row[6] and sem=$row_sem[0]";
$sql_day_par="select day, nompar from dvv_summary where prpd_id=$row[8] and displ_id=$row[0] and spec_id=$row[6] and sem=$row_sem[0]";
//if ($row[0]==29) echo $sql_day_par;
$result_day_par=mysql_query($sql_day_par) or die("Invalid query: " . mysql_error());
while ($row_day_par=mysql_fetch_row($result_day_par))
{
$days[$i-1]=$row_day_par[0];
$pars[$i-1]=$row_day_par[1];
//$nom_daypar_a0
}
//echo $sql_day_par;
}
//print_r ($sem);
//print_r ($days);
//echo "<BR>";
$tab_sem='<table><tr>';
//!!
/*$sql1="select dvv_list.id, dvv_list.sem, dvv_list.flag, dvv_rozkl_prpd.day, dvv_rozkl_prpd.nompar from dvv_list 
inner join dvv_summary on dvv_summary.type_id=$block
inner join dvv_rozkl_prpd on  dvv_rozkl_prpd.id_prpd=dvv_summary.prpd_id and dvv_rozkl_prpd.id_spec =dvv_summary.spec_id and dvv_rozkl_prpd.id_displ =dvv_summary.displ_id and dvv_rozkl_prpd.sem =dvv_summary.sem
where summary_id=$row[1] and stud_id=$id_stud
";
*/
$sql1="select dvv_list.id, dvv_list.sem, dvv_list.flag, dvv_summary.day, dvv_summary.nompar from dvv_list 
inner join dvv_summary on dvv_summary.type_id=$block
where summary_id=$row[1] and stud_id=$id_stud";
//and dvv_rozkl_prpd.id_prpd=$row[8] and dvv_rozkl_prpd.id_displ=$row[0] and 
$result1=mysql_query($sql1) or die("Invalid query1:" . mysql_error());
$res1=mysql_fetch_row($result1);
$fon='';
if (mysql_num_rows($result1)!=0) 
	if ($res1[2]==1) 
	{$chk='checked="checked"'; $fon='bgcolor="#99EE99"'; $nom_daypar_a[$nom_i++]=$res1[3].$res1[4];}
	else  $chk=""; 
else $chk="";


for($i=1;$i<=8;$i++)
{
$fon0='bgcolor="#FFDDDD"';
// !!! если уже выбран день недели и пара....
$day=$days[array_search($i, $sem)];
$par=$pars[array_search($i, $sem)];
//!
/*$sql_unical="select dvv_list.id, dvv_rozkl_prpd.id from dvv_list
inner join dvv_summary on dvv_list.summary_id=dvv_summary.id and dvv_summary.type_id=$block
inner join dvv_rozkl_prpd on  dvv_rozkl_prpd.id_prpd=dvv_summary.prpd_id and dvv_rozkl_prpd.id_spec =dvv_summary.spec_id and dvv_rozkl_prpd.id_displ =dvv_summary.displ_id and dvv_rozkl_prpd.sem =dvv_summary.sem
where stud_id=$id_stud and dvv_list.flag='1' and dvv_rozkl_prpd.day=$day and dvv_rozkl_prpd.nompar=$par and dvv_list.sem=$i
";*/
$sql_unical="select dvv_list.id, dvv_summary.id from dvv_list
inner join dvv_summary on dvv_list.summary_id=dvv_summary.id and dvv_summary.type_id=$block
where stud_id=$id_stud and dvv_list.flag='1' and dvv_summary.day=$day and dvv_summary.nompar=$par and dvv_list.sem=$i
";
// and dvv_rozkl_prpd.id_prpd=$row[8]  and dvv_rozkl_prpd.id_displ=$row[0]  and dvv_rozkl_prpd.id_spec=$row[6]
if (isset($day))
{$result_unical=mysql_query($sql_unical) or die("Invalid query1:" . mysql_error());
$res_unical=mysql_fetch_row($result_unical);
//if ($res_unical[0]==299) echo $sql_unical;
//if (mysql_num_rows($result1)==0) $dis=''; else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//if ((mysql_num_rows($result_unical)==0) ||(in_array($i,$sem))) $dis=''; else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//if ((mysql_num_rows($result_unical)==0)) $dis=''; else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
if ((mysql_num_rows($result_unical)==0)) {
	if(in_array($i,$sem)) 
	{	$dis='';
//if ($row[0]==29) echo $res_unical[1]."<BR>";
//if ($res_unical[1]<>0) $a=1; else $fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';
	}
	else {
			$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}} 
			else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//
} 		else {
//	if ($res_unical[0]<>0) $a=1; else $fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';
	$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';	}

//	if ($flag==$i){ $fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//блокируем вібор если уже есть лимит кредитов
//$basket[$bask][0]=$block;//block
//$basket[$bask][1]=$row_basket[1];//semestr
//$basket[$bask][2]=$row_basket[0];//kred
//$basket[$bask][3]=$row_basket[0];//kred
for ($ind=0;$ind<sizeof($basket);$ind++)
if (($basket[$ind][0]==$block)&&($basket[$ind][1]==$i)&&($basket[$ind][3]>=$basket[$ind][2]))
{$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//if(in_array($i,$sem)) $dis=''; else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//if(in_array($i,$sem)) $dis=''; else {$fon0='bgcolor="#aaaaaa"';$dis='disabled="disabled"';}
//||in_array($days[array_search($i, $sem)].$pars[array_search($i, $sem)],$nom_daypar_a)
$dis_rad='';
if($res1[1]==$i)
{
$kred+=$row[7];$chk0='checked="checked"'; $fon0='bgcolor="#99EE99"'; $dis_rad=' onclick="cal_hard('.$calhard.')"';
$day=$days[array_search($i, $sem)];
$rozklad[$i][$day]=$row[4];
}else $chk0='';
//if(in_array($i,$sem))$day=$day_a[$days[0000]];else $day='';
if(in_array($i,$sem))
{$day=$days[array_search($i, $sem)];
$day_par='<BR>'.$day_a[$day];//.'пара:'.$pars[array_search($i, $sem)];
$par='; пара:'.$pars[array_search($i, $sem)];
}
else {$day_par='';$par='';}
$calhard="'".$res1[0]."|".$row[1]."|".$res1[2]."'";
////////////
//узнаем скольк человек вібрало дисциплину....
$sql_kol="select count(*) from dvv_list 
inner join dvv_summary on dvv_list.summary_id=dvv_summary.id 
inner join dvv_displ on dvv_displ.id=dvv_summary.displ_id
where dvv_list.flag=1 and dvv_list.sem=$i and dvv_summary.type_id=$block and dvv_displ.id=$row[0]";
//inner join dvv_summary on dvv_summary.type_id=$block  and  dvv_summary.id=dvv_list.summary_id
//where flag=1 and dvv_list.summary_id=$row[0] and dvv_list.sem=$i"

//$sql_kol="select count(*) from dvv_list 
//inner join dvv_summary on dvv_summary.type_id=$block  and  dvv_summary.id=dvv_list.summary_id
//where flag=1 and dvv_list.summary_id=$row[1] and dvv_list.sem=$i";

//echo $sql_kol."<BR>";
//and dvv_rozkl_prpd.id_prpd=$row[8] and dvv_rozkl_prpd.id_displ=$row[0] and 
$result_kol=mysql_query($sql_kol) or die("Invalid query1:" . mysql_error());
$res_kol=mysql_fetch_row($result_kol);

if ($day_par!='')
{ $obrano='<BR>обрали '.$res_kol[0].' ст./'.$row[9].''; 
$kolvo=$res_kol[0];
}else $obrano='';
$tab_sem.='<td '.$fon0.'><input type="radio" name="r|'.$row[0].'" value="'.$i.'|'.$row[0].'|'.$row[1].'|'.$block.'" '.$dis.' '.$chk0.'><label '.$dis_rad.'>'.$i.$par.$day_par.$obrano.'</label></td>';
}
$tab_sem.='</tr></table>';
if (!isset($res1[0])) $res1[0]=0;
if (!isset($res1[2])) $res1[2]=2;
//узнаем скольк человек вібрало дисциплину....
//$sql_kol="select count(*) from dvv_list 
//inner join dvv_summary on dvv_summary.type_id=$block  and  dvv_summary.id=dvv_list.summary_id
//where flag=1 and dvv_list.summary_id=$row[1]";
//echo $sql_kol."<BR>";
//and dvv_rozkl_prpd.id_prpd=$row[8] and dvv_rozkl_prpd.id_displ=$row[0] and 
//$result_kol=mysql_query($sql_kol) or die("Invalid query1:" . mysql_error());
//$res_kol=mysql_fetch_row($result_kol);
// (mysql_num_rows($result1)!=0) 

//$tab.='<tr '.$fon.' ><td>'.'<label onclick="call(123)"><input type="checkbox" name="c|'.$row[1].'" value="'.$row[1].'" '.$chk.'>'.$row[4].$row[3].'</label></td><td><label onclick="call(123)"><input type="hidden" name = "f|'.$row[1].'" value="'.$row[1].'">'.$row[5].'</label></td><td><label onclick="call(123)">'.$tab_sem.'</label></td></tr>';
//$tab.='<tr '.$fon.' ><td>'.'<label onclick="call(123)"><input type="checkbox" name="c|'.$row[1].'" value="'.$row[1].'" '.$chk.'>'.$row[4].$row[3].'</label></td><td><label onclick="cal_hard('.$calhard.')">'.$row[5].'</label></td><td><label onclick="call(123)">'.$tab_sem.'</label></td></tr>';
$tab.='<tr '.$fon.' ><td>'.'<label onclick="cal_hard('.$calhard.')"><B>'.$row[4].'</B><BR>>>>обрали '.$kolvo.' студ. ('.$row[9].') </label></td><td><label onclick="cal_hard('.$calhard.')">'.$row[5].'</label></td><td><label onclick="call(123)">'.$tab_sem.'</label></td></tr>';

}
$tab.='</table>';
$mess='<table><tr bgcolor="#FFFFAA"><TD>';
//for ($ind0=0;$ind0<sizeof($basket[0]);$ind0++)
for ($ind=0;$ind<sizeof($basket);$ind++)
{if (!$basket[$ind][3]) $me="0"; else $me=$basket[$ind][3];
$mess.="для семестру ".$basket[$ind][1]." обрано: <B>".$me."</B> із запланованих <B>".$basket[$ind][2]."</B><BR>";
}
$mess.='</TD></tr></table>';
//
//$tday.="<table><tr>";
//for($dd=1;$dd<7;$dd++) $tday.="<TD>".$day_a[$dd]."</td>";
//$tday.="</tr></table>";
mb_internal_encoding('UTF-8');
echo "<table><TR><TD><B><B>". mb_strtoupper($block_a[$block])."</B></B>";
echo $mess."</TD><TD>".$tday."<TD><TR></table>";
echo $tab;//.'<body bgcolor="#c0c0c0"><p>з блоку обрано:'.$kred." крдт.</p><body>";

/*echo '
<div id="summ1" style="position: fixed; top: 70; right: 0; 
z-index:500;"/><table width="70" border="1">
  <tr bgcolor="#A9E9CE">
    <td>&nbsp;</td>
  </tr>
</table></div>';
*/
//print_r ($rozklad);
echo "<HR>";
}
echo '</form>';
if (!empty($rozklad))
{
$rozkl='<table  border="1"><TR>';
for ($ind0=0;$ind0<6;$ind0++)
$rozkl.='<td align="center">'.$day_a[$ind0].'</td>';
$rozkl.='</TR>';

for ($ind=1;$ind<4;$ind++)
if ($rozklad[$ind]!=Array()){$rozkl."<TR>";
for ($ind0=0;$ind0<6;$ind0++)
{
if ($rozklad[$ind]!=Array()) if ($ind0==0) $rozkl.='<TD align="center">'.$ind.'</TD>'; else $rozkl.='<TD>'.$rozklad[$ind][$ind0].'</td>';
}
$rozkl.='</TR>';
}
$rozkl."</Table>";
echo $rozkl;
}
?>