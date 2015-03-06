<!--Copyright (C) Digito.cz, Digito Proprietary License-->
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-pp-std-uk" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (isset($error['error_warning'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-pp-std-uk" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-status" data-toggle="tab"><?php echo $tab_order_status; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="entry_api"><span data-toggle="tooltip" title="<?php echo $help_api; ?>"><?php echo $entry_api; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="bcp_payment_api" value="<?php echo $bcp_payment_api; ?>" placeholder="<?php echo $entry_api; ?>" id="entry_api" class="form-control"/>
                  <?php if ($error_api) { ?>
                  <div class="text-danger"><?php echo $error_api; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="callback-password"><span data-toggle="tooltip" title="<?php echo $help_password; ?>"><?php echo $entry_password; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="bcp_payment_password" value="<?php echo $bcp_payment_password; ?>" placeholder="<?php echo $entry_password; ?>" id="callback-password" class="form-control"/>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="entry-email"><span data-toggle="tooltip" title="<?php echo $help_email; ?>"><?php echo $entry_email; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="bcp_payment_email" value="<?php echo $bcp_payment_email; ?>" placeholder="<?php echo $entry_email; ?>" id="entry-email" class="form-control"/>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="payout-currency"><span data-toggle="tooltip" title="<?php echo $help_currency; ?>"><?php echo $entry_currency; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="bcp_payment_currency" value="<?php echo $bcp_payment_currency; ?>" placeholder="<?php echo $entry_currency; ?>" id="payout-currency" class="form-control"/>
                  <?php if ($error_currency) { ?>
                  <div class="text-danger"><?php echo $error_currency; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_buttons; ?></label>
                <div class="col-sm-10">
                <input type="radio" name="bcp_payment_buttons" value="1" <?php if($bcp_payment_buttons == 1){?>checked="checked"<?php } ?>" /><img src="view/image/payment/bcp_buttons/01_s.png" alt="i01">
                <input type="radio" name="bcp_payment_buttons" value="2" <?php if($bcp_payment_buttons == 2){?>checked="checked"<?php } ?>" /><img src="view/image/payment/bcp_buttons/02_s.png" alt="i02">
                <input type="radio" name="bcp_payment_buttons" value="3" <?php if($bcp_payment_buttons == 3){?>checked="checked"<?php } ?>" /><img src="view/image/payment/bcp_buttons/03_s.png" alt="i03">
                <input type="radio" name="bcp_payment_buttons" value="4" <?php if($bcp_payment_buttons == 4){?>checked="checked"<?php } ?>" /><?php echo $entry_buttons_text; ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_geo_zone_id" id="input-geo-zone" class="form-control">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                    <?php if ($geo_zone['geo_zone_id'] == $bcp_payment_geo_zone_id) { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="bcp_payment_sort_order" value="<?php echo $bcp_payment_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_status" id="input-status" class="form-control">
                    <?php if ($bcp_payment_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-status">
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_confirmed_status; ?>"><?php echo $entry_confirmed_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_confirmed_status_id" class="form-control">
                    <?php if($bcp_payment_confirmed_status_id == NULL) $bcp_payment_confirmed_status_id = 15; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_confirmed_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_pending_status; ?>"><?php echo $entry_pending_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_pending_status_id" class="form-control">
                  <?php if($bcp_payment_pending_status_id == NULL) $bcp_payment_pending_status_id = 1; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_pending_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_timeout_status; ?>"><?php echo $entry_timeout_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_timeout_status_id" class="form-control">
                  <?php if($bcp_payment_timeout_status_id == NULL) $bcp_payment_timeout_status_id = 14; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_timeout_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_received_status; ?>"><?php echo $entry_received_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_received_status_id" class="form-control">
                  <?php if($bcp_payment_received_status_id == NULL) $bcp_payment_received_status_id = 1; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_received_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_invalid_status; ?>"><?php echo $entry_invalid_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_invalid_status_id" class="form-control">
                  <?php if($bcp_payment_invalid_status_id == NULL) $bcp_payment_invalid_status_id = 16; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_invalid_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_refunded_status; ?>"><?php echo $entry_refunded_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_refunded_status_id" class="form-control">
                   <?php if($bcp_payment_refunded_status_id == NULL) $bcp_payment_refunded_status_id = 11; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_refunded_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_pat_status; ?>"><?php echo $entry_pat_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_pat_status_id" class="form-control">
                   <?php if($bcp_payment_pat_status_id == NULL) $bcp_payment_pat_status_id = 16; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_pat_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_insufficient_amount_status; ?>"><?php echo $entry_insufficient_amount_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bcp_payment_insufficient_amount_status_id" class="form-control">
                  <?php if($bcp_payment_insufficient_amount_status_id == NULL) $bcp_payment_insufficient_amount_status_id = 16; ?>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bcp_payment_insufficient_amount_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>