<?php
class Bitmex
{
    private $key    = ""; /* api key */
    private $sec    = ""; /* api secret */
    protected $url  = "https://testnet.bitmex.com/api/v1/order";
    protected $verb = "POST";
    protected $path = "/api/v1/order";

    public function __construct()
    {
        $this->placeOrder();
    }

    protected function expires()
    {
        return str_replace(".", "", microtime(true)); /* expires  */
    }
    private function placeOrder()
    {
        $exp  = $this->expires();
        $data = array(
            "symbol"   => "XBTUSD",
            "side"     => "Buy",
            "price"    => 7900,
            "ordType"  => "Limit",
            "orderQty" => 36, /* quantity */
            "currency" => "USD",
        );

        $query     = http_build_query($data);
        $signature = hash_hmac("sha256", $this->verb . $this->path . $exp . $query, $this->sec);

        $hdr   = array();
        $hdr[] = "api-signature: $signature";
        $hdr[] = "api-key: $this->key";
        $hdr[] = "api-expires: $exp";
        $hdr[] = "api-nonce: $exp";
        $hdr[] = 'Accept: application/json';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->url,
            CURLOPT_POST           => true,
            CURLOPT_CUSTOMREQUEST  => $this->verb,
            CURLOPT_HTTPHEADER     => $hdr,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $query,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $res = curl_exec($curl);
        curl_close($curl);

    }
}
