<?php
/**
 * Interact with Google measurement protocol
 * https://developers.google.com/analytics/devguides/collection/protocol/
 *
 * Allows a website to programatically to push info to Google Analytics
 *
 * @package default 
 * @author Dave Barnwell https://github.com/freshsauce
 */
class GAMeasurementProtocol
{
  const URL = 'http://www.google-analytics.com/collect';
  const ANONYMOUS_CID = 555; // Anonymous Client ID.
  private $trackingID = null;
  private $domain     = null;
  
  /**
   * undocumented function
   *
   * @param string $trackingID google Tracking ID / Web property / Property ID.
   * @param string $domainName e.g. freshsauce.co.uk
   * @author Dave Barnwell
   */
  public function __construct($trackingID, $domainName) {
    $this->trackingID = $trackingID;
    $this->domain     = $domainName;
  }
  
  private function commonFields() {
    return array(
      'v'   => 1,
      'tid' => urlencode($this->trackingID),
      'cid' => self::ANONYMOUS_CID,
      'z'   => rand(0, 100000)   // cache buster, but shouldnt be needed as server side
    );
  }


  public function trackTransactionHit($transId, $affiliationName = 'shop name', $amount = 1, $shipping = 0, $tax = 0, $curreny = 'GBP') {
    $fields        = $this->commonFields();
    $fields['t']   = 'transaction'; // Transaction hit type.
    $fields['ti']  = urlencode($transId); // transaction ID. Required.
    $fields['ta']  = urlencode($affiliationName); // Transaction affiliation. (shop name)
    $fields['tr']  = urlencode(sprintf('%.2f', $amount)); // Transaction revenue.
    $fields['ts']  = urlencode(sprintf('%.2f', $shipping)); // Transaction shipping.
    $fields['tt']  = urlencode(sprintf('%.2f', $tax)); // Transaction tax.
    $fields['cu']  = urlencode($curreny); // ISO Currency code.
    return self::sendData($fields);
  }
  
  /**
   * Call trackTransactionHit() then this function trackItemHits() with detail items
   *
   * @param string $transId 
   * @param string $items 
   * @return bool
   */
  public function trackItemHits($transId, $items) {
    if ($items && is_array($items)) {
      foreach ($items as $item) {
        $fields        = $this->commonFields();
        $fields['t']   = 'item'; // purchase on page.
        $fields['ti']  = urlencode($transId); // transaction ID. Required.
        $fields['in']  = $item['name']; // product name.
        $fields['ip']  = urlencode(sprintf('%.2f', $item['price'])); // product price
        $fields['iq']  = $item['quantity']; // product quantity
        $fields['ic']  = $item['sku']; // product code /SKU
        $fields['iv']  = $item['variant']; // product variant/category
        $fields['cu']  = $item['curreny']; // product position.
        if (!self::sendData($fields)) {
          return false;
        }
      }
    }
    return true;
  }
  
  
  public function trackPageView($pageUrl, $pageTitle = '') {
    $fields        = $this->commonFields();
    $fields['t']   = 'pageview'; // Pageview hit type.
    $fields['dh']  = urlencode($this->domain); // Document hostname.
    $fields['dp']  = urlencode($pageUrl); // Page.
    $fields['dt']  = urlencode($pageTitle); // Title.
    return self::sendData($fields);
  }
  
  public function trackEvent($category, $action, $label = '', $value = 0) {
    $fields        = $this->commonFields();
    $fields['t']   = 'event'; // Event hit type
    $fields['ec']  = urlencode($category); // Event Category. Required.
    $fields['ea']  = urlencode($action); // Event Action. Required.
    $fields['el']  = urlencode($label); // Event label.
    $fields['ev']  = urlencode($value); // Event value.
    return self::sendData($fields);
  }
  
  public function trackException($description, $fatal) {
    $fields        = $this->commonFields();
    $fields['t']   = 'exception'; // Exception hit type
    $fields['exd'] = urlencode($description); // Exception description.
    $fields['exf'] = urlencode($fatal ? 1 : 0); // Exception is fatal?
    return self::sendData($fields);
  }
  
  private static function sendData($fields) {
    //url-ify the data for the POST
    $fields_string = '';
    foreach ($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, self::URL);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result !== false;
  }
}
