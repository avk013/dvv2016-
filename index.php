<?
echo "<html>";
echo '<script type="text/javascript" src="admin/jquery-1.11.1.min.js"></script>';
echo '<script type="text/javascript" language="javascript">';
echo "function call(el) {
 	  var msg   = $('#formx1').serialize();
        $.ajax({
          type: 'POST',
          url: 'res.php',
		  data: 'i='+el,
          data: msg,
          success: function(data) {
            $('#results').html(data);
          },
          error:  function(xhr, str){
	    alert('Возникла ошибка: ' + xhr.responseCode);
          }}); 
    }
</script> ";
echo '<script type="text/javascript" language="javascript">';
echo "function cal_hard(el) {
   $.ajax({
   type: 'POST',
   url: 'res.php',
    data: 'i='+el,
   success: function(data){
         	 $('#results').html(data);
          },
		error:  function(xhr, str){
	    alert('Возникла ошибка: ' + xhr.responseCode);
   }});
  }
  </script>";
////////////////////
/////////////
$id_stud=3565;
//echo '<form method="POST" id="formx" action="javascript:void(null);" onsubmit="call()">';
//echo '<input value="Send" type="submit">';
//echo '</form>';
echo '<script type="text/javascript"> call() </script>';
///
////
echo "студент=".$id_stud."<BR>";
//echo "1234";
echo '<div id="results">вывод</div>';
/*echo '
<div id="summ" style="position: fixed; top: 70; right: 0; 
z-index:500;"/><table width="70" border="1">
  <tr bgcolor="#A9E9CE">
    <td>&nbsp;</td>
  </tr>
</table></div>';*/
echo "</html>";
?>