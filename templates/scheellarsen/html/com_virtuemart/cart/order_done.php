<?php
defined('_JEXEC') or die('');

/**
*
* Template for the shopping cart
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/


/*if ($this->display_title) {
	echo "<h3>".JText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU')."</h3>";
}
	echo $this->html;*/

$admin = JFactory::getUser('252');
$cart = $this->cart;
if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$config =& JFactory::getConfig();
$fromName = $config->getValue( 'config.sitename' );
$fromMail = $config->getValue( 'config.mailfrom' );
$vars['user'] = array('name' => $fromName, 'email' => $fromMail);
$vars['vendor'] = array('vendor_store_name' => $fromName );

$db = JFactory::getDBO();
$orderid = $cart->order_number;

$query = "SELECT virtuemart_order_id, order_shipment, order_total, order_salesPrice, order_number FROM #__virtuemart_orders WHERE order_number = '".$orderid."'";
$db->setQuery($query);
$order_info = $db->loadObject();

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');
$orderModel=VmModel::getModel('orders');
$order = $orderModel->getOrder($order_info->virtuemart_order_id);

$vars['orderDetails']=$order;
shopFunctionsF::renderMail('invoice', $admin->email, $vars);
shopFunctionsF::renderMail('invoice', $cart->BT['email'], $vars);

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'BT' AND virtuemart_order_id = ".$order_info->virtuemart_order_id;
$db->setQuery($query);
$BT_info = $db->loadObject();

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'ST' AND virtuemart_order_id = ".$order_info->virtuemart_order_id;
$db->setQuery($query);
$ST_info = $db->loadObject();

$query = "SELECT * FROM #__virtuemart_order_items WHERE virtuemart_order_id = ".$order_info->virtuemart_order_id;
$db->setQuery($query);
$items = $db->loadObjectList();

if($BT_info->address_type_name == 1 ){
	$type = "Privat";
} else if($BT_info->address_type_name == 2 ){
	$type = "Erhverv";
} else {
	$type = "Offentlig instans";
}
?>

<div class="template2">
  <div class="thanks_page clearfix">
    <h2 class="c669903">Indkøbskurven</h2>
    <div class="top_info">
      <p><strong>Ordrenummer: <?php echo $orderid;?></strong><br>
        En ordrebekræftelse vil blive sendt til <strong><a href="mailto:<?php echo $email;?>"><?php echo $email;?></a></strong><br>
        Har du spørgsmål, kan du kontakte os på +45 4162 8001</p>
    </div>
    <div class="thanks_info clearfix">
      <div class="w460 fl">
        <p class="bold">Kundeoplysninger:</p>
        <p>
          <label for="">Kundetype:</label>
          <span><?php echo $type;?></p>
		<?php if($BT_info->address_type_name == 2){?>
        <label for="">Firmanavn:</label><span><?php echo $BT_info->company;?></span><br>
        <label for="">CVR-nr.:</label><span><?php echo $BT_info->cvr;?></span><br>
        <?php } else if($BT_info->address_type_name == 3){?>
        <label for="">EAN-nr.:</label><span><?php echo $BT_info->ean;?></span><br>
        <label for="">Myndighed/Institution:</label><span><?php echo $BT_info->authority;?></span><br>
        <label for="">Ordre- el. rekvisitionsnr.:</label><span><?php echo $BT_info->order1;?></span><br>
        <label for="">Personreference:</label><span><?php echo $BT_info->person;?></span><br>
        <?php }?>
        <p>
          <label for="">Fornavn:</label>
          <?php echo $BT_info->first_name;?></p>
        <p>
          <label for="">Efternavn:</label>
          <?php echo $BT_info->last_name;?></p>
        <p>
          <label for="">Vejnavn:</label>
          <?php echo $BT_info->street_name;?></p>
        <p>
          <label for="">Hus/gade nr.:</label>
          <?php echo $BT_info->street_number;?></p>
        <p>
          <label for="">Postnr.:</label>
          <?php echo $BT_info->zip;?></p>
        <p>
          <label for="">Bynavn:</label>
          <?php echo $BT_info->city;?></p>
        <p>
          <label for="">Telefonnummer:</label>
          <?php echo $BT_info->phone_1;?></p>
        <p>
          <label for="">E-mail adresse:</label>
          <?php echo $BT_info->email;?></p>
        <p class="clearfix">
          <label for="" class="fl">Besked:</label>
          <span class="w320 fl"><?php echo $BT_info->message1;?></span> </p>
        <p>
          <label for=""><strong>Betalingsmetode:</strong></label>
          Kortbetaling</p>
        <p>
          <label for=""><strong>Levering:</strong></label>
        <?php if($BT_info->type == 1){?>
        <span>Ved afhentning på Hesselrødvej 26, 2980 Kokkedal</span>
        <?php } else {?>
        <span>Forsendelse</span>
        <?php }?>
          </p>
      </div>
      <div class="w250 fl">
        <p class="bold">Leveringsadresse:</p>
        <p>
          <label for="">Fornavn:</label>
          <?php echo $ST_info->first_name;?></p>
        <p>
          <label for="">Efternavn:</label>
          <?php echo $ST_info->last_name;?></p>
        <p>
          <label for="">Vejnavn:</label>
          <?php echo $ST_info->street_name;?></p>
        <p>
          <label for="">Hus/gade nr.:</label>
          <?php echo $ST_info->street_number;?></p>
        <p>
          <label for="">Postnr.:</label>
          <?php echo $ST_info->zip;?></p>
        <p>
          <label for="">Bynavn:</label>
          <?php echo $ST_info->city;?></p>
        <p>
          <label for="">Telefonnummer:</label>
          <?php echo $ST_info->phone_1;?></p>
        <?php if($BT_info->type == 1){?>
        <p class="red f18">Bemærk! Vi kontakter jer, når varen er klar afhentning</p>
        <?php }?>
      </div>
    </div>
    <div class="clear"></div>
    <table class="list_item_cart">
      <tbody>
        <tr class="title">
          <th>Varebeskrivelse</th>
          <th>Antal</th>
          <th>Pris pr stk.</th>
          <th>Pris i alt</th>
        </tr>
        <tr>
          <td><div class="img_pro"> <img alt="" src="img/img04.jpg"> </div>
            <div class="content_pro">
              <h4>Filippa Grå Terracotta 38 cm</h4>
              <p>Vare-nummer: 30283</p>
              <p>Størrelse: Højde 21 cm-Ø27 cm</p>
              <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
            </div></td>
          <td><p>1</p></td>
          <td><p>479 DKK </p></td>
          <td><p>479 DKK </p></td>
        </tr>
        <tr>
          <td><div class="img_pro"> <img alt="" src="img/img04.jpg"> </div>
            <div class="content_pro">
              <h4>Filippa Grå Terracotta 38 cm</h4>
              <p>Vare-nummer: 30283</p>
              <p>Størrelse: Højde 21 cm-Ø27 cm</p>
              <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
            </div></td>
          <td><p>1</p></td>
          <td><p>479 DKK </p></td>
          <td><p>479 DKK </p></td>
        </tr>
        <tr>
          <td><div class="img_pro"> <img alt="" src="img/img04.jpg"> </div>
            <div class="content_pro">
              <h4>Filippa Grå Terracotta 38 cm</h4>
              <p>Vare-nummer: 30283</p>
              <p>Størrelse: Højde 21 cm-Ø27 cm</p>
              <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
            </div></td>
          <td><p>1</p></td>
          <td><p>479 DKK </p></td>
          <td><p>479 DKK </p></td>
        </tr>
        <tr>
          <td class="cf9f7f3" colspan="4"><table class="sub_order_Summary">
              <tbody>
                <tr>
                  <td colspan="2">Subtotal: </td>
                  <td width="25%" colspan="2"> 1.916 DKK </td>
                </tr>
                <tr>
                  <td colspan="2">Heraf moms: </td>
                  <td colspan="2">383,20 DKK</td>
                </tr>
                <tr>
                  <td colspan="2">FRAGT: </td>
                  <td>150 DKK</td>
                </tr>
                <tr>
                  <td colspan="2"><h4>total:</h4></td>
                  <td colspan="2"><h4>1.955 DKK</h4></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </tbody>
    </table>
    <p class="bb1"><strong>Sådan returnerer du en vare</strong><br>
      Vi ønsker at du er tilfreds hver gang du handler hos os, derfor er vi også 
      opmærksomme på at du lejlighedsvis ønsker at returnere en vare. Klik her for at læse mere om vores returpolitik.</p>
    <p><strong>Har du brug for hjælp?</strong><br>
      Se vores Almindelige Spørgsmål. Her finder du svar på spørgsmål om vores onlineshop.</p>
    <p>Tak for din bestilling.<br>
      Helle Scheel-Larsen<br>
      Hesselrødvej 26, Karlebo<br>
      2980 Kokkedal<br>
      Mobil: 41628001<br>
      Email: info@scheel-larsen.dk</p>
    <div class="goto clearfix"> <a href="index.php" class="btnHome fl hover">Til forside</a> <a href="#" class="btnPrint fl hover ml10">PRINT KVITTERING</a> </div>
  </div>
</div>
