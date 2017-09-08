<?php
/**
 * Copyright (c) 2008 PayNow (Pty) Ltd
 * You (being anyone who is not PayNow (Pty) Ltd) may download and use this plugin / code in your own website in conjunction with a registered and active PayNow account. If your PayNow account is terminated for any reason, you may not use this plugin / code or part thereof.
 * Except as expressly indicated in this licence, you may not use, copy, modify or distribute this plugin / code or part thereof in any way.
 */

class ModelExtensionPaymentPayNow extends Model {
	public function getMethod( $address, $total ) {
		$this->load->language( 'extension/payment/paynow' );

		$query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get( 'payment_paynow_geo_zone_id' ) . "' AND country_id = '" . (int)$address['country_id'] . "' AND ( zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0' )" );

		if ( $this->config->get( 'payment_paynow_total' ) > $total ) {
			$status = false;
		} elseif ( !$this->config->get( 'payment_paynow_geo_zone_id' ) ) {
			$status = true;
		} elseif ( $query->num_rows ) {
			$status = true;
		} else {
			$status = false;
		}

		$currencies = array( 'ZAR' );

		if ( !in_array( strtoupper( $this->session->data['currency'] ), $currencies ) ) {
			$status = false;
		}

		$method_data = array();

		if ( $status ) {
			$method_data = array(
				'code'       => 'paynow',
				'title'      => $this->language->get( 'text_pay_method' ).$this->language->get( 'text_logo' ),
				'terms'      => '',
				'sort_order' => $this->config->get( 'payment_paynow_sort_order' )
			);
		}

		return $method_data;
	}

	public function recurringPayments()
	{
		/*
		 * Used by the checkout to state the module
		 * supports recurring billing.
		 */
		return true;
	}
}