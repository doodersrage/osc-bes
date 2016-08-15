<?php
  if (is_array($payment_modules->modules)) {
  	tep_print($payment_modules);
    if ($confirmation = $payment_modules->confirmation()) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td>
      </tr>
      <tr>
        <td>
        	<table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" colspan="4"><?php echo $confirmation['title']; ?></td>
              </tr>
<?php
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>
              <tr>
                <td class="main"><?php echo $confirmation['fields'][$i]['title']; ?></td>
                <td class="main"><?php echo $confirmation['fields'][$i]['field']; ?></td>
              </tr>
<?php
      }
?>
       		 </table>
        </td>
      </tr>
<?php
    }
  }
?>